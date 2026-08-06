<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\StockAudit;
use App\Models\StockAuditDetail;
use App\Models\Store;
use App\Models\StoreProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockAuditController extends Controller
{

    protected $stockAudit;

    public function __construct() {

        $this->stockAudit = new StockAudit();
        $this->middleware('permission:List Stock Audit', ['only' => ['index','show']]);
        $this->middleware('permission:Create Stock Audit', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Stock Audit', ['only' => ['edit','update']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['stockAudits'] = StockAudit::orderBy('id','DESC')->get();

        return view('stock-audit.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['stores'] = Store::where('status',true)->get();
        $data['brands'] = Brand::orderBy('title','ASC')->get();

        return view('stock-audit.create',$data);
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
            'brand_id' => ['required']
            ]);

        $stockAudit = $this->stockAudit->store($request);

        activity('Create')->log('Stock Audit of brand  [ <b>' . $stockAudit->brand->title. ' </b> ] Start.');
        return view('stock-audit.audit',compact('stockAudit'))->with('message', 'Start for adding products!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StockAudit  $stockAudit
     * @return \Illuminate\Http\Response
     */
    public function show(StockAudit $stockAudit)
    {

        $variantIds = StockAuditDetail::where('stock_audit_id',$stockAudit->id)->pluck('variant_id')->toArray();

        $brandId = $stockAudit->brand->id;

        $variants = ProductVariant::whereNotIn('id',$variantIds)->whereHas('product',function($query) use($brandId){
            $query->where('brand_id',$brandId);
        })->where('available_stock','>',0)->get();

        $stock = array();
        foreach($variants as $v) {
            $totalPurchase = StoreProductStock::where([['store_id',$stockAudit->store_id],['variant_id',$v->id]])->sum('purchase_qty');
            $totalSold = StoreProductStock::where([['store_id',$stockAudit->store_id],['variant_id',$v->id]])->sum('sold_qty');
            $stock[$v->id] = $totalPurchase - $totalSold;
        }

        activity('View')->log('Stock Audit of brand  [ <b>' . $stockAudit->brand->title. ' </b> ] date : '. $stockAudit->date .' Detail.');
        return view('stock-audit.show',compact('stockAudit','variants','stock'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StockAudit  $stockAudit
     * @return \Illuminate\Http\Response
     */
    public function edit(StockAudit $stockAudit)
    {
        return view('stock-audit.audit',compact('stockAudit'))->with('message', 'Start for adding products!');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StockAudit  $stockAudit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StockAudit $stockAudit)
    {
        $request->validate([
            'audit_id' => ['required'],
            'product_id' => ['required']
        ]);

        $stockAudit = $this->stockAudit->updateAudit($request,$stockAudit);

        return view('stock-audit.audit',compact('stockAudit'))->with('message', 'Start for adding products!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StockAudit  $stockAudit
     * @return \Illuminate\Http\Response
     */
    public function destroy(StockAudit $stockAudit)
    {
        $stockAudit->delete();

        activity('Delete')->log('Stock Audit of brand  [ <b>' . $stockAudit->brand->title. ' </b> ] date : '. $stockAudit->date .' Deleted.');
        return redirect()->route('stock-audits.index')->with('message', 'Delete Successfully!');

    }

    public function getScanProductDetail(Request $request) {

        // $productVariant = ProductVariant::where('barcode','LIKE','%'.$request->barcode.'%')->with('product')->first();
        
        if($request->barcode) {
        
            $product = Product::where('title','LIKE','%'.$request->barcode.'%')->orwhere('barcode',$request->barcode)->first();
            $variant = false;
        }
       

        if($product) {
            
                // $totalPurchase = StoreProductStock::where([['store_id',$request->store_id],['product_id',$product->id]])->sum('purchase_qty');
                // $totalSold = StoreProductStock::where([['store_id',$request->store_id],['product_id',$product->id]])->sum('sold_qty');
                    $availableQty = $product->available_stock;
              
                

                return view('stock-audit.product-info', compact('product','availableQty','variant'));
        }
        else
            return ['success' => false,'message' => 'Product not found..'];
    }
    
    public function getScanVariantDetail(Request $request) {

        // $productVariant = ProductVariant::where('barcode','LIKE','%'.$request->barcode.'%')->with('product')->first();
        
        
            $product = ProductVariant::where('barcode','LIKE','%'.$request->barcode1.'%')->first();
            $variant = true;
        

        if($product) {
            
                // $totalPurchase = StoreProductStock::where([['store_id',$request->store_id],['product_id',$product->id]])->sum('purchase_qty');
                // $totalSold = StoreProductStock::where([['store_id',$request->store_id],['product_id',$product->id]])->sum('sold_qty');
                    $availableQty = $product->available_stock;
              
                

                return view('stock-audit.product-info', compact('product','availableQty','variant'));
        }
        else
            return ['success' => false,'message' => 'Product not found..'];
    }

    public function approveAudit(Request $request) {

        $stockAudit = StockAudit::find($request->id);
        $stockAudit->status = StockAudit::APPROVED;
        $stockAudit->approve_by = Auth::user()->id;
        $stockAudit->save();

        return redirect()->back()->with('message', 'Audit Approve Successfully!');
    }
}
