<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    protected $coupon;

    public function __construct()
    {
        $this->coupon = new Coupon();
        $this->middleware('permission:List Coupons', ['only' => ['index']]);
        $this->middleware('permission:View Coupons', ['only' => ['show']]);
        $this->middleware('permission:Create Coupons', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Coupons', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Coupons', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['coupons'] = Coupon::orderBy('updated_at','DESC')->get();

        activity('View')->log('List of Coupons');
        return view('coupon.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


        return view('coupon.create');
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
            'name' => ['required', 'string', 'max:255','unique:coupons'],
            'usage' => ['required'],
            'type' => ['required'],
            'discount_amount' => ['required'],
            'start_date' => ['date'],
            'expiry_date' => ['date','after_or_equal:start_date']
        ]);


        $coupon = $this->coupon->store($request);

        activity('Create')->log('New [ <b>' . $coupon->name. ' </b> ] Coupon is created');
        return redirect()->route('coupons.index')->with('message', 'Coupon Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        activity('View')->log(' Show the detail of [ <b>' . $coupon->name. ' </b> ] Coupon');
        return view('coupon.show',compact('coupon'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['coupon'] = Coupon::find($id);

        return view('coupon.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:coupons,name,'.$coupon->id],
            'usage' => ['required'],
            'type' => ['required'],
            'discount_amount' => ['required'],
            'start_date' => ['date'],
            'expiry_date' => ['date','after_or_equal:start_date']
        ]);


        $coupon = $this->coupon->updateCoupon($request,$coupon);

        activity('Update')->log('[ <b>' . $coupon->name. ' </b> ] Coupon is updated');
        return redirect()->route('coupons.index')->with('message', 'Coupon info Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon = Coupon::find($id);
        Coupon::find($id)->delete();

        activity('Update')->log('[ <b>' . $coupon->name. ' </b> ] Coupon is deleted.');
        return redirect()->route('coupons.index')->with('message', 'Coupon info Deleted Successfully!');
    }
}
