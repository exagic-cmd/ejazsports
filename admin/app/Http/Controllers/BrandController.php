<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Brand;
use phpDocumentor\Reflection\Types\Boolean;

class BrandController extends Controller
{
    protected $brand;

    public function __construct()
    {
        $this->brand = new Brand();
        $this->middleware('permission:List Brand', ['only' => ['index']]);
        $this->middleware('permission:View Brand', ['only' => ['show']]);
        $this->middleware('permission:Create Brand', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Brand', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Brand', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['brands'] = Brand::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Brands');
        return view('catalog/brand.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['brands'] = Brand::select('title','serial_no')->orderBy('serial_no','ASC')->get();
        $data['discounts'] = Discount::where([['type',Discount::BRAND],['status',true]])->get();

        return view('catalog/brand.create',$data);
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
            'title' => ['required', 'string', 'max:255','unique:brands'],
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
            'image' => ['nullable','image','mimes:jpeg,jpg,png,svg', 'max:500'],
           
        ]);


        $brand = $this->brand->store($request);

        activity('Create')->log('New [ <b>' . $brand->title. ' </b> ] Brand is created');
        return redirect()->route('brands.index')->with('message', 'Brand Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        $brandProducts = Product::where('brand_id',$brand->id)->paginate(30);

        $totalProducts = Product::where('brand_id',$brand->id)->count();
        $activeProducts = Product::where([['brand_id',$brand->id],['status',true]])->count();
        $inActiveProducts = Product::where([['brand_id',$brand->id],['status',false]])->count();

        activity('View')->log(' Show the detail of [ <b>' . $brand->title. ' </b> ] Brand');
        return view('catalog/brand.show',compact('brand','brandProducts','totalProducts','activeProducts','inActiveProducts'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        $brands = Brand::where('id','!=',$brand->id)->select('title','serial_no')->orderBy('serial_no','ASC')->get();
        $discounts = Discount::where([['type',Discount::BRAND],['status',true]])->get();

        return view('catalog/brand.edit',compact('brand','brands','discounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Brand $brand)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255','unique:brands,title,'.$brand->id],
       
            'status'=> ['required','boolean'],
            'serial_no' => ['nullable','integer'],
            'image' => ['nullable','image','mimes:jpeg,jpg,png,svg', 'max:100'],
            
        ]);

        $brand = $this->brand->updateBrand($request,$brand);

        activity('Update')->log('[ <b>' . $brand->title. ' </b> ] Brand is updated');
        return redirect()->route('brands.index')->with('message', 'Brand Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        activity('Delete')->log('<b> ' . $brand->title. '</b>  Brand is deleted');
        return redirect()->route('brands.index')
            ->with('message','Brand deleted successfully');
    }

    /**
     * Search brand products by title
     *
     * @param string $value
     * @return \Illuminate\Http\Response
     */
    public function searchBrandProduct(Request $request) {

        if($request->value)
            $brandProducts = Product::where([['brand_id',$request->brand_id],['title','LIKE','%'.$request->value.'%']])->get();
        else
            $brandProducts = Product::where([['brand_id',$request->brand_id]])->get();

        return view('catalog/brand.search-brand-product',compact('brandProducts'));
    }
}
