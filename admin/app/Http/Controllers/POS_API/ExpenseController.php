<?php

namespace App\Http\Controllers\POS_API;


use App\Http\Controllers\POS_API\BaseController as BaseController;

use App\Models\Product;
use App\Models\Category;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends BaseController
{
    public function getExpenseData(Request $request) {

       $storeId = $request->get('store_id');

       $data['previousExpense'] = Expense::where('store_id',$storeId)->whereDate('date','<',Carbon::today())->orderBy('date','DESC')->get();
       $data['previousExpenseAmount'] = Expense::where('store_id',$storeId)->whereDate('date','<',Carbon::today())->sum('amount');

       $data['todayExpense'] = Expense::where('store_id',$storeId)->whereDate('date',Carbon::today())->orderBy('id','DESC')->get();
       $data['todayExpenseAmount'] = Expense::where('store_id',$storeId)->whereDate('date',Carbon::today())->sum('amount');
       
       $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();

$data['categories'] = Category::select('id','title')->get();

       return $this->sendResponse($data,'Expense Page Content.');
    }

    public function createExpense(Request $request) {

        try {

            $expense = new Expense();
            $expense->category_id = $request->category_id;
            $expense->amount = $request->amount;
            $expense->date = $request->date;
            $expense->detail = $request->detail;
            $expense->store_id = $request->store_id;
            $expense->bill_no = $request->bill_no;

            $expense->save();

        } catch(\Exception $e) {
            return $e->getMessage();
        }

        $data['expense'] = $expense;
        return $this->sendResponse($data,'Expense Created.');
    }


}
