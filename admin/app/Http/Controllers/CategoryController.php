<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Category;
use phpDocumentor\Reflection\Types\Boolean;

class CategoryController extends Controller
{
    protected $category;

    public function __construct()
    {
        $this->category = new Category();
        $this->middleware('permission:List Category', ['only' => ['index']]);
        $this->middleware('permission:View Category', ['only' => ['show']]);
        $this->middleware('permission:Create Category', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Category', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Category', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['categories'] = Category::orderBy('id','ASC')->get();


        activity('View')->log('List of Categories');
        return view('catalog/category.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['categories'] = Category::select('id','title','serial_no')->orderBy('serial_no','ASC')->get();

        return view('catalog/category.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255','unique:categories'],
         
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
            'image' => ['nullable','image','mimes:jpeg,jpg,png,svg', 'max:500'],
           
        ]);

        $category = $this->category->store($request);

        activity('Create')->log('New [ <b>' . $category->title. ' </b> ] Category is created');
        return redirect()->route('categories.index')->with('message', 'Category Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category_id = $category->id;
        $categoryProducts = Product::whereHas('categories', function($query) use($category_id) {
            $query->where('category_id',$category_id);
        })->paginate(30);


        $totalProducts = Product::whereHas('categories', function($query) use($category_id) {
            $query->where('category_id',$category_id);
        })->count();
        $activeProducts = Product::where('status',true)->whereHas('categories', function($query) use($category_id) {
            $query->where('category_id',$category_id);
        })->count();
        $inActiveProducts = Product::where('status',false)->whereHas('categories', function($query) use($category_id) {
            $query->where('category_id',$category_id);
        })->count();

        activity('View')->log(' Show the detail of [ <b>' . $category->title. ' </b> ] Category');
        return view('catalog/category.show',compact('category','categoryProducts','totalProducts','activeProducts','inActiveProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id','!=',$category->id)->select('id','title','serial_no')->orderBy('serial_no','ASC')->get();
        ///product catoegories
        $selectedParentCategories = array();
        foreach ($category->parentCategory as $r) {
            array_push($selectedParentCategories, $r->parent_category_id);
        }
        $discounts = Discount::where([['type',Discount::CATEGORY],['status',true]])->get();

        return view('catalog/category.edit',compact('category','categories','selectedParentCategories','discounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Category $category)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255','unique:categories,title,'.$category->id],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
            'image' => ['nullable','image','mimes:jpeg,jpg,png,svg', 'max:500'],
           
        ]);

        $category = $this->category->updateCategory($request,$category);

        activity('Update')->log('[ <b>' . $category->title. ' </b> ] Category is updated');
        return redirect()->route('categories.index')->with('message', 'Category Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        activity('Delete')->log('<b> ' . $category->title. '</b>  Category is deleted');
        return redirect()->route('categories.index')
            ->with('message','Category deleted successfully');
    }

    /**
     * Search brand products by title
     *
     * @param string $value
     * @return \Illuminate\Http\Response
     */
    public function searchCategoryProduct(Request $request) {

        $category_id = $request->category_id;

        if($request->value)
            $categoryProducts = Product::whereHas('categories',function($query) use($category_id) {
                $query->where('category_id',$category_id);
            })->where('title','LIKE','%'.$request->value.'%')->get();
        else
            $categoryProducts = Product::whereHas('categories',function($query) use($category_id) {
                $query->where('category_id',$category_id);
            })->get();

        return view('catalog/category.search-category-product',compact('categoryProducts'));
    }
}
