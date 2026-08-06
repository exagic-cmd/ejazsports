<?php

namespace App\Http\Controllers;

use App\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index() {

        // POST Data
        $postInput = [
            // 'store_id' => Auth::user()->store_id
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getProductData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('product.index',$data);
    }

    public function outOfStockProducts() {

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
            env('API_BASE_URL').'/getOutOfStockProductData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('product.out-of-stock',$data);
    }

    public function productDetail(Request $request) {

        // POST Data
        $postInput = [
             'store_id' => Auth::user()->store_id,
            'product_id' => $request->product_id
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getProductDetailPOS',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('product.product-detail',$data);
    }
    
     public function productVariantDetail(Request $request) {

        // POST Data
        $postInput = [
             'store_id' => Auth::user()->store_id,
            'variant_id' => $request->variant_id
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getProductVariantDetailPOS',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('product.product-detail',$data);
    }


}
