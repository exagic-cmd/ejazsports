<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SmsController extends Controller
{



    public static function sendSMS($number, $message)
    {
        $result = self::callAPI($number,$message);
        return $result;
    }



    public static function callAPI($number,$message)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://brandyourtext.com/sms/api/send?username=vegaspk&password=786786&mask=VEGAS%20PK&mobile=' . $number . '&message=' .urlencode($message),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: ci_session=a0bffac4aa4964803ac40da723073cdd6e93b067'
            ),
        ));

        // dd($curl);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;

    }

}
