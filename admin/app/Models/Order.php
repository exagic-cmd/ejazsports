<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'order_no',
        'customer_id',
        'name',
        'email',
        'phone_number',
        'address',
        'city',
        'status',
        'delivery_charges',
        'discount_amount',
        'total_amount',
        'coupon_id',
        'payment_method',
        'paid_amount',
        'fbr_invoice_id',
        'fbr_return_invoice_id',
        'store_id',
        'total_products',
        'total_quantity',
        'cn_no',
        'courier_id',
        'booking_time',
        'scanned',
        'handover',
        'handover_id',
        'dispatch_time',
        'return_date',
        'return_amount',
        'return_type',
        'additional_notes',
        'employee_id',
        'margin',
        'pay_amount',
        'is_website_order',
        'adjust_type',
        'order_notes'
        ];

    //Order Satus
    const PENDING = 1;
    const BOOKED = 2;
    const SCANNED = 3;
    const DISPATCHED = 4;
    const DELIVERED = 5;
    const RETURNED = 6;
    const CANCELED = 7;
    const COMPLETED = 8;
    const PARTIALLY_RETURNED = 9;

    //Payment Method
    const CASH = 1;
    const ONLINE = 2;
    const QISTPAY = 3;
    const EASYPAISA = 4;
    const JAZZCASH = 5;

    public function products() {
        return $this->hasMany(OrderProduct::class)->with('product','variant');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function area() {
        return $this->belongsTo(Area::class,'city');
    }

    public function courier() {
        return $this->belongsTo(Courier::class);
    }

    public function newOrderBooking($request) {

        //check the areas of courier and select orders of that area
        $courier = Courier::find($request->courier_id);
        $courierAreas = CourierArea::where('courier_id',$courier->id)->pluck('area_id')->toArray();
        $orders = Order::whereIn('id',$request->check_ids)->whereIn('city',$courierAreas)->get();

        //send to call courier
        $courier = new Courier();
        return $courier->callCourierBooking($orders,$request->courier_id);
    }

    public function updateOrder($request, $order) {

        DB::beginTransaction();
        try {
            //update the customer info
            $order->name = $request->name;
            $order->email = $request->email;
            $order->phone_number = $request->phone_number;
            $order->address = $request->address;
            $order->city = $request->city;

            //update the amount info
            $order->delivery_charges = $request->delivery_charges;
            $order->discount_amount = $request->discount_amount;
            $order->total_amount = $request->total_amount;



            //update order products
            //add Order Products
            $totalAmount = 0;
            $totalProducts = 0;
            $totalQuantity  = 0;

            OrderProduct::where('order_id',$order->id)->delete();

            for ($i = 0; $i < count($request->product_id); $i++) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $request->product_id[$i];
                $orderProduct->variant_id = $request->variant_id[$i];
                $orderProduct->qty = $request->quantity[$i];
                $orderProduct->price = $request->price[$i];
                $orderProduct->save();

                $totalAmount += ($request->price[$i] * $request->quantity[$i]) ;
                $totalProducts++;
                $totalQuantity += $request->quantity[$i];
            }

            $order->delivery_charges = $request->delivery_charges;
            $order->discount_amount = $request->discount_amount;
            $order->total_amount = $totalAmount + $request->delivery_charges - $order->discount_amount;
            $order->total_products = $totalProducts;
            $order->total_quantity = $totalQuantity;

            $order->save();

            //update in the customer information
            $customer = Customer::find($order->customer_id);
            $customer->first_name = $request->name;
            $customer->email = $request->email;
            $customer->phone_number = $request->phone_number;
            $customer->address = $request->address;
            $customer->area_id = $request->city;
            $customer->save();

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
        }

    }

    public function orderDispatchComplete($request) {

        DB::beginTransaction();
        try {
            $courierHandover = new CourierHandoverOrder();
            $courierHandover->courier_id = $request->courier_id;
            $courierHandover->total_orders = 0;
            $courierHandover->total_amount = 0;
            $courierHandover->save();

            $total_orders = $total_amount = 0;

            foreach ($request->cn_nos as $val) {

                $order = Order::where('cn_no',$val)->first();
                $order->status = Order::DISPATCHED;
                $order->handover = true;
                $order->handover_id = $courierHandover->id;
                $order->dispatch_time = Carbon::now();
                $order->save();
                $total_orders++;
                $total_amount += ($order->total_amount - $order->paid_amount);
            }

            $courierHandover->total_amount = $total_amount;
            $courierHandover->total_orders = $total_orders;
            $courierHandover->save();

            DB::commit();

            return ['success' => true,'message' => 'Orders Dispatched Successfully...'];

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // something went wrong
            }
    }

    public function getDashboardStats() {

        //get Status Wise Data
        $data['last2PendingOrders'] = Order::where('status',Order::PENDING)->whereDate('created_at','>=',Carbon::today()->subDay(1))
            ->whereDate('created_at','<=',Carbon::today())->count();
        $data['allPendingOrders'] = Order::where('status',Order::PENDING)->count();
        $data['last2BookedOrders'] = Order::where('status',Order::BOOKED)->whereDate('booking_time','>=',Carbon::today()->subDay(1))
            ->whereDate('booking_time','<=',Carbon::today())->count();
        $data['allBookedOrders'] = Order::where('status',Order::BOOKED)->count();
        $data['todayDispatchOrders'] = Order::where('status',Order::DISPATCHED)->whereDate('dispatch_time',Carbon::today())->count();
        $data['todayCanceledOrders'] = Order::where('status',Order::CANCELED)->whereDate('created_at',Carbon::today())->count();
        $data['todayReturnOrders'] = Order::where('status',Order::RETURNED)->whereDate('return_date',Carbon::today())->count();

        //Store Wise Orders States
        $stores = Store::all();$temp = array(); $storeInfo = array();
        foreach ($stores as $store) {
            $temp['info'] = $store->name;
            $temp['todayOrdersCount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at', Carbon::today())->count();
            $tomarrowOrdersCount = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at', Carbon::today()->subDay(1))->count();
            $temp['todayOrdersCountPer'] = round((( $temp['todayOrdersCount'] - $tomarrowOrdersCount ) / ( $tomarrowOrdersCount != 0 ? $tomarrowOrdersCount : 1 ) ) * 100);

            $temp['todayOrdersAmount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at', Carbon::today())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at', Carbon::today())->sum('return_amount');
            $tomarrowOrdersAmount = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at', Carbon::today()->subDay(1))->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at', Carbon::today()->subDay(1))->sum('return_amount');
            $temp['todayOrdersAmountPer'] = round((( $temp['todayOrdersAmount'] - $tomarrowOrdersAmount ) / ( $tomarrowOrdersAmount != 0 ? $tomarrowOrdersAmount : 1 ) ) * 100);

            $temp['todayCashOrdersCount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where([['store_id',$store->id],['payment_method',Order::CASH]])->whereDate('created_at', Carbon::today())->count();
            $temp['todayCashOrdersAmount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where([['store_id',$store->id],['payment_method',Order::CASH]])->whereDate('created_at', Carbon::today())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where([['store_id',$store->id],['payment_method',Order::CASH]])->whereDate('created_at', Carbon::today())->sum('return_amount');
            $temp['todayCardOrdersCount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where([['store_id',$store->id],['payment_method',Order::ONLINE]])->whereDate('created_at', Carbon::today())->count();
            $temp['todayCardOrdersAmount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where([['store_id',$store->id],['payment_method',Order::ONLINE]])->whereDate('created_at', Carbon::today())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where([['store_id',$store->id],['payment_method',Order::ONLINE]])->whereDate('created_at', Carbon::today())->sum('return_amount');

            $temp['weeklyOrdersCount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->startOfWeek())->whereDate('created_at','<=',Carbon::today())->count();
            $lastWeekOrdersCount = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subWeek(1)->startOfWeek())->whereDate('created_at','<=',Carbon::now()->subWeek(1)->endOfWeek())->count();
            $temp['weeklyOrdersCountPer'] = round((( $temp['weeklyOrdersCount'] - $lastWeekOrdersCount ) / ( $lastWeekOrdersCount != 0 ? $lastWeekOrdersCount : 1) ) * 100);

            $temp['weeklyOrdersAmount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->startOfWeek())->whereDate('created_at','<=',Carbon::today())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->startOfWeek())->whereDate('created_at','<=',Carbon::today())->sum('return_amount');
            $lastWeekOrdersAmount = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subWeek(1)->startOfWeek())->whereDate('created_at','<=',Carbon::now()->subWeek(1)->endOfWeek())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subWeek(1)->startOfWeek())->whereDate('created_at','<=',Carbon::now()->subWeek(1)->endOfWeek())->sum('return_amount');
            $temp['weeklyOrdersAmountPer'] = round((( $temp['weeklyOrdersAmount'] - $lastWeekOrdersAmount ) / ( $lastWeekOrdersAmount != 0 ? $lastWeekOrdersAmount : 1) ) * 100);

            $temp['monthlyOrdersCount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->startOfMonth())->whereDate('created_at','<=',Carbon::today())->count();
            $lastMonthOrdersCount =  Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth(1)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth(1)->endOfMonth())->count();
            $temp['monthlyOrdersCountPer'] = round((($temp['monthlyOrdersCount'] - $lastMonthOrdersCount ) / ( $lastMonthOrdersCount != 0 ? $lastMonthOrdersCount : 1) ) * 100);

            $temp['monthlyOrdersAmount'] = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->startOfMonth())->whereDate('created_at','<=',Carbon::today())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->startOfMonth())->whereDate('created_at','<=',Carbon::today())->sum('return_amount');
            $lastMonthOrdersAmount =  Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth(1)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth(1)->endOfMonth())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth(1)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth(1)->endOfMonth())->sum('return_amount');
            $temp['monthlyOrdersAmountPer'] = round((($temp['monthlyOrdersAmount'] - $lastMonthOrdersAmount ) / ( $lastMonthOrdersAmount != 0 ? $lastMonthOrdersAmount : 1) ) * 100);

            array_push($storeInfo,(object) $temp);
        }

        $data['storeInfo'] =  $storeInfo;


        return $data;
    }

    public function getGraphReport() {

        $stores = Store::all();$temp = array(); $storeInfo = array();
        //last 12 month sales
        $storeMonthlySales = array();
        foreach ($stores as $store) {
            $storeMonthlySales[$store->id]['label'] = $store->name;
            $monthlySales = array();
            for($i = 12; $i > -1 ; $i--) {
                $temp = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth($i)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth($i)->endOfMonth())->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth($i)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth($i)->endOfMonth())->sum('return_amount');
                array_push($monthlySales,$temp);
            }
            $storeMonthlySales[$store->id]['monthlySales'] = $monthlySales;
        }

        $data['storeMonthlySales'] = $storeMonthlySales;

        $storeMonthlyOrders = array();
        foreach ($stores as $store) {
            $storeMonthlyOrders[$store->id]['label'] = $store->name;
            $monthlyOrders = array();
            for($i = 12; $i > -1 ; $i--) {
                $temp = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subMonth($i)->startOfMonth())->whereDate('created_at','<=',Carbon::now()->subMonth($i)->endOfMonth())->count();
                array_push($monthlyOrders,$temp);
            }
            $storeMonthlyOrders[$store->id]['monthlyOrders'] = $monthlyOrders;
        }

        $data['storeMonthlyOrders'] = $storeMonthlyOrders;

        $monthNames = array();
        for($i = 12; $i > -1 ; $i--) {
            $temp = Carbon::now()->subMonth($i)->getTranslatedShortMonthName();

            array_push($monthNames,$temp);
        }
        $data['monthNames'] = $monthNames;

        //last 15 days sales
        $storeDailySales = array();
        foreach ($stores as $store) {
            $storeDailySales[$store->id]['label'] = $store->name;
            $dailySales = array();
            for($i = 15; $i > -1 ; $i--) {
                $temp = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subDay($i))->whereDate('created_at','<=',Carbon::now()->subDay($i))->sum('total_amount') - Order::where('status',Order::PARTIALLY_RETURNED)->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subDay($i))->whereDate('created_at','<=',Carbon::now()->subDay($i))->sum('return_amount');
                array_push($dailySales,$temp);
            }
            $storeDailySales[$store->id]['dailySales'] = $dailySales;
        }

        $data['storeDailySales'] = $storeDailySales;

        $storeDailyOrders = array();
        foreach ($stores as $store) {
            $storeDailyOrders[$store->id]['label'] = $store->name;
            $dailyOrders = array();
            for($i = 15; $i > -1 ; $i--) {
                $temp = Order::whereNotIn('status',[Order::CANCELED,Order::RETURNED])->where('store_id',$store->id)->whereDate('created_at','>=' , Carbon::now()->subDay($i))->whereDate('created_at','<=',Carbon::now()->subDay($i))->count();
                array_push($dailyOrders,$temp);
            }
            $storeDailyOrders[$store->id]['dailyOrders'] = $dailyOrders;
        }

        $data['storeDailyOrders'] = $storeDailyOrders;

        $dayNames = array();
        for($i = 15; $i > -1 ; $i--) {
            $temp = Carbon::now()->subDay($i)->getTranslatedShortDayName();

            array_push($dayNames,$temp);
        }
        $data['dayNames'] = $dayNames;

        $data['colors'] = ['rgba(44, 120, 220, 0.2)','rgba(117,217,121,0.2)','rgba(172,129,213,0.2)','rgba(213,151,129,0.2)','rgba(44, 120, 220, 0.2)'];
        $data['borders'] = ['rgba(44, 120, 220)','rgb(66,173,32)','rgb(78,21,140)','rgb(140,75,21)','rgba(44, 120, 220)'];

        return $data;
    }
    public function getDailyGraphReport()
    {
        $stores = Store::where('status', true)->where('id', '<', 15)->get();
        $temp = array();
        $storeInfo = array();
        //last 36 days sales
        $storeDailySales = array();
        $totalDailySales = 0;
        $allDailySales = array();
        $allDailySales = array_fill(0, 37, 0);
        $storeDailyOrders = array();
        $allDailyOrders = array();
        $allDailyOrders = array_fill(0, 37, 0);
        $storeDailySkus = array();
        $allDailySkus = array();
        $allDailySkus = array_fill(0, 37, 0);
        foreach ($stores as $store) {
            $storeDailySales[$store->id]['label'] = $store->name;
            $storeDailyOrders[$store->id]['label'] = $store->name;
            $storeDailySkus[$store->id]['label'] = $store->name;
            $dailySales = array();
            $dailyOrders = array();
            $dailySkus = array();
            $c = 0;
            for ($i = 36; $i > -1; $i--) {
                $temp = Order::whereBetween('created_at',[ Carbon::now()->subDay($i)->startOfDay(), Carbon::now()->subDay($i)->endOfDay()])->where('store_id', $store->id)->whereNotIn('status', [Order::CANCELED, Order::RETURNED])->sum(DB::raw('total_amount - delivery_charges')) - Order::whereBetween('created_at',[ Carbon::now()->subDay($i)->startOfDay(), Carbon::now()->subDay($i)->endOfDay()])->where('store_id', $store->id)->where('status', Order::PARTIALLY_RETURNED)->sum('return_amount');
                array_push($dailySales, $temp);
                $allDailySales[$c] = $allDailySales[$c]  + $temp;
                $temp = Order::whereBetween('created_at',[ Carbon::now()->subDay($i)->startOfDay(), Carbon::now()->subDay($i)->endOfDay()])->where('store_id', $store->id)->whereNotIn('status', [Order::CANCELED, Order::RETURNED])->count();
                array_push($dailyOrders, $temp);
                $allDailyOrders[$c] = $allDailyOrders[$c] + $temp;
                $temp = Order::whereBetween('created_at',[ Carbon::now()->subDay($i)->startOfDay(), Carbon::now()->subDay($i)->endOfDay()])->where('store_id', $store->id)->whereNotIn('status', [Order::CANCELED, Order::RETURNED])->sum('total_quantity');
                array_push($dailySkus, $temp);
                $allDailySkus[$c] = $allDailySkus[$c] + $temp;
                $c++;
            }
            $storeDailySales[$store->id]['dailySales'] = $dailySales;
            $storeDailyOrders[$store->id]['dailyOrders'] = $dailyOrders;
            $storeDailySkus[$store->id]['dailySkus'] = $dailySkus;
        }
        $storeDailySales['0']['label'] = 'All';
        $storeDailySales['0']['dailySales'] = $allDailySales;
        $data['storeDailySales'] = $storeDailySales;


        $storeDailyOrders['0']['label'] = 'All';
        $storeDailyOrders['0']['dailyOrders'] = $allDailyOrders;
        $data['storeDailyOrders'] = $storeDailyOrders;

         $storeDailySkus['0']['label'] = 'All';
        $storeDailySkus['0']['dailySkus'] = $allDailySkus;
        $data['storeDailySkus'] = $storeDailySkus;



        $dayNames = array();
        for ($i = 36; $i > -1; $i--) {
            $temp = Carbon::now()->subDay($i)->format('d') . '-'.Carbon::now()->subDay($i)->format('D');
            array_push($dayNames, $temp);
        }
        $data['dayNames'] = $dayNames;
        $data['colors'] = ['rgba(44, 120, 220, 0.2)', 'rgba(117,217,121,0.2)', 'rgba(172,129,213,0.2)', 'rgba(213,151,129,0.2)', 'rgba(144, 10, 120, 0.2)', 'rgba(254, 190, 220, 0.2)', 'rgba(99, 80, 190, 0.2)', 'rgba(34, 255, 48, 0.2)'];
        $data['borders'] = ['rgba(44, 120, 220)', 'rgb(66,173,32)', 'rgb(78,21,140)', 'rgb(213,151,129)', 'rgb(144, 220, 120)', 'rgb(254, 190, 220)', 'rgb(99, 80, 190)', 'rgb(34, 255, 48)'];
        return $data;
    }
     public static function generateOrderNo()
    {
        $lastOrder = self::orderBy('id', 'desc')->first();
        $lastId = $lastOrder ? $lastOrder->id : 0;
        return 'ORD' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
    }

}
