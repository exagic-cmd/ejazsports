<?php

namespace App\Http\Controllers\POS_API;
use App\Http\Controllers\POS_API\BaseController as BaseController;

use App\Models\Product;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Category;
use App\Models\StoreClosing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends BaseController
{
    public function getCashierData(Request $request) {

        $storeId = $request->get('store_id');

        $result = StoreClosing::where('store_id',$storeId)->whereDate('date',Carbon::today()->subDay(1))->first();
        $data['openingBalance'] = $result ? $result->closing_amount  : 0;

        $data['cashBills'] = Order::where([['store_id',$storeId],['payment_method',Order::CASH]])->whereDate('created_at',Carbon::today())->sum('total_amount');
        $data['cardBills'] = Order::where([['store_id',$storeId],['payment_method',Order::ONLINE]])->whereDate('created_at',Carbon::today())->sum('total_amount');
        $data['returnBills'] = Order::where('store_id',$storeId)->whereIn('status',[Order::RETURNED,Order::PARTIALLY_RETURNED])->whereDate('return_date',Carbon::today())->sum('return_amount');

        $data['expenseAmount'] = Expense::where('store_id',$storeId)->whereDate('date',Carbon::today())->sum('amount');

        $data['allClosings'] = StoreClosing::where('store_id',$storeId)->orderBy('date','DESC')->get();

        $data['todayClosing'] = StoreClosing::where('store_id',$storeId)->where('date',Carbon::today())->first();
        
        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

$data['categories'] = Category::select('id','title')->get();

        return $this->sendResponse($data,'Cashier Page Content.');
    }

    public function createClosing(Request $request) {

        DB::beginTransaction();
        try {

        $storeId = $request->get('store_id');
        $closing = new StoreClosing();
        $closing->store_id = $storeId;
        $closing->date = Carbon::today();

        $result = StoreClosing::where('store_id',$storeId)->whereDate('date',Carbon::today()->subDay(1))->first();

        $closing->opening_balance = $result ? $result->closing_amount  : 0;
        $closing->cash_bills = Order::where([['store_id',$storeId],['payment_method',Order::CASH]])->whereDate('created_at',Carbon::today())->sum('total_amount');
        $closing->card_bills = Order::where([['store_id',$storeId],['payment_method',Order::ONLINE]])->whereDate('created_at',Carbon::today())->sum('total_amount');
        $closing->return_bills = Order::where('store_id',$storeId)->whereIn('status',[Order::RETURNED,Order::PARTIALLY_RETURNED])->whereDate('return_date',Carbon::today())->sum('return_amount');
        $closing->expense = Expense::where('store_id',$storeId)->whereDate('date',Carbon::today())->sum('amount');

        $expectingAmount = ($result ? $result->closing_amount  : 0 ) + Order::where([['store_id',$storeId],['payment_method',Order::CASH]])->whereDate('created_at',Carbon::today())->sum('total_amount') - Order::where('store_id',$storeId)->whereIn('status',[Order::RETURNED,Order::PARTIALLY_RETURNED])->whereDate('return_date',Carbon::today())->sum('return_amount') - Expense::where('store_id',$storeId)->whereDate('date',Carbon::today())->sum('amount');

        $closing->expecting_amount = $expectingAmount;
        $closingAmount = 0;

        $closing->five_coin_count = $request->get('5_amount');$closingAmount += $request->get('5_amount') * 5;
        $closing->ten_note_count = $request->get('10_amount');$closingAmount += $request->get('10_amount') * 10;
        $closing->twenty_note_count = $request->get('20_amount');$closingAmount += $request->get('20_amount') * 20;
        $closing->fifty_note_count = $request->get('50_amount');$closingAmount += $request->get('50_amount') * 50;
        $closing->hundred_note_count = $request->get('100_amount');$closingAmount += $request->get('100_amount') * 100;
        $closing->five_hundred_note_count = $request->get('500_amount');$closingAmount += $request->get('500_amount') * 500;
        $closing->one_thousand_note_count = $request->get('1000_amount');$closingAmount += $request->get('1000_amount') * 1000;
        $closing->five_thousand_note_count = $request->get('5000_amount');$closingAmount += $request->get('5000_amount') * 5000;

        $closing->closing_amount = $closingAmount;
        $closing->difference = $expectingAmount - $closingAmount;
        $closing->note = $request->get('note');
        $closing->save();

        $data['closing'] = $closing;
        DB::commit();

        return $this->sendResponse($data,'Create Closing Content.');
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            dd($e);
            // something went wrong
        }

    }






}
