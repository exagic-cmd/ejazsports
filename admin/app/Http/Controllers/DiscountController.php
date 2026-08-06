<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Discount;

class DiscountController extends Controller
{
    protected $discount;

    public function __construct()
    {
        $this->discount = new Discount();
        $this->middleware('permission:List Discounts', ['only' => ['index']]);
        $this->middleware('permission:View Discounts', ['only' => ['show']]);
        $this->middleware('permission:Create Discounts', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Discounts', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Discounts', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['discounts'] = Discount::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Discounts');
        return view('discount.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('discount.create');
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
            'name' => ['required', 'string', 'max:255','unique:discounts'],
            'type' => ['required'],
            'amount' => ['required'],
            'start_date' => ['nullable','date'],
            'expiry_date' => ['nullable','date','after_or_equal:start_date']
        ]);


        $discount = $this->discount->store($request);

        activity('Create')->log('New [ <b>' . $discount->name. ' </b> ] Discount is created');
        return redirect()->route('discounts.index')->with('message', 'Discount Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Discount $discount)
    {
        activity('View')->log(' Show the detail of [ <b>' . $discount->name. ' </b> ] Store');
        return view('discount.show',compact('discount'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['discount'] = Discount::find($id);

        return view('discount.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:discounts,name,'.$discount->id],
            'type' => ['required'],
            'amount' => ['required'],
            'start_date' => ['nullable','date'],
            'expiry_date' => ['nullable','date','after_or_equal:start_date']
        ]);


        $discount = $this->discount->updateDiscount($request,$discount);

        activity('Update')->log('[ <b>' . $discount->name. ' </b> ] Discount is updated');
        return redirect()->route('discounts.index')->with('message', 'Discount info Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $discount = Discount::find($id);
        Discount::find($id)->delete();

        activity('Update')->log('[ <b>' . $discount->name. ' </b> ] Discount is deleted.');
        return redirect()->route('discounts.index')->with('message', 'Discount info Deleted Successfully!');
    }
}
