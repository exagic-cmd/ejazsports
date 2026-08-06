<?php

namespace App\Http\Controllers;


use App\Models\Store;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use phpDocumentor\Reflection\Types\Boolean;

class ExpenseController extends Controller
{
    protected $expense;

    public function __construct()
    {
        $this->expense = new Expense();
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
        $data['expenses'] = Expense::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Expenses');
        return view('expense.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['stores'] = Store::get();
        $data['categories'] = ExpenseCategory::get();

        return view('expense.create',$data);
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
            'amount' => ['required'],
            'category_id' => ['required'],
            'store_id' => ['required'],
            'date' => ['required'],
            'picture' => ['required','image','mimes:jpeg,jpg,png,svg', 'max:500']
        ]);

        $expense = $this->expense->store($request);

        activity('Create')->log('New [ <b>' . $expense->detail. ' </b> ] Expense is created');
        return redirect()->route('expense.index')->with('message', 'Expense Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Expesne $expense)
    {
        activity('View')->log(' Show the detail of [ <b>' . $expense->detail. ' </b> ] Expense');
        return view('expense.show',compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense)
    {
        $stores = Store::get();
        $categories = ExpenseCategory::get();

        return view('expense.edit',compact('expense','stores','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Expense $expense)
    {
        $request->validate([
            'amount' => ['required'],
            'category_id' => ['required'],
            'store_id' => ['required'],
            'date' => ['required'],
            'picture' => ['nullable','image','mimes:jpeg,jpg,png,svg', 'max:500']
        ]);

        $expense = $this->expense->updateExpense($request,$expense);

        activity('Update')->log('[ <b>' . $expense->detail. ' </b> ] Expense is updated');
        return redirect()->route('expense.index')->with('message', 'Expense Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        activity('Delete')->log('<b> ' . $expense->detail. '</b>  Expense is deleted');
        return redirect()->route('expense.index')
            ->with('message','Expense deleted successfully');
    }


}
