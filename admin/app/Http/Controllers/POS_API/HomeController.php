<?php

namespace App\Http\Controllers\POS_API;

use App\Http\Controllers\POS_API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends BaseController
{

    public function getAgentLogin(Request $request)
    {

        if (!($request->email) || !($request->password)) {
            return $this->sendError('Email or password required', [], 200);
        }

        $data['user'] = User::where('email', $request->email)->first();

        if ($data['user']) {
            if (Hash::check($request->password, $data['user']->password)) {
                if ($data['user']->store_id)
                    return $this->sendResponse($data, 'POS Agent login successfully.');
                else
                    return $this->sendError('Store id not found.', [], 200);
            } else
                return $this->sendError('Email or password not match.', [], 200);
        }
        return $this->sendError('Email not found.', [], 200);
    }
}
