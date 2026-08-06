<?php

namespace App\Http\Controllers\POS_API;


use App\Http\Controllers\POS_API\BaseController as BaseController;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Order;

use App\Models\Category;
use App\Models\Product;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;

class CustomerController extends BaseController
{
    public function getCustomerData(Request $request) {

        $storeId = $request->get('store_id');

        $data['customers'] = Customer::where('is_website_customer', 0)->orderBy('id','DESC')->get();
        
        $data['allProducts'] = Product::with('variants')->select('id','title','have_variants')
->get();


 $data['categories'] = Category::select('id','title')->get();

        return $this->sendResponse($data,'Customer Data.');
    }

    public function getCustomerSearchData(Request $request) {

        $val = $request->get('val');
        // $storeId = $request->get('store_id');

        $query = Customer::where('is_website_customer', 0)
            ->where(function($q) use ($val) {
                $q->where('first_name', 'Like', '%'.$val.'%')
                  ->orWhere('last_name', 'Like', '%'.$val.'%')
                  ->orWhere('phone_number', 'Like', '%'.$val.'%');
            });

        $data['customers'] = $query->get();

        return $this->sendResponse($data,'Customer Search Data.');
    }
    
    
    public function getOrderSearchData(Request $request) {

        $val = $request->get('val');
        // $storeId = $request->get('store_id');

         $query = Order::query();
        $query = $query->where('order_no','Like','%'.$val.'%');

        $data['orders'] = $query->get();

        return $this->sendResponse($data,'Order Search Data.');
    }

    public function getSpecificCustomerData(Request $request) {

        $customerId = $request->get('customer_id');

        $data['customer'] = Customer::find($customerId);
        $data['totalOrders'] = Order::where('customer_id',$customerId)->count();
        $data['orderAmount'] = Order::where('customer_id',$customerId)->sum(DB::raw('total_amount - return_amount'))  + $data['customer']->opening_balance;
        
        $data['totalPayment'] = CustomerPayment::where('customer_id',$customerId)->sum(DB::raw('amount + discount')) ;
        
        
        $totalBillAmount = Order::where('customer_id',$customerId)->where('status','!=',6)->sum('total_amount');
            $totalReturnAmount = Order::where('customer_id',$customerId)->where('return_type',1)->sum('return_amount');
            
            $totalPayment = CustomerPayment::where('customer_id',$customerId)->where('status',2)->sum('amount');
            $totalDiscount = CustomerPayment::where('customer_id',$customerId)->where('status',2)->sum('discount');
            
            $data['balance'] = (((($data['customer']->opening_balance + $totalBillAmount) - $totalReturnAmount) - $totalPayment) - $totalDiscount);

        return $this->sendResponse($data,'Specific Customer Data.');
    }

    public function createCustomer(Request $request) {

        try {
            $data['request'] = $request;
            $customer = new Customer();
            $customer->first_name = $request->get('first_name');
            $customer->last_name = $request->get('last_name');
            $customer->email = $request->get('email');
            $customer->phone_number = $request->get('phone');
            $customer->dob = $request->get('dob');
            $customer->gender = $request->get('gender');
            $customer->store_id = $request->get('store_id');
            $customer->password = Hash::make($request->get('phone'));

            $customer->save();
        } catch(\Exception $e) {
            return $e->getMessage();
        }

        $data['customer'] = $customer;
        return $this->sendResponse($data,'Customer Created.');
    }

    public function getCustomerSuggestions(Request $request)
    {
        $q = trim($request->get('val', $request->get('q', '')));

        if (strlen($q) < 2) {
            return $this->sendResponse([], 'Customer Suggestions Data.');
        }

        $words = array_filter(explode(' ', $q));

        $query = Customer::where('is_website_customer', 0);

        $query->where(function ($group) use ($q, $words) {
            $group->where(DB::raw("CONCAT(COALESCE(first_name,''), ' ', COALESCE(last_name,''))"), 'LIKE', '%' . $q . '%')
                ->orWhere('first_name', 'LIKE', '%' . $q . '%')
                ->orWhere('last_name', 'LIKE', '%' . $q . '%')
                ->orWhere('phone_number', 'LIKE', '%' . $q . '%')
                ->orWhere('email', 'LIKE', '%' . $q . '%');

            foreach ($words as $word) {
                if (strlen($word) >= 2) {
                    $group->orWhere('first_name', 'LIKE', '%' . $word . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $word . '%')
                        ->orWhereRaw("SOUNDEX(first_name) = SOUNDEX(?)", [$word])
                        ->orWhereRaw("SOUNDEX(last_name) = SOUNDEX(?)", [$word]);
                }
            }
        });

        $customers = $query->limit(10)->get();

        $results = $customers->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => trim($c->first_name . ' ' . $c->last_name),
                'first_name' => $c->first_name,
                'last_name' => $c->last_name,
                'phone' => $c->phone_number,
                'email' => $c->email
            ];
        });

        return $this->sendResponse($results, 'Customer Suggestions Data.');
    }
}

