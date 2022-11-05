<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'otp_code' => 'required',
        ]);

        $check_otp = User::where('pin', $request->otp_code)->where('phone_number', $request->phone_number)->first();

        if($check_otp) {
            $check_otp->verified = 1;
            $check_otp->save();

            return response()->json(['success' => true, 'message' => 'Success! Your account is now verified.', 'role' => $check_otp->register_as]);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid, you have entered an incorrect code.']);
        }
    }

    public function resend(Request $request)
    {
        $pin = random_int(100000, 999999);

        $user = User::find($request->user_id);
        $user->pin = $pin;
        $user->update();

        // print_r($user);


        // //OTP Sending thru iTextMo
        // $ch = curl_init();
        // $itexmo = array(
        //     '1' => $user->phone_number,
        //     '2' => "Your Agrabah Logistics One-Time Code is ".$pin.". Enter this to confirm your registration.",
        //     '3' => env('ITEXMO_API_KEY'),
        //     'passwd' => env('ITEXMO_PW')
        // );
        // curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
        // curl_setopt($ch, CURLOPT_POST, 1);

        // //Send the parameters set above with the request
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($itexmo));

        // // Receive response from server
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $output = curl_exec($ch);
        // curl_close ($ch);

        // // Show the server response
        // return($output);

        // $ch = curl_init();
        // $itexmo = array(
        //     '1' => "09932913899",
        //     '2' => "Your Agrabah Logistics One-Time Code is ".$pin.". Enter this to confirm your registration.",
        //     '3' => env('ITEXMO_API_KEY'),
        //     'passwd' => env('ITEXMO_PW')
        // );
        // curl_setopt($ch, CURLOPT_URL,"https://www.itexmo.com/php_api/api.php");
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($itexmo));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $output = curl_exec($ch);
        // curl_close ($ch); 

        $url = 'https://www.itexmo.com/php_api/api.php';
        $itexmo = array(
                '1' => $user->phone_number,
                '2' => "Your Agrabah Logistics One-Time Code is ".$pin.". Enter this to confirm your registration.",
                '3' => env('ITEXMO_API_KEY'),
                'passwd' => env('ITEXMO_PW')
            );
        $param = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($itexmo),
            ),
        );
        $context  = stream_context_create($param);
        $output = file_get_contents($url, false, $context);

        // echo($output);

        if($output == 0) {
            $status = true;
            $message = "Success! Your new pin has been sent.";
        } else {
            $status = false;
            $message = "Error detected! Please wait a few minutes before you try again.";
        }

        return response()->json(['success' => $status, 'message' => $message]);
    }
}
