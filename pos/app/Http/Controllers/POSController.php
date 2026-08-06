<?php

namespace App\Http\Controllers;

use App\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class POSController extends Controller
{
    public function index()
    {

        $postInput = [];
        $headers = [];

        $http = new Client();
        $response = $http->request('POST', env('API_BASE_URL').'/getPOSModuleData', [
            'form_params' => $postInput,
            'headers' => $headers
        ]);

        $data['result'] = json_decode($response->getBody());
        return view('pos.index', $data);
    }

    public function getBrandData(Request $request)
    {
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'brand_id' => $request->brand_id
        ];
        $headers = [];

        $http = new Client();
        $response = $http->request('POST', env('API_BASE_URL').'/getBrandData', [
            'form_params' => $postInput,
            'headers' => $headers
        ]);

        $data['result'] = json_decode($response->getBody());
        return view('pos.update-products-div', $data);
    }

    public function getCategoryData(Request $request)
    {
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'category_id' => $request->category_id,
            'customer_id' => $request->customer_id
        ];
        $headers = [];

        $http = new Client();
        $response = $http->request('POST', env('API_BASE_URL').'/getCategoryData', [
            'form_params' => $postInput,
            'headers' => $headers
        ]);

        $data['result'] = json_decode($response->getBody());

        if ($request->route == 1) {
            return view('pos.update-category-div', $data);
        }
        return view('pos.update-products-div', $data);
    }

    public function getSearchData(Request $request)
    {
        $postInput = [
            'val' => $request->val,
            'customer_id' => $request->customer_id
        ];
        $headers = [];

        $http = new Client();
        $response = $http->request('POST', env('API_BASE_URL').'/getSearchData', [
            'form_params' => $postInput,
            'headers' => $headers
        ]);

        $data['result'] = json_decode($response->getBody());

        if ($request->route == 1) {
            return view('pos.search-product', $data);
        }
        return view('pos.update-products-div', $data);
    }

   public function getCartData(Request $request)
{
    $postInput = [
        'store_id' => Auth::user()->store_id,
        'cart' => $request->cart ?? [],
        'discount_id' => $request->discount_id,
        'bundles' => $request->bundles ?? [], // Ensure bundles is always an array
        'customer_id' => $request->customer_id,
        'manual_return_only' => $request->manual_return_only ?? 0
    ];

    $headers = [];

    $http = new Client();
    $response = $http->request('POST', env('API_BASE_URL').'/getCartData', [
        'form_params' => $postInput,
        'headers' => $headers
    ]);

    $data['result'] = json_decode($response->getBody());

    if ($data['result']->success == false) {
        return response()->json(['error' => $data['result']->message], 400);
    }

    return view('pos.update-cart-div', $data);
}
    public function updatePayment(Request $request)
    {
        $postInput = [
            'customer_id' => $request->customer_id,
            'discount_id' => $request->discount_id,
            'cart' => $request->cart,
            'bundles' => $request->bundles,
            'store_id' => Auth::user()->store_id
        ];
        $headers = [];

        $http = new Client();
        $response = $http->request('POST', env('API_BASE_URL').'/updatePayment', [
            'form_params' => $postInput,
            'headers' => $headers
        ]);

        $data['result'] = json_decode($response->getBody());
        return view('pos.update-payment', $data);
    }

    public function mannualReturnForm(Request $request)
    {
        $postInput = [
            'customer_id' => $request->customer_id ?? 1,
            'discount_id' => $request->discount_id,
            'cart' => $request->cart ?? [],
            'bundles' => $request->bundles ?? [],
            'store_id' => Auth::user()->store_id,
            'manual_return_only' => $request->manual_return_only ?? 0
        ];
        $headers = [];

        $http = new Client();
        $response = $http->request('POST', env('API_BASE_URL').'/mannualReturnForm', [
            'form_params' => $postInput,
            'headers' => $headers
        ]);

        $data['result'] = json_decode($response->getBody());
        return view('pos.mannual-return', $data);
    }

   public function createSale(Request $request)
    {
        $postInput = [
            'customer_id' => $request->customer_id,
            'discount_id' => $request->discount_id,
            'employee_id' => $request->employee_id,
            'cart' => $request->cart ?? [],
            'bundles' => $request->bundles ?? [],
            'comment' => $request->comment,
            'store_id' => Auth::user()->store_id ?? 1,
            'margin' => $request->margin,
            'pay_amount' => $request->pay_amount,
            'order_id' => $request->order_id ?? 0,
            'sub_total' => $request->sub_total
        ];
        $headers = [];

        try {
            $http = new Client();
            $response = $http->request('POST', env('API_BASE_URL').'/createSale', [
                'form_params' => $postInput,
                'headers' => $headers,
                'timeout' => 30 // Add timeout to prevent hanging
            ]);

            $result = json_decode($response->getBody(), true);
            Log::info('createSale API response', ['result' => $result]);

            if (!isset($result['success']) || !isset($result['data']['order']['id'])) {
                Log::error('Invalid API response structure', ['result' => $result]);
                throw new \Exception('Invalid response from API');
            }

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Order placed successfully',
                'data' => [
                    'order' => $result['data']['order'],
                    'order_id' => $result['data']['order']['id']
                ]
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Log::error('createSale API request failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $postInput
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: API request error',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::error('createSale failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $postInput
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function createReturn(Request $request)
    {
        $postInput = [
            'customer_id' => $request->customer_id ?? 1,
            'discount_id' => $request->discount_id,
            'employee_id' => $request->employee_id,
            'cart' => $request->cart ?? [],
            'bundles' => $request->bundles ?? [],
            'comment' => $request->comment,
            'store_id' => 1,
            'margin' => $request->margin,
            'return_type' => $request->return_type,
            'adjust_type' => $request->adjust_type,
            'mannual_return' => $request->mannual_return,
            'manual_return_only' => $request->manual_return_only ?? 0
        ];
        $headers = [];

        $http = new Client();
        $response = $http->request('POST', env('API_BASE_URL').'/createReturn', [
            'form_params' => $postInput,
            'headers' => $headers
        ]);

        $data['result'] = json_decode($response->getBody());
        return $data['result']->data->order->id;
    }
    public function printOrder($id)
    {
        try {
            $postInput = ['order_id' => $id];
            $headers = [];
            $http = new Client();
            $response = $http->request('POST', env('API_BASE_URL').'/orderInfo', [
                'form_params' => $postInput,
                'headers' => $headers
            ]);
            $responseData = json_decode($response->getBody());
            // dd( $responseData);
            if (!$responseData || !isset($responseData->data) || !isset($responseData->data->order)) {
                throw new \Exception('Invalid order data received from API');
            }
            $data['result'] = $responseData;
            // dd($data);
            return view('pos.print', $data);
        } catch (\Exception $e) {
            Log::error('Error printing order', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load order details: ' . $e->getMessage());
        }
    }
}
