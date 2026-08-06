<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function login(Request $request) {

        // POST Data
        $postInput = [
            'email' => $request->email,
            'password' => $request->password
        ];
        // Headers
        $headers = [
            //...
        ];

        $http= new Client();
        $response = $http->request('POST',
            env('API_BASE_URL').'/getAgentLogin',['form_params' => $postInput, 'headers' => $headers]
        );

        $result = json_decode($response->getBody());

        if($result->success) {
            $user = User::find($result->data->user->id);
            auth()->login($user);
            return redirect()->route('dashboard')->with(['message' => $result->message,'result' => 'success']);
        }
        else {
            return redirect()->back()->with(['message' => $result->message,'result' => 'success']);
        }
    }

    public function pos() {

        return view('pos.index');
    }
    
    public function qr() {
        return view('qr');
    }
}
