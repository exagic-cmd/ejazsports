<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Courier;
use App\Models\CourierHandoverOrder;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Models\StoreProductStock;
use App\Models\Store;

class OrderController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->order = new Order;
        // $this->middleware('permission:List Orders', ['only' => ['index']]);
        // $this->middleware('permission:View Orders', ['only' => ['show']]);
        // $this->middleware('permission:Create Orders', ['only' => ['create','store']]);
        // $this->middleware('permission:Edit Orders', ['only' => ['edit','update']]);
        // $this->middleware('permission:Delete Orders', ['only' => ['destroy']]);
        // $this->middleware('permission:List Pending Orders',['only' => ['getPendingOrders']]);
        // $this->middleware('permission:List Return Orders',['only' => ['getReturnOrders']]);
        // $this->middleware('permission:List Cancel Orders',['only' => ['getCancelOrders']]);
        // $this->middleware('permission:List Complete Orders',['only' => ['getCompleteOrders']]);
        // $this->middleware('permission:List Booked Orders',['only' => ['getBookedOrders']]);
        // $this->middleware('permission:New Order Booking',['only' => ['newOrderBooking']]);
        // $this->middleware('permission:Un Booked Order',['only' => ['unBookedOrder']]);
        // $this->middleware('permission:Scan Orders',['only' => ['addNewScan']]);
        // $this->middleware('permission:List Scanned Orders',['only' => ['getScannedOrders']]);


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['allOrders'] = Order::orderBy('id','DESC')->paginate(500);
        $data['orderCount'] = Order::count();

        $data['stores'] = Store::where('status',true)->get();

        return view('order/orders',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {

        $order->load('products.product', 'products.bundle', 'products.variant');
        return view('order.show',compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $data['order'] = $order;
        $data['products'] = Product::where('status',true)->orderBy('product_heading','ASC')->get();
        $variants = array();
        foreach($order->products as $pro)
            $variants[$pro->product_id] = ProductVariant::where('product_id',$pro->product_id)->get();

        $data['variants'] = $variants;

        $data['areas'] = Area::orderBy('serial_no','ASC')->get();

        return view('order.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required'],
            'email' => ['nullable', 'string', 'max:255'],
            'address' => ['required'],
            'city' => ['required'],
            'sub_total' => ['required', 'integer', 'min:1'],
            'total_amount' => ['required', 'integer', 'min:1'],

        ]);

        $order = $this->order->updateOrder($request,$order);

        activity('Update')->log(' [ <b> ' . $order->order_no. ' </b> ] Order is updated');

        return redirect()->route('orders.show',$order->id)->with('message', 'Order updated Successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {

        foreach ($order->products as $pro) {
            if($pro->variant_id) {
                //quantity adjust in product and variant table
                $variant = ProductVariant::where('id', $pro->variant_id)->with('product')->first();

                Product::where('id', $variant->product_id)->update(['available_stock' => $variant->product->available_stock
                    + $pro->qty]);
                ProductVariant::where('id', $variant->id)->update(['available_stock' => $variant->available_stock
                    + $pro->qty]);
            }
            else
            {
                 $product = Product::where('id', $pro->product_id)->first();

                Product::where('id', $product->id)->update(['available_stock' => $product->available_stock
                    + $pro->qty]);

            }

              $storeStocks = StoreProductStock::where([['product_id',$pro->product_id],['variant_id',($pro->variant_id ? $pro->variant_id : null)]])->whereRaw("sold_qty <  purchase_qty")->orderBy('id','ASC')->get();



                    $orderQty = $pro->qty;
                    $receivingId= 0;
                    foreach($storeStocks as $stock) {
                        $availableQty = $stock->purchase_qty - $stock->sold_qty;

                        if($orderQty > $availableQty) {
                            $orderQty -= $availableQty;
                            StoreProductStock::where('id',$stock->id)->update(['sold_qty' => 0]);
                        }
                        else {
                            StoreProductStock::where('id',$stock->id)->update(['sold_qty' => $stock->sold_qty - $orderQty]);
                            $orderQty = 0;
                        }

                        if($orderQty == 0){
                            break;
                        }

                    }
            }

        $order->delete();
         activity('Update')->log(' [ <b> ' . $order->order_no. ' </b> ] Order is deleted');

        return redirect()->route('customers.show',$order->customer_id)->with('message','Order deleted Successfully.');
    }


    public function getPendingOrders() {

        $data['orders'] = Order::where('status',Order::PENDING)->orderBy('id','DESC')->get();
        $data['couriers'] = Courier::where('status',true)->get();

        activity('View')->log('Pending Order List');

        return view('order.pending',$data);
    }

    public function getReturnOrders() {

        $data['orders'] = Order::where('status',Order::RETURNED)->orderBy('id','DESC')->paginate(500);

        activity('View')->log('Return Order List');

        return view('order.return',$data);
    }

    public function getCancelOrders() {

        $data['orders'] = Order::where('status',Order::CANCELED)->orderBy('id','DESC')->paginate(500);

        activity('View')->log('Cancel Order List');

        return view('order.cancel',$data);
    }

    public function getCompleteOrders() {

        $data['orders'] = Order::where('status',Order::COMPLETED)->orderBy('id','DESC')->paginate(500);

        activity('View')->log('Complete Order List');

        return view('order.complete',$data);
    }

    public function newOrderBooking(Request $request) {

        $result = $this->order->newOrderBooking($request);

        return $result;
    }

    public function getBookedOrders() {

        $data['orders'] = Order::where('status',Order::BOOKED)->orderBy('id','DESC')->get();

        activity('View')->log('Booked Order List');
        return view('order.booked',$data);
    }

    public function unBookedOrder(Request $request) {

        if($request->order_id) {
            $result = Order::where('id',$request->order_id)->update(['cn_no'=>null,'courier_id' => null,'booking_time' =>null, 'status' => Order::PENDING]);

            return ['status' => true];
        }
        return ['status' => false];
    }

    public function getScannedOrders() {

        $data['orders'] = Order::where('status',Order::SCANNED)->orderBy('id','DESC')->get();

        activity('View')->log('Scanned Order List');
        return view('order.scanned',$data);
    }

    public function addNewScan() {

        return view('order.new-scan');
    }
    public function showOrderInfo(Request $request) {

        $order = Order::where('cn_no',$request->cn_no)->first();

        if($order) {
            if($order->scanned)
                return ['success' => false,'message' => 'Order Already Scanned'];
            else
                return view('order.order-info-ajax',compact('order'));
        }
        else
            return ['success' => false,'message' => 'Cn No not found..'];
    }

    public function scanProductOrder(Request $request) {

        $result = OrderProduct::where([['order_id',$request->order_id],['barcode','like','%'.$request->barcode . '%']])->first();

        if($result)
            return ['success' => true,'message' => 'Product Scanned Successfully..','data' => $result];
        else
            return ['success' => false,'message' => 'Product not found..'];

    }

    public function orderScanComplete(Request $request) {

        $result = Order::where('id',$request->order_id)->update(['scanned' => true,'status' => Order::SCANNED]);
        OrderProduct::where('order_id',$request->order_id)->update(['scanned' => true]);

        activity('View')->log('Order Scanned.');
        if($result)
            return ['success' => true,'message' => 'Order Scanned Successfully..'];
        else
            return ['success' => false,'message' => 'Order not found..'];

    }

    public function getDispatchedOrders() {

        $data['orders'] = Order::where('status',Order::DISPATCHED)->orderBy('id','DESC')->get();

        return view('order.dispatched',$data);
    }

    public function addNewDispatch() {

        $couriers = Courier::where('status',true)->orderBy('name','ASC')->get();

        return view('order.new-dispatch',compact('couriers'));
    }

    public function scanDispatchOrder(Request $request) {

        $orderArray = array();
        $totalAmount = 0;
        foreach($request->cn_nos as $val) {
            $order = Order::where('cn_no',$val)->first();
            if($order->courier_id != $request->courier_id)
                return ['success' => false,'message' => 'Courier not match..'];
            if($order->status != Order::SCANNED)
                return ['success' => false,'message' => 'Order is not Scan..'];
            array_push($orderArray,$order);
            $totalAmount += ($order->total_amount - $order->paid_amount);
        }

        return view('order.order-scan-ajax',compact('orderArray','totalAmount'));
    }

    public function orderDispatchComplete(Request $request) {

        $result = $this->order->orderDispatchComplete($request);

        activity('View')->log('Dispatch Orders.');
        return $result;

    }

    public function getreturnVoucher($id) {

        $order  = Order::find($id);

        return view('order.return-show',compact('order'));
    }

    public function downloadPosReturn($id) {
        $order = Order::with('products.product', 'products.variant', 'products.bundle', 'employee', 'customer')->where('id',$id)->first();
        
        $previousBalance = 0;
        $totalRemaining = 0;
        if ($order && $order->customer_id != 1) {
            $totalBillAmount = Order::where('id', '!=', $order->id)
                ->where('customer_id', $order->customer_id)
                ->where('status', '!=', 6)
                ->sum('total_amount');
            $totalReturnAmount = Order::where('customer_id', $order->customer_id)
                ->where('return_type', 1)
                ->sum('return_amount');
            $totalPayment = \App\Models\CustomerPayment::where('customer_id', $order->customer_id)
                ->where('status', 2)
                ->sum('amount') - $order->paid_amount;
            $totalDiscount = \App\Models\CustomerPayment::where('customer_id', $order->customer_id)
                ->where('status', 2)
                ->sum('discount');

            $previousBalance = ((($order->customer->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount;

            $totalBillAmountInc = Order::where('customer_id', $order->customer_id)
                ->where('status', '!=', 6)
                ->sum('total_amount');
            $totalRemaining = ((($order->customer->opening_balance + $totalBillAmountInc) - $totalReturnAmount) - $totalPayment) - $totalDiscount;
        }
        
        $result = new \stdClass();
        $result->data = new \stdClass();
        $result->data->order = $order;
        $result->data->previousBalance = $previousBalance;
        $result->data->totalRemaining = $totalRemaining;
        
        return view('order.pos-print', compact('result'));
    }

    public function printOrder($id) {
        $data['order'] = Order::with('products.product', 'products.variant', 'products.bundle')->where('id',$id)->first();

        return view('order.print',$data);
    }
     public function printa4Order($id) {
        $data['order'] = Order::with('products.product', 'products.variant', 'products.bundle')->where('id',$id)->first();

        return view('order.a4',$data);
    }
     public function pdfOrder($id) {
        $data['order'] = Order::with('products.product', 'products.variant', 'products.bundle')->where('id',$id)->first();

        return view('order.pdf',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function customerOrder($id)
    {
        $order = Order::with('products')->where('id',$id)->first();
        return view('order.show-customer',compact('order'));
    }
}
