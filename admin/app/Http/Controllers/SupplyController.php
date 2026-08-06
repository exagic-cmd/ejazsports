<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Models\StoreProductStock;
use App\Models\Supply;
use App\Models\SupplyProduct;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;

class SupplyController extends Controller
{

    protected $supplies;

    public function __construct() {

        $this->supplies = new Supply();
        $this->middleware('permission:List Supplies', ['only' => ['index']]);
        $this->middleware('permission:View Supplies', ['only' => ['show']]);
        $this->middleware('permission:Create Supplies', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Supplies', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Supplies', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['storeSupplies'] = Supply::orderBy('send_date','DESC')->get();

        activity('View')->log('List of Store Supplies');
        return view('store/supply.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['stores'] = Store::orderBy('name','ASC')->get();
        $data['today'] = date('Y-m-d');
        $data['brands'] = Brand::orderBy('brand_heading','ASC')->get();

        return view('store/supply.create',$data);
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
            'store_out_id' => ['required'],
            'store_in_id' => ['required'],
            'send_date' => ['required'],
            'type' => ['required'],
            'total_products' => ['required','gt:0'],
            'total_qty' => ['required','gt:0'],
            'brand_id' => ['required']
        ]);


        $supplies = $this->supplies->store($request);

        activity('Create')->log('New [ <b> VEG-00' . $supplies->id. ' </b> ] Supply is created');
        return redirect()->route('supplies.show',$supplies->id)->with('message', 'Supply Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supply  $suply
     * @return \Illuminate\Http\Response
     */
    public function show(Supply $supply)
    {

        activity('View')->log('[ <b> VEG-00' . $supply->id. ' </b> ] Supply detail Page View.');

        return view('store/supply.show',compact('supply'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supply  $supply
     * @return \Illuminate\Http\Response
     */
    public function edit(Supply $supply)
    {
        $data['stores'] = Store::orderBy('name','ASC')->get();
        $data['supply'] = $supply;
        $data['brands'] = Brand::orderBy('brand_heading','ASC')->get();

        $data['selectedProducts'] = SupplyProduct::where('supply_id',$supply->id)->get();

        $variantOutStock = array();
        $variantInStock = array();
        foreach($supply->brand->products as $p) {
            foreach($p->variants as $v) {
                $purchase_qty = StoreProductStock::where([['store_id',$supply->store_out_id],['variant_id',$v->id]])->sum('purchase_qty');
                $sold_qty = StoreProductStock::where([['store_id',$supply->store_out_id],['variant_id',$v->id]])->sum('sold_qty');
                $variantOutStock[$v->id] = $purchase_qty - $sold_qty;
                $purchase_qty = StoreProductStock::where([['store_id',$supply->store_in_id],['variant_id',$v->id]])->sum('purchase_qty');
                $sold_qty = StoreProductStock::where([['store_id',$supply->store_in_id],['variant_id',$v->id]])->sum('sold_qty');
                $variantInStock[$v->id] = $purchase_qty - $sold_qty;
            }
        }

        $data['variantOutStock'] = $variantOutStock;
        $data['variantInStock'] = $variantInStock;

        return view('store/supply.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supply  $supply
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supply $supply)
    {
        $request->validate([
            'store_out_id' => ['required'],
            'store_in_id' => ['required'],
            'send_date' => ['required'],
            'type' => ['required'],
            'total_products' => ['required','gt:0'],
            'total_qty' => ['required','gt:0'],
            'brand_id' => ['required']
        ]);


        $supplies = $this->supplies->updateSupply($request,$supply);

        activity('Update')->log('New [ <b> VEG-00' . $supply->id. ' </b> ] Supply is updated.');
        return redirect()->route('supplies.show',$supply->id)->with('message', 'Supply Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supply  $supply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supply $supply)
    {
        //
    }

    public function getBrandProduct(Request $request) {

        $data['brand'] = Brand::find($request->brand_id);

        $variantOutStock = array();
        $variantInStock = array();
        foreach($data['brand']->products as $p) {
            foreach($p->variants as $v) {
                $purchase_qty = StoreProductStock::where([['store_id',$request->store_out_id],['variant_id',$v->id]])->sum('purchase_qty');
                $sold_qty = StoreProductStock::where([['store_id',$request->store_out_id],['variant_id',$v->id]])->sum('sold_qty');
                $variantOutStock[$v->id] = $purchase_qty - $sold_qty;
                $purchase_qty = StoreProductStock::where([['store_id',$request->store_in_id],['variant_id',$v->id]])->sum('purchase_qty');
                $sold_qty = StoreProductStock::where([['store_id',$request->store_in_id],['variant_id',$v->id]])->sum('sold_qty');
                $variantInStock[$v->id] = $purchase_qty - $sold_qty;
            }
        }

        $data['variantOutStock'] = $variantOutStock;
        $data['variantInStock'] = $variantInStock;

        return view('store/supply.update-brand-product',$data);
    }

    public function changeSupplyStatus(Request $request) {

        if($request->status && $request->supply_id) {

            $result = $this->supplies->changeSupplyStatus($request);
            return $result;
        }
        else
            return ['status'=>false];
    }

    public function supplyReceivingForm($id) {

        $supply = Supply::find($id);
        $data['today'] = date('Y-m-d');

        return view('store/supply.receiving',compact('supply'));
    }

    public function addSupplyReceiving(Request $request) {

        $request->validate([
            'date' => ['required'],
            'total_products' => ['required','numeric','gt:0'],
            'total_qty' => ['required','numeric','gt:0'],
        ]);


        $supply = $this->supplies->addSupplyReceiving($request);

        activity('Create')->log('New Supply [ <b>' . $supply->id. ' </b> ] Receiving is created');

        return redirect()->route('supplies.show',$request->supply_id)->with('message', 'Receiving Created Successfully!');
    }
}
