<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
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
            env('API_BASE_URL').'/getCashierData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('cashier.index',$data);
    }

    public function createClosing(Request $request) {
        // POST Data
        $postInput = [
            'store_id' => $request->store_id,
            '5_amount' => $request->get('5_amount'),
            '10_amount' => $request->get('10_amount'),
            '20_amount' => $request->get('20_amount'),
            '50_amount' => $request->get('50_amount'),
            '100_amount' => $request->get('100_amount'),
            '500_amount' => $request->get('500_amount'),
            '1000_amount' => $request->get('1000_amount'),
            '5000_amount' => $request->get('5000_amount'),
            'note' => $request->note
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/createClosing',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());



        return redirect()->route('cashier.data');
    }


}
