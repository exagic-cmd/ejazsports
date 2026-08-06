<?php

namespace App\Http\Controllers;
use App\Mail\SendPO;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Receiving;
use App\Models\OrderProduct;
use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PDF;
use App\Models\ReceivingProduct;
use App\Models\StoreProductStock;


class PurchaseOrderController extends Controller
{
    protected $purchaseOrder;

    public function __construct() {

        $this->purchaseOrder = new PurchaseOrder();
        $this->middleware('permission:List Purchase Order', ['only' => ['index']]);
        $this->middleware('permission:View Purchase Order', ['only' => ['show']]);
        $this->middleware('permission:Create Purchase Order', ['only' => ['create','store']]);
        $this->middleware('permission:Edit Purchase Order', ['only' => ['edit','update']]);
        $this->middleware('permission:Delete Purchase Order', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['purchaseOrders'] = PurchaseOrder::orderBy('created_at','DESC')->paginate(100);
        activity('View')->log('List of Purchase Order');
        return view('purchase-order.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['products'] = Product::with('variants')->get(); //->whereRaw('available_stock < re_order_level')
        $data['suppliers'] = Supplier::where('status',true)->orderBy('name','ASC')->get();
        $data['brands'] = Brand::orderBy('title','ASC')->get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        $data['stores'] = Store::orderBy('name','ASC')->get();
           
        return view('purchase-order.create',$data);
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
            'supplier_id' => ['required'],
            'store_id' => ['required'],
            'date' => ['required']
        ]);

        $purchase = $this->purchaseOrder->store($request);
        activity('Create')->log('New [ <b>' . $purchase->po_no. ' </b> ] PO is created');
        return redirect()->route('purchase-orders.index')->with('message', 'Purchase Order Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        activity('View')->log('<b>' . $purchaseOrder->po_no. ' </b> PO detail.');
        return view('purchase-order.show',compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $data['purchaseOrder'] = $purchaseOrder;
        $data['suppliers'] = Supplier::orderBy('name','ASC')->get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        $data['stores'] = Store::orderBy('name','ASC')->get();

        return view('purchase-order.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, PurchaseOrder $purchaseOrder)
    // {
    //     $request->validate([
    //         'supplier_id' => ['required'],
    //         'store_id' => ['required'],
    //         'date' => ['required']
    //     ]);


    //     $purchase = $this->purchaseOrder->updatePurchase($request,$purchaseOrder);

    //     activity('Edit')->log('[ <b>' . $purchase->po_no. ' </b> ] PO is updated.');

    //     return redirect()->route('purchase-orders.show',$purchase->id)->with(['status'=>'success','message'=>'Purchase Order updated successfully..']);
    // }
            // updated section 
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier_id' => ['required'],
            'store_id' => ['required'],
            'date' => ['required'],
        ]);

        // update directly on the model
        $purchaseOrder->update($request->all());
        activity('Edit')->log('[ <b>' . $purchaseOrder->po_no . ' </b> ] PO is updated.');
        return redirect()
            ->route('purchase-orders.index')
            ->with(['status' => 'success', 'message' => 'Purchase Order updated successfully..']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();

        activity('Delete')->log('<b> ' . $purchaseOrder->po_no. '</b>  PO is deleted');

        return redirect()->route('purchase-orders.index')->with('message', 'Purchase Order deleted Successfully!');
    }

    // public function changePOStatus(Request $request) {
            
    //             if($request->status && $request->po_id) {
    //                 if($request->status == PurchaseOrder::PO_SENT)
    //                 {
    //                     $purchaseOrder = PurchaseOrder::find($request->po_id);
    //                     if ($purchaseOrder->supplier->email) {
    //                         $data['purchaseOrder'] = $purchaseOrder;

    //                         $pdf = PDF::loadView('purchase-order.pdf-po', $data);
    //                         $path = public_path('storage/po/');
    //                         $fileName = $purchaseOrder->po_no.'.pdf';
    //                         $pdf->save($path . $fileName);

    //                         $file = $path . $fileName;

    //                         Mail::send('emails.send-po', $data, function($message)use($purchaseOrder, $file) {
    //                             $message->to($purchaseOrder->supplier->email)
    //                                 ->subject('PO from Vegas.pk')
    //                                 ->cc('nadeemakhter821@gmail.com');
    //                             $message->attach($file);
    //                         });

    //                     }
    //                 }
    //                 PurchaseOrder::where('id', $request->po_id)->update(['status' => $request->status,'approved_by' => Auth::user()->id]);
    //                 return ['status'=>true];
    //             }
    //             else
    //                 return ['status'=>false];
    // }

    // updated function of adding qty after po send
    public function changePOStatus(Request $request)
    {
        if (!$request->status || !$request->po_id) {
            return ['status' => false];
        }
        $purchaseOrder = PurchaseOrder::with('products', 'supplier')->find($request->po_id);
        if (!$purchaseOrder) {
            return ['status' => false, 'message' => 'PO not found'];
        }
        // If PO is sent
        if ($request->status == PurchaseOrder::PO_SENT) {
            // Create Receiving entry
            $receiving = Receiving::create([
                'po_id'         => $purchaseOrder->id,
                'total_products'=> $purchaseOrder->products->count(),
                'total_qty'     => $purchaseOrder->products->sum('quantity'),
                'date'          => now(),
                'created_by'    => Auth::id(),
                'supplier_id'   => $purchaseOrder->supplier_id ?? null,
                'status'        => 1, // optional
            ]);
            // Save each product into ReceivingProduct
            foreach ($purchaseOrder->products as $detail) {
                ReceivingProduct::create([
                    'receiving_id'       => $receiving->id,
                    'product_id'         => $detail->product_id,
                    'product_variant_id' => $detail->product_variant_id ?? null,
                    'qty'                => $detail->quantity,
                    'po_product'         => $detail->id, // assuming it's the pivot id or po_product_id
                ]);
            }
            // Send PO email if supplier email exists
            if ($purchaseOrder->supplier && $purchaseOrder->supplier->email) {
                $data['purchaseOrder'] = $purchaseOrder;
                $pdf = PDF::loadView('purchase-order.pdf-po', $data);
                $path = public_path('storage/po/');
                if (!file_exists($path)) mkdir($path, 0755, true);
                $fileName = $purchaseOrder->po_no . '.pdf';
                $pdf->save($path . $fileName);
                $file = $path . $fileName;
                Mail::send('emails.send-po', $data, function ($message) use ($purchaseOrder, $file) {
                    $message->to($purchaseOrder->supplier->email)
                            ->subject('PO from Vegas.pk')
                            ->cc('nadeemakhter821@gmail.com');
                    $message->attach($file);
                });
            }
        }
        // Update PO status and approved_by
        $purchaseOrder->update([
            'status' => $request->status,
            'approved_by' => Auth::id(),
        ]);
        return ['status' => true];
    }


    public function getAutoBrandFilterPurchaseOrder()
     {
        $data['brands'] = Brand::where('status',true)->get();
		
        return view('purchase-order.auto-brand-filter',$data);
    }
  
    public function getAutoBrandsPurchaseOrder(Request $request) {
        
        if($request->brand_id)
            $allBrands = Brand::where('id',$request->brand_id)->paginate(500);
        else {
            $allBrands = Brand::where('status',true)->orderBy('serial_no','ASC')->paginate(50);
        }
        $outOfStockPer = array();
        $averageSale = array();
        foreach($allBrands as $brand) {
            $allProducts = Product::where('brand_id',$brand->id)->count();
            $outOfStockProducts = Product::where([['brand_id',$brand->id],['available_stock','<',1]])->count();
            $outOfStockPer[$brand->id] = round(($outOfStockProducts / ($allProducts == 0 ? 1 : $allProducts)) * 100,2);
            $brandId = $brand->id;
            $totalSale = OrderProduct::whereDate('created_at','>',Carbon::now()->subMonth(1))->whereHas('product',function($query) use($brandId){
                $query->where('brand_id',$brandId);
            })->selectRaw('sum(qty) as sale_price')->get();
            $averageSale[$brand->id] = round($totalSale[0]->sale_price / 30,2);
        }
        $data['outOfStockPer'] = $outOfStockPer;
        $data['averageSale'] = $averageSale;
        $data['brands'] = $allBrands;
	
        return view('purchase-order.auto-brands',$data);
    }

    public function getAutoBrandPurchaseOrderForm($id) {
      
        $data['products'] = Product::with('variants')->where('brand_id',$id)->get();
		
        $average = array();
        $totalSold = array();
        $lastPurchasePrice = array();
        $data['stores'] = Store::where('status',true)->get();
        $storeVariants = array();
        foreach($data['products'] as $pro) {
            foreach ($pro->variants as $v) {

                if($pro->have_variants) {
                    $totalSale = OrderProduct::whereHas('order',function($query){
                        $query->whereNotIn('status',[Order::RETURNED,Order::CANCELED,Order::PARTIALLY_RETURNED]);
                    })->where('variant_id', $v->id)->selectRaw('sum(qty) as sale_price')->get();
                    $totalSold[$v->id] = OrderProduct::whereHas('order',function($query){
                        $query->whereNotIn('status',[Order::RETURNED,Order::CANCELED,Order::PARTIALLY_RETURNED]);
                    })->where('variant_id', $v->id)->sum('qty');
                    $firstOrder = OrderProduct::with('order')->where('variant_id', $v->id)->orderBy('id','ASC')->first();
                }
                else {
                    $totalSale = OrderProduct::where('product_id', $pro->id)->whereHas('order',function($query){
                        $query->whereNotIn('status',[Order::RETURNED,Order::CANCELED,Order::PARTIALLY_RETURNED]);
                    })->selectRaw('sum(qty) as sale_price')->get();
                    $totalSold[$v->id] = OrderProduct::where('product_id', $pro->id)->whereHas('order',function($query){
                        $query->whereNotIn('status',[Order::RETURNED,Order::CANCELED,Order::PARTIALLY_RETURNED]);
                    })->sum('qty');
                    $firstOrder = OrderProduct::with('order')->where('product_id', $pro->id)->orderBy('id','ASC')->first();
                }
                $today = Carbon::now();
                if($firstOrder)
                    $firstOrder = Carbon::parse($firstOrder->created_at);
                else
                    $firstOrder = Carbon::now()->subMonth(3);
                $diff = $firstOrder->diffInDays($today);
                $average[$v->id] = round($totalSale[0]->sale_price / $diff, 4);
                if($v->available_stock > 0) {
                    foreach ($data['stores'] as $s) {
                        $pur = StoreProductStock::where([['store_id',$s->id],['variant_id',$v->id]])->sum('purchase_qty');
                        $sold = StoreProductStock::where([['store_id',$s->id],['variant_id',$v->id]])->sum('sold_qty');
                        $storeVariants[$s->id][$v->id] = $pur - $sold;
                    }
                }
            }

            $result = ReceivingProduct::where('product_id',$pro->id)->orderBy('id','DESC')->first();
            if($result)
                $lastPurchasePrice[$pro->id] = $result->cost_price;
            else
                $lastPurchasePrice[$pro->id] = 0;
        }
			
        $data['average'] = $average;
        $data['totalSold'] = $totalSold;
        $data['lastPurchasePrice'] = $lastPurchasePrice;
        $data['storeVariants'] = $storeVariants;
        $data['brand'] = Brand::where('id',$id)->first();
        $data['suppliers'] = Supplier::where('status',true)->orderBy('name','ASC')->get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        $data['stores'] = Store::where('status',true)->get();
      // dd( $data);
        return view('purchase-order.auto-brand-form',$data);
    }

    public function getAutoProductPurchaseOrderForm() {
        $data['products'] = Product::with('variants')->get(); //->whereRaw('available_stock < re_order_level')
        $data['suppliers'] = Supplier::where('status',true)->orderBy('name','ASC')->get();
        $data['today'] = Carbon::today()->format('Y-m-d');
        $data['stores'] = Store::where('status',true)->get();
        $data['brands'] = Brand::where('status',true)->orderBy('title','ASC')->get();
        $data['categories'] = Category::where('status',true)->orderBy('title','ASC')->get();
        return view('purchase-order.auto-product-form',$data);

    }

}
