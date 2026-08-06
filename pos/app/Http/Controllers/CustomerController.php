<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index() {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getCustomerData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return view('customer.index',$data);
    }

    public function getCustomerSearchData(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'val' => $request->val
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getCustomerSearchData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('customer.update-customer-div',$data);
    }

    public function getCustomerSuggestions(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'val' => $request->val
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getCustomerSuggestions',['form_params' => $postInput, 'headers' => $headers]
        );

        return response()->json(json_decode($response->getBody()));
    }

    
    public function getOrderSearchData(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'val' => $request->val
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getOrderSearchData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('sale.update-order-div',$data);
    }

    public function getSpecificCustomerData(Request $request) {

        // POST Data
        $postInput = [
            'customer_id' => $request->customer_id
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getSpecificCustomerData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('customer.update-customer-detail',$data);
    }

    public function createCustomer(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => $request->store_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'dob' => $request->dob,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/createCustomer',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return redirect()->route('customer.data');
    }


}
