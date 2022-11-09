<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'phone_number' => 'required|unique:users'
        ]);

        if (!$credentials) {

            return response()->json();
        } else {
            // PIN Code
            $pin = random_int(100000, 999999);

            $user = new User();
            $user->phone_number = $request->phone_number;
            $user->password = Hash::make($request->password);
            $user->register_as = $request->register_as;
            $user->pin = $pin;
            $user->save();

            //OTP Sending thru iTextMo
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

            // Show the server response
            // echo $output;

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

            // Login registered user 
            Auth::loginUsingId($user->id);

            return response()->json([
                'message' => 'You are successfully registered. Kindly check your inbox for your verification code.',
                'user' => $user
            ]);

        }
    }

    public function guard($guard = 'web')
    {
        return Auth::guard($guard);
    }
}
