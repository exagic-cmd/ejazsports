<?php

namespace App\Http\Controllers;


use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use phpDocumentor\Reflection\Types\Boolean;

class ExpenseCategoryController extends Controller
{
   protected $category;

    public function __construct()
    {
        $this->category = new ExpenseCategory();
        $this->middleware('permission:List Expense', ['only' => ['index']]);
        $this->middleware('permission:View Expense', ['only' => ['show']]);
        $this->middleware('permission:Create Expense', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Expense', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Expense', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['categories'] = ExpenseCategory::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Expense Categories');
        return view('expense/category.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    
        return view('expense/category.create');
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
            'name' => ['required']
        ]);

        $category = $this->category->store($request);

        activity('Create')->log('New [ <b>' . $category->name. ' </b> ] Category is created');
        return redirect()->route('expense-category.index')->with('message', 'Expense Created Successfully!');
    }

   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ExpenseCategory $expenseCategory)
    {

        return view('expense/category.edit',compact('expenseCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name' => ['required']
        ]);

        $category = $this->category->updateCategory($request,$expenseCategory);

        activity('Update')->log('[ <b>' . $category->detail. ' </b> ] Category is updated');
        return redirect()->route('expense-category.index')->with('message', 'Category Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        activity('Delete')->log('<b> ' . $expenseCategory->name. '</b>  Category is deleted');
        return redirect()->route('expense-category.index')
            ->with('message','Category deleted successfully');
    }


}
