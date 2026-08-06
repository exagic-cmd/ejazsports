<?php

namespace App\Http\Controllers\POS_API;

use App\Http\Controllers\POS_API\BaseController as BaseController;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\CustomerPayment;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController
{
    public function getReportData(Request $request)
    {

        $storeId = $request->get('store_id');

        $data['todayBillsCount'] = Order::where('store_id', $storeId) ->whereRaw('(total_amount - return_amount) > 0')->whereDate('created_at', Carbon::today())->count();

        $data['todayBillsAmount'] = Order::where('store_id', $storeId)
            ->whereDate('created_at', Carbon::today())
            ->select(DB::raw('SUM(total_amount - return_amount) as total_amount'))
            ->value('total_amount') ?? 0;
        $data['todaySales'] = Order::join('order_products', 'order_products.order_id', '=', 'orders.id')
            ->where('orders.store_id', $storeId)
            ->whereDate('orders.created_at', Carbon::today())
            ->where('order_products.is_bundle', 0) // Exclude bundle products
            ->selectRaw('
        orders.name,
        orders.id,
        orders.order_no,
        COUNT(DISTINCT CASE WHEN order_products.qty > COALESCE(order_products.return_qty, 0) THEN order_products.id END) as total_products,
        SUM(order_products.qty - COALESCE(order_products.return_qty, 0)) as total_sold_qty,
        (orders.total_amount - orders.return_amount) as total_amount
    ')
            ->groupBy('orders.id', 'orders.order_no', 'orders.total_amount', 'orders.return_amount', 'orders.name')
            ->havingRaw('SUM(order_products.qty - COALESCE(order_products.return_qty, 0)) > 0')
            ->get();
        $data['todayCashBillsCount'] = Order::where([['store_id', $storeId], ['customer_id', 1]]) ->whereRaw('(total_amount - return_amount) > 0')->whereDate('created_at', Carbon::today())->count();
        $data['todayCashBillsAmount'] = Order::where([
            ['store_id', $storeId],
            ['customer_id', 1]
        ])
            ->whereDate('created_at', Carbon::today())
            ->select(DB::raw('SUM(total_amount - return_amount) as total'))
            ->value('total') ?? 0;
        $data['todayRetailSales'] = Order::join('order_products', 'order_products.order_id', 'orders.id')
            ->where([['orders.store_id', $storeId], ['orders.customer_id', 1]])
            ->whereDate('orders.created_at', Carbon::today())
            ->where('order_products.is_bundle', 0) // Exclude bundle products
            ->selectRaw('
        orders.name,
        orders.id,
        orders.order_no,
        COUNT(DISTINCT CASE WHEN order_products.qty > COALESCE(order_products.return_qty, 0) THEN order_products.id END) as total_products,
        SUM(order_products.qty - COALESCE(order_products.return_qty, 0)) as total_sold_qty,
        (orders.total_amount - orders.return_amount) as total_amount
    ')
            ->groupBy('orders.id', 'orders.order_no', 'orders.total_amount', 'orders.return_amount', 'orders.name')
            ->havingRaw('SUM(order_products.qty - COALESCE(order_products.return_qty, 0)) > 0')
            ->get();
        $data['todayCardBillsCount'] = Order::where([['store_id', $storeId], ['customer_id', '!=', 1]])->whereDate('created_at', Carbon::today())->count();
        $data['todayCardBillsAmount'] = Order::where([['store_id', $storeId], ['customer_id', '!=', 1]])->whereDate('created_at', Carbon::today())->sum('total_amount');

        $data['todayWholeSales'] = Order::join('order_products', 'order_products.order_id', 'orders.id')
            ->where([['orders.store_id', $storeId], ['orders.customer_id', '!=', 1]])
            ->whereDate('orders.created_at', Carbon::today())
            ->selectRaw('
        orders.*,
        COUNT(DISTINCT CASE WHEN order_products.qty > order_products.return_qty THEN order_products.id END) as total_products,
        (orders.total_amount - orders.return_amount) as total_amount
    ')
            ->groupBy('orders.id')
            ->havingRaw('(orders.total_quantity - COALESCE(SUM(order_products.return_qty), 0)) > 0')
            ->get();


        $data['todayCreditBillsCount'] = Order::where([['store_id', $storeId], ['customer_id', '!=', 1]])->where('paid_amount', 0)->whereDate('created_at', Carbon::today())->count();
        $data['todayCreditBillsAmount'] = Order::where([['store_id', $storeId], ['customer_id', '!=', 1]])->where('paid_amount', 0)->whereDate('created_at', Carbon::today())->sum(DB::raw('total_amount - paid_amount'));

        $data['todayWholeSalesCredit'] = Order::join('order_products', 'order_products.order_id', 'orders.id')
            ->where([['orders.store_id', $storeId], ['orders.customer_id', '!=', 1], ['orders.paid_amount', 0]])
            ->whereDate('orders.created_at', Carbon::today())
            ->selectRaw('
        orders.*,
        COUNT(DISTINCT CASE WHEN order_products.qty > order_products.return_qty THEN order_products.id END) as total_products,
        (orders.total_amount - orders.return_amount) as total_amount
    ')
            ->groupBy('orders.id')
            ->havingRaw('(orders.total_quantity - COALESCE(SUM(order_products.return_qty), 0)) > 0')
            ->get();

        $data['todayPaidBillsCount'] = Order::where([['store_id', $storeId], ['customer_id', '!=', 1]])->where('paid_amount', '>', 0)->whereDate('created_at', Carbon::today())->count();
        $data['todayPaidBillsAmount'] = Order::where([['store_id', $storeId], ['customer_id', '!=', 1]])->where('paid_amount', '>', 0)->whereDate('created_at', Carbon::today())->sum('paid_amount');

        $data['todayWholeSalesPaid'] = Order::join('order_products', 'order_products.order_id', 'orders.id')
            ->where([['orders.store_id', $storeId], ['orders.customer_id', '!=', 1], ['orders.paid_amount', '>', 0]])
            ->whereDate('orders.created_at', Carbon::today())
            ->selectRaw('
        orders.*,
        COUNT(DISTINCT CASE WHEN order_products.qty > order_products.return_qty THEN order_products.id END) as total_products,
        (orders.total_amount - orders.return_amount) as total_amount
    ')
            ->groupBy('orders.id')
            ->havingRaw('(orders.total_quantity - COALESCE(SUM(order_products.return_qty), 0)) > 0')
            ->get();

        $data['todayCustomerPaymentA'] = CustomerPayment::whereDate('date', Carbon::today())->sum('amount') - Order::whereDate('created_at', Carbon::today())->where([['store_id', $storeId], ['customer_id', '!=', 1]])->sum('paid_amount');

        // Find customer payments made today
        $customerPayments = CustomerPayment::whereDate('date', Carbon::today())->get();

        // Find orders placed today
        $orders = Order::whereDate('created_at', Carbon::today())->where([['store_id', $storeId], ['customer_id', '!=', 1]])->get();



        // Filter out customer payments not associated with today's orders
        $data['todayLedgerPayment'] = $customerPayments->reject(function ($customerPayment) use ($orders) {
            return $orders->contains('customer_id', $customerPayment->customer_id);
        });



        $data['todayCustomerPaymentC'] = CustomerPayment::whereDate('date', Carbon::today())->count() - Order::where([['store_id', $storeId], ['customer_id', '!=', 1]])->where('paid_amount', '>', 0)->whereDate('created_at', Carbon::today())->count();

        $data['todayExpenseA'] = Expense::whereDate('date', Carbon::today())->sum('amount');

        $data['todayMargin'] = Order::whereDate('created_at', Carbon::today())->sum('margin');

        $data['todayProfit'] = OrderProduct::whereDate('created_at', Carbon::today())->where('is_bundle', 0)->sum(DB::raw('(price * qty) - (cost_price * qty)'));

        $data['todayExpenseC'] = Expense::whereDate('date', Carbon::today())->count();

        $data['todayCReturnBillsCount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->where('return_type', 2)->whereDate('return_date', Carbon::today())->count();
        $data['todayCReturnBillsAmount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->where('return_type', 2)->whereDate('return_date', Carbon::today())->sum('return_amount');


        $data['todayLReturnBillsCount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->where('return_type', 1)->whereDate('return_date', Carbon::today())->count();
        $data['todayLReturnBillsAmount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->where('return_type', 1)->whereDate('return_date', Carbon::today())->sum('return_amount');

        $orderIds = Order::where('store_id', $storeId)->whereDate('created_at', Carbon::today())->pluck('id')->toArray();
        $result = OrderProduct::groupBy('variant_id')->with('variant')->whereIn('order_id', $orderIds)->selectRaw('sum(price * qty) as tprice, variant_id,sum(qty) as tqy')->get();
        $data['variants'] = $result->sortByDESC('tprice');
        $products = array();
        foreach ($result as $res) {
            $variant_id = $res->variant_id;
            $products[$res->variant_id] = Product::wherehas('variants', function ($query) use ($variant_id) {
                $query->where('id', $variant_id);
            })->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
        }
        $data['products'] = $products;

        $data['allProducts'] = Product::with('variants')->select('id', 'title', 'have_variants')
            ->get();

        $data['categories'] = Category::select('id', 'title')->get();

        return $this->sendResponse($data, 'Report Page Content.');
    }

    public function getCustomReportData(Request $request)
    {

        $storeId = $request->get('store_id');
        $option = $request->get('option');

        if ($option == 'today') {

            $data['billsCount'] = Order::where('store_id', $storeId)->whereDate('created_at', Carbon::today())->count();
            $data['billsAmount'] = Order::where('store_id', $storeId)->whereDate('created_at', Carbon::today())->sum('total_amount');

            $data['cashBillsCount'] = Order::where([['store_id', $storeId], ['payment_method', Order::CASH]])->whereDate('created_at', Carbon::today())->count();
            $data['cashBillsAmount'] = Order::where([['store_id', $storeId], ['payment_method', Order::CASH]])->whereDate('created_at', Carbon::today())->sum('total_amount');

            $data['cardBillsCount'] = Order::where([['store_id', $storeId], ['payment_method', Order::ONLINE]])->whereDate('created_at', Carbon::today())->count();
            $data['cardBillsAmount'] = Order::where([['store_id', $storeId], ['payment_method', Order::ONLINE]])->whereDate('created_at', Carbon::today())->sum('total_amount');

            $data['returnBillsCount'] = Order::where('store_id', $storeId)->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->whereDate('return_date', Carbon::today())->count();
            $data['returnBillsAmount'] = Order::where('store_id', $storeId)->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->whereDate('return_date', Carbon::today())->sum('return_amount');

            $orderIds = Order::where('store_id', $storeId)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->pluck('id')->toArray();
            $result = OrderProduct::groupBy('variant_id')->with('variant')->whereIn('order_id', $orderIds)->selectRaw('sum(price * qty) as tprice, variant_id,sum(qty) as tqy')->get();
            $data['variants'] = $result->sortByDESC('tprice');
            $products = array();
            foreach ($result as $res) {
                $variant_id = $res->variant_id;
                $products[$res->variant_id] = Product::wherehas('variants', function ($query) use ($variant_id) {
                    $query->where('id', $variant_id);
                })->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
            }
            $data['products'] = $products;
            $data['option'] = $option;
        } elseif ($option == 'weekly') {
            $data['billsCount'] = Order::where('store_id', $storeId)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
            $data['billsAmount'] = Order::where('store_id', $storeId)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');

            $data['cashBillsCount'] = Order::where([['store_id', $storeId], ['payment_method', Order::CASH]])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
            $data['cashBillsAmount'] = Order::where([['store_id', $storeId], ['payment_method', Order::CASH]])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');

            $data['cardBillsCount'] = Order::where([['store_id', $storeId], ['payment_method', Order::ONLINE]])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
            $data['cardBillsAmount'] = Order::where([['store_id', $storeId], ['payment_method', Order::ONLINE]])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');

            $data['returnBillsCount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
            $data['returnBillsAmount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('return_amount');

            $orderIds = Order::where('store_id', $storeId)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->pluck('id')->toArray();
            $result = OrderProduct::groupBy('variant_id')->with('variant')->whereIn('order_id', $orderIds)->selectRaw('sum(price * qty) as tprice, variant_id,sum(qty) as tqy')->get();
            $data['variants'] = $result->sortByDESC('tprice');
            $products = array();
            foreach ($result as $res) {
                $variant_id = $res->variant_id;
                $products[$res->variant_id] = Product::wherehas('variants', function ($query) use ($variant_id) {
                    $query->where('id', $variant_id);
                })->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
            }
            $data['products'] = $products;
            $data['option'] = $option;
        } else {
            $data['billsCount'] = Order::where('store_id', $storeId)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            $data['billsAmount'] = Order::where('store_id', $storeId)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_amount');

            $data['cashBillsCount'] = Order::where([['store_id', $storeId], ['payment_method', Order::CASH]])->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            $data['cashBillsAmount'] = Order::where([['store_id', $storeId], ['payment_method', Order::CASH]])->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_amount');

            $data['cardBillsCount'] = Order::where([['store_id', $storeId], ['payment_method', Order::ONLINE]])->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            $data['cardBillsAmount'] = Order::where([['store_id', $storeId], ['payment_method', Order::ONLINE]])->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_amount');

            $data['returnBillsCount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->count();
            $data['returnBillsAmount'] = Order::where([['store_id', $storeId]])->whereIn('status', [Order::PARTIALLY_RETURNED, Order::RETURNED])->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('return_amount');

            $orderIds = Order::where('store_id', $storeId)->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->pluck('id')->toArray();
            $result = OrderProduct::groupBy('variant_id')->with('variant')->whereIn('order_id', $orderIds)->selectRaw('sum(price * qty) as tprice, variant_id,sum(qty) as tqy')->get();
            $data['variants'] = $result->sortByDESC('tprice');
            $products = array();
            foreach ($result as $res) {
                $variant_id = $res->variant_id;
                $products[$res->variant_id] = Product::wherehas('variants', function ($query) use ($variant_id) {
                    $query->where('id', $variant_id);
                })->select('title', 'price', 'id', 'discount_status', 'discount_amount')->first();
            }
            $data['products'] = $products;
            $data['option'] = $option;
        }

        $data['allProducts'] = Product::with('variants')->select('id', 'title', 'have_variants')
            ->get();


        return $this->sendResponse($data, 'Report Page Content.');
    }
}
