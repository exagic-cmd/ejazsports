<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
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
            env('API_BASE_URL').'/getReportData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());
        
       

        return view('report.index',$data);
    }

    public function customReport(Request $request) {

        // POST Data
        $postInput = [
            'store_id' => Auth::user()->store_id,
            'option' => $request->option
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getCustomReportData',['form_params' => $postInput, 'headers' => $headers]
        );

        $data['result'] = json_decode($response->getBody());
        dd($data);

        return view('report.update-report',$data);
    }


}
