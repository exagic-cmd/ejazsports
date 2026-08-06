<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderProduct;
use App\Models\Receiving;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ReceivingProduct;
use App\Models\Store;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReceivingController extends Controller
{

    protected $receiving;

    public function __construct() {

        $this->receiving = new Receiving();
        $this->middleware('permission:List Receiving', ['only' => ['index']]);
        $this->middleware('permission:View Receiving', ['only' => ['show']]);
        $this->middleware('permission:Create Receiving', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Receiving', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Receiving', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $data['receiving'] = Receiving::orderBy('date','DESC')->get();

        activity('View')->log('List of Receiving');

        return view('purchase-order/receiving.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['purchaseOrders'] = PurchaseOrder::where('status',PurchaseOrder::PO_SENT)->get();
        $data['suppliers'] = Supplier::get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        $data['stores'] = Store::orderBy('name','ASC')->get();
        
        $data['receiving']  = 0;

        return view('purchase-order/receiving.create',$data);
    }

    public function addNewProduct(Request $request) {
        
         $vId = $request->vId;
         if($request->vId == 0){
             
            $data['product'] = Product::where('id',$request->proId)->with('variants')->first();}
            else { 
            $data['product']= Product::where('id',$request->proId)->with([ "variants" => function($q) use($vId ){
                $q->where('id', $vId);}])->first();
            }

        // $data['products'] = Product::where('id',$request->proId)->with('variants')->orderBy('title')->get();
         $data['randomNumber'] = rand(100000,999999);
        
     //   dd($data);
       

        return view('purchase-order/receiving.add-product',$data);
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
            'store_id' => ['required'],
            'date' => ['required'],
            'cargo_no' => ['required']
        ]);


        $receiving = $this->receiving->store($request);

        activity('Create')->log('New [ <b>' . $receiving->invoice_no. ' </b> ] Receiving is created');
        return redirect()->route('receiving.index')->with('message', 'Receiving Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function show(Receiving $receiving)
    {
        
        activity('View')->log('<b>' . $receiving->invoice_no. ' </b> Receiving detail.');
        return view('purchase-order/receiving.show',compact('receiving'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function edit(Receiving $receiving)
    {
        $data['receiving'] = $receiving;
        $data['purchaseOrders'] = PurchaseOrder::whereIn('status',[PurchaseOrder::PO_SENT,PurchaseOrder::RECEIVED])->get();
        $data['stores'] = Store::orderBy('name','ASC')->get();
         $data['suppliers'] = Supplier::get();

        $lPrice = array();

        foreach($data['receiving']->products as $p){

            $result = ReceivingProduct::where([['product_id',$p->product_id],['product_variant_id',$p->product_variant_id],['receiving_id','!=',$receiving->id]])->orderBy('id','DESC')->first();
            if($result)
                $lPrice[$p->id] = $result->trade_price;
            else
                $lPrice[$p->id] = 0;
        }

        $data['lPrice'] = $lPrice;

        return view('purchase-order/receiving.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receiving $receiving)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'store_id' => ['required'],
            'date' => ['required'],
            'net_amount' => ['required','numeric','gt:0'],
            'total_products' => ['required','numeric','gt:0'],
            'total_qty' => ['required','numeric','gt:0'],
        ]);


        $result = $this->receiving->updateReceiving($request,$receiving);

        activity('Update')->log('New [ <b>' . $result->invoice_no. ' </b> ] Receiving is updated.');

        return redirect()->route('receiving.show',$result->id)->with('message', 'Receiving Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receiving $receiving)
    {
        
        if($receiving->status != 1) {
        //delete inventory
        foreach($receiving->products as $pro) {
            
            if($pro->product_variant_id) {
                $p = Product::where('id',$pro->product_id)->first();
                $p->available_stock = $p->available_stock - $pro->qty;
                $p->save();
                
                $v = ProductVariant::where('id',$pro->product_variant_id)->first();
                $v->available_stock = $p->available_stock - $pro->qty;
                $v->save();
            }
            else {
                $p = Product::where('id',$pro->product_id)->first();
                $p->available_stock = $p->available_stock - $pro->qty;
                $p->save();
            }
        }
        
        }
        
        ReceivingProduct::where('receiving_id',$receiving->id)->delete();
        $receiving->delete();
        
         activity('Update')->log('[ <b>' . $receiving->cargo_no. ' </b> ] Receiving delete');
        return redirect()->back()->with('message', 'Receiving deleted Successfully!');
    }

    /**
     * Get the brand details of specific supplier.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPurchaseOrderDetail(Request $request) {

        $data['purchaseOrder'] = PurchaseOrder::find($request->po_id);

        return view('purchase-order/receiving.update-po-detail',$data);
    }

    /**
     * Get the Product details of specific supplier.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getPurhcaseOrderProductDetail(Request $request) {

        $data['purchaseOrder'] = PurchaseOrder::find($request->po_id);

        $lPrice = array();

        foreach($data['purchaseOrder']->products as $p){

            $result = ReceivingProduct::where('product_variant_id',$p->product_variant_id)->orderBy('id','DESC')->first();
            if($result)
                $lPrice[$p->product_variant_id] = $result->trade_price;
            else
                $lPrice[$p->product_variant_id] = 0;
        }

        $data['lPrice'] = $lPrice;

        return view('purchase-order/receiving.update-po-product-detail',$data);
    }

    public function changeReceivingStatus(Request $request) {

        if($request->status && $request->receiving_id) {

            $result = $this->receiving->changeReceivingStatus($request);
            return $result;
        }
        else
            return ['status'=>false];
    }

    public function getGRN($id) {

        $receiving = Receiving::find($id);

        $orderQuantity = array();

        foreach($receiving->products as $p) {
            $result = PurchaseOrderProduct::where([['purchase_order_id',$receiving->po_id],['product_variant_id',$p->product_variant_id]])->first();

            $orderQuantity[$p->product_variant_id] = $result ? $result->quantity : 0;
        }

        activity('View')->log('<b>' . $receiving->invoice_no. ' </b> GRN detail.');
        return view('purchase-order/receiving.grn',compact('receiving','orderQuantity'));
    }
    
    public function getInCompleteReceivings() {
        
        $data['receiving'] = Receiving::where('status',Receiving::APPROVAL_PENDING)->orderBy('date','DESC')->get();

        activity('View')->log('List of In Complete Receiving');

        return view('purchase-order/receiving.incomplete',$data);
        
    }
    
    public function getDirectReceivingForm() {

       // $data['suppliers'] = Supplier::where('status',true)->get();
        $data['stores'] = Store::where('status',true)->get();

        return view('purchase-order/receiving.direct-receiving-form',$data);
    }

    public function submitDirectReceivingForm(Request $request)
    {
        $request->validate([
            'cargo_no' => ['required'],
            'date' => ['required']
        ]);

        $receiving = $this->receiving->directReceiving($request);

        activity('Create')->log('Direct Receiving of  [ <b>' . $request->cargo_no. ' </b> ] Start.');
        return view('purchase-order/receiving.direct-receiving-product-addition',compact('receiving'))->with('message', 'Start for adding products!');
    }

    public function directReceivingProductSearch(Request $request) {

        $val = $request->get('val');

    
        $query = Product::with('brand','variants','thumbnail')->where('status',true);
            $query = $query->where('title','Like','%'.$val.'%');

            $data['products'] = $query->select('id', 'title', 'price', 'brand_id', 'is_new','discount_amount','discount_status','have_variants','available_stock')->limit(50)->orderBy('id','DESC')->get();
            
          

       return view('purchase-order/receiving.update-products-div',$data);
    }
    
    
     public function directReceivingProductScan(Request $request) {
         
        // dd(4);
         
         $vId = $request->vId;
         if($request->vId == 0){
             
            $data['product'] = Product::where('id',$request->proId)->with('variants')->first();}
            else {
            $data['product']= Product::where('id',$request->proId)->whereHas('variants',function($query) use($vId){
                $query->where('id',$vId);})->with('variants')->first();
            }
            
            
       

        if($data['product']) {
           
            return view('purchase-order/receiving.product-info', $data);
        }
        else
            return ['success' => false,'message' => 'Product not found..'];
    }
    
    public function directReceivingProductSubmit(Request $request)
    {
        $request->validate([
            'receiving_id' => ['required'],
            'scan_qty' => ['required']
        ]);

        $receiving = $this->receiving->directReceivingProductSubmit($request);

        return view('purchase-order/receiving.direct-receiving-product-addition',compact('receiving'))->with('message', 'Start for adding products!');
    }
    
    
    public function getInCompleteReceivingsEdit($id) {
        
       
        $data['suppliers'] = Supplier::get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        $data['stores'] = Store::orderBy('name','ASC')->get();
        
        $data['receiving'] = Receiving::where('id',$id)->first();

        return view('purchase-order/receiving.incomplete-edit',$data);
    }


}
