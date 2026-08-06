<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
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
            env('API_BASE_URL').'/getSaleData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return view('sale.index',$data);
    }
    
    
    public function orderDetail(Request $request) {

        // POST Data
        $postInput = [
            'order_no' => $request->order_no
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getOrderDetail',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());
        

        return $data;
    }
    
     public function getHold(Request $request) {

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
            env('API_BASE_URL').'/getHoldList',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return view('sale.hold-list',$data);

    }

    public function getHoldList(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'cart' => $request->cart
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getHoldList',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return view('sale.update-hold-list',$data);

    }

    public function orderInfo(Request $request) {
        // POST Data
        $postInput = [
            'order_id' => $request->order_id
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/orderInfo',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());
        
        

        return view('sale.update-order-info',$data);
    }

    public function getReturnOrders() {

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
            env('API_BASE_URL').'/getReturnOrders',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return view('sale.return-orders',$data);
    }

    public function getSearchOrder(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'order_no' => $request->order_no
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getSearchOrder',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return view('sale.update-search-order',$data);
    }

    public function updateCompleteReturnOrder(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'order_id' => $request->order_id,
            'type' => $request->type
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/updateCompleteReturnOrder',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return view('sale.update-search-order',$data);
    }

    public function updatePartiallyReturnOrder(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'order_id' => $request->order_id,
            'product_ids' => $request->product_ids,
            'return_qty' => $request->return_qty,
            'type' => $request->type
        ];
        // Headers
        $headers = [
            //...
        ];
        
       

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/updatePartiallyReturnOrder',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());
        
       

        return view('sale.update-search-order',$data);
    }
}
