<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
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
            env('API_BASE_URL').'/getExpenseData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());


        return view('expense.index',$data);
    }

    public function createExpense(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => $request->store_id,
            'category_id' => $request->category_id,
            'bill_no' => $request->bill_no,
            'amount' => $request->amount,
            'date' => $request->date,
            'detail' => $request->detail
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/createExpense',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());

        return redirect()->route('expense.data');
    }


}
