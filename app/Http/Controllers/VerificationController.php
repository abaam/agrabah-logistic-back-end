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

        // $ch = curl_init();
        // $itexmo = array(
        //     '1' => $user->phone_number,
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

        // // echo($output);

        // if($output == 0) {
        //     $status = true;
        //     $message = "Success! Your new pin has been sent.";
        // } else {
        //     $status = false;
        //     $message = "Error detected! Please wait a few minutes before you try again.";
        // }
        
        // Semaphore
        $ch = curl_init();
        $parameters = array(
            'apikey' => env('SEMAPHORE_KEY'), //Your API KEY
            'number' => $user->phone_number,
            'message' => "Your Agrabah Logistics One-Time Code is ".$pin.". Enter this to confirm your registration.",
            'sendername' => env('SEMAPHORE_SENDER_NAME')
        );
        curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close ($ch);

        //Show the server response
        // echo $output;

        $status = true;
        $message = "Success! Your new pin has been sent. Please check your registered phone number.";

        return response()->json(['success' => $status, 'message' => $message]);
    }
}
