<?php

namespace App\Http\Controllers;


use App\Models\SupplierReturn;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SupplierReturnProduct;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Receiving;
use App\Models\ReceivingProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class SupplierReturnController extends Controller
{

    protected $sReturns;

    public function __construct() {

        $this->sReturns = new SupplierReturn();
        $this->middleware('permission:Manage Supplier Returns', ['only' => ['index','show','create','store','edit','update','destroy','getInComplete']]);
       
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $data['receiving'] = SupplierReturn::orderBy('date','DESC')->get();

        activity('View')->log('List of Supplier Return');

        return view('return.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['suppliers'] = Supplier::get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        $data['stores'] = Store::orderBy('name','ASC')->get();

        return view('return.create',$data);
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
       

        return view('return.add-product',$data);
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


        $receiving = $this->sReturns->store($request);

        activity('Create')->log('New [ <b>' . $receiving->invoice_no. ' </b> ] Supplier Return is created');
        return redirect()->route('supplier-returns.index')->with('message', 'Supplier Return Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierReturn $supplierReturn)
    {
        $data['receiving'] = $supplierReturn;
        
        activity('View')->log('<b>' .  $data['receiving']->invoice_no. ' </b> Supplier Return detail.');
        return view('return.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierReturn $supplierReturn)
    {
        $data['receiving'] = $supplierReturn;
        $data['stores'] = Store::orderBy('name','ASC')->get();
         $data['suppliers'] = Supplier::get();

        $lPrice = array();

        foreach($data['receiving']->products as $p){

            $result = ReceivingProduct::where([['product_variant_id',$p->product_variant_id],['receiving_id','!=',$data['receiving']->id]])->orderBy('id','DESC')->first();
            if($result)
                $lPrice[$p->product_variant_id] = $result->trade_price;
            else
                $lPrice[$p->product_variant_id] = 0;
        }

        $data['lPrice'] = $lPrice;

        return view('return.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierReturn $supplierReturn)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'store_id' => ['required'],
            'date' => ['required'],
            'net_amount' => ['required','numeric','gt:0'],
            'total_products' => ['required','numeric','gt:0'],
            'total_qty' => ['required','numeric','gt:0'],
        ]);
        
      
        
        $receiving = $supplierReturn;


        $result = $this->sReturns->updateReceiving($request,$receiving);

        activity('Update')->log('New [ <b>' . $result->invoice_no. ' </b> ] Supplier Return is updated.');

        return redirect()->route('supplier-returns.show',$result->id)->with('message', 'Supplier Return Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Receiving  $receiving
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierReturn $supplierReturn)
    {
        $supplierReturn->delete();
        
         activity('Update')->log('[ <b>' . $supplierReturn->cargo_no. ' </b> ] Return delete');
        return redirect()->back()->with('message', 'Supplier Return deleted Successfully!');
    }

    

    public function changeSupplierReturnStatus(Request $request) {

        if($request->status && $request->receiving_id) {

            $result = $this->sReturns->changeReceivingStatus($request);
            return $result;
        }
        else
            return ['status'=>false];
    }

    
    public function getInComplete() {
        
        $data['receiving'] = SupplierReturn::where('status',Receiving::APPROVAL_PENDING)->orderBy('date','DESC')->get();

        activity('View')->log('List of In Complete Receiving');

        return view('return.incomplete',$data);
        
    }
    
    public function getDirectSupplierReturnForm() {

       // $data['suppliers'] = Supplier::where('status',true)->get();
        $data['stores'] = Store::where('status',true)->get();

        return view('return.direct-receiving-form',$data);
    }

    public function submitDirectSupplierReturnForm(Request $request)
    {
        $request->validate([
            'cargo_no' => ['required'],
            'date' => ['required']
        ]);

        $receiving = $this->sReturns->directReceiving($request);

        activity('Create')->log('Direct Receiving of  [ <b>' . $request->cargo_no. ' </b> ] Start.');
        return view('return.direct-receiving-product-addition',compact('receiving'))->with('message', 'Start for adding products!');
    }

    public function directSupplierReturnProductSearch(Request $request) {

        $val = $request->get('val');

    
        $query = Product::with('brand','variants','thumbnail')->where('status',true);
            $query = $query->where('title','Like','%'.$val.'%');

            $data['products'] = $query->select('id', 'title', 'price', 'brand_id', 'is_new','discount_amount','discount_status','have_variants','available_stock')->limit(50)->orderBy('id','DESC')->get();
            
          

       return view('return.update-products-div',$data);
    }
    
    
     public function directSupplierReturnProductScan(Request $request) {
         
        // dd(4);
         
         $vId = $request->vId;
         if($request->vId == 0){
             
            $data['product'] = Product::where('id',$request->proId)->with('variants')->first();}
            else {
            $data['product']= Product::where('id',$request->proId)->whereHas('variants',function($query) use($vId){
                $query->where('id',$vId);})->with('variants')->first();
            }
            
            
       

        if($data['product']) {
           
            return view('return.product-info', $data);
        }
        else
            return ['success' => false,'message' => 'Product not found..'];
    }
    
    public function directSupplierReturnProductSubmit(Request $request)
    {
        $request->validate([
            'receiving_id' => ['required'],
            'scan_qty' => ['required']
        ]);

        $receiving = $this->sReturns->directReceivingProductSubmit($request);

        return view('return.direct-receiving-product-addition',compact('receiving'))->with('message', 'Start for adding products!');
    }


}
