<?php

namespace App\Http\Controllers\Auth;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {   

        $credentials = $request->validate([
            'phone_number' => 'required',
            'password' => 'required'
        ]);

        if (!$this->guard()->attempt($credentials)) {
            return response()->json([
                'message' => 'The phone number or password is incorrect.'
            ], 500);
        }

        $verified = User::where('phone_number', $request->phone_number)->where('verified', 1)->first();
        $not_verified = User::where('phone_number', $request->phone_number)->where('verified', 0)->first();

        if($verified){
            $response = [
                'verified' => true,
                'access_token' => Auth::user()->createToken('MyApp')->plainTextToken,
                'token_type' => 'Bearer',
                'csrf_token' => csrf_token(),
                'role' => $verified->register_as,
                'id' => $verified->id,
            ];
        } else {
            $response = [
                'verified' => false,
                'message' => 'Account has not been verified yet.',
                'phone_number' => $request->phone_number,
                'access_token' => Auth::user()->createToken('MyApp')->plainTextToken,
                'token_type' => 'Bearer',
                'csrf_token' => csrf_token(),
                'role' => $not_verified->register_as,
                'id' => $not_verified->id,
            ];
        }

        return response()->json($response);   
    }

    public function logout()
    {
        try {
            Session::flush();
            $success = true;
            $message = 'Successfully logged out';
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }

        $response = [
            'success' => $success,
            'message' => $message,
        ];
        return response()->json($response);
    }

    public function requestVerification(Request $request)
    {
        $credentials = $request->validate([
            'phone_number' => 'required'
        ]);

        if (!$credentials) {
            return response()->json();
        } else {
            $user = User::where('phone_number', $request->phone_number)->first();

            if(!empty($user)) {
                $code = random_int(100000, 999999);

                $user->forgot_password = $code;
                $user->update();

                // Semaphore
                $ch = curl_init();
                $parameters = array(
                    'apikey' => env('SEMAPHORE_KEY'), //Your API KEY
                    'number' => $user->phone_number,
                    'message' => "Your Agrabah Logistics Verification code is ".$code.". Enter this to change your password.",
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
                $message = "We have sent you your verification code. Please check your inbox.";
            } else {
                $status = false;
                $message = "Incorrect phone number. Please check your number and try again.";
            }

            return response()->json([
                'message' => $message,
                'status' => $status
            ]);

        }
    }

    public function forgotPassword(Request $request)
    {
        $credentials = $request->validate([
            'phone_number' => 'required',
            'password' => 'required',
            'verification_code' => 'required'
        ]);

        if (!$credentials) {
            return response()->json();
        } else {
            $user = User::where('phone_number', $request->phone_number)->where('forgot_password', $request->verification_code)->first();

            if(!empty($user)) {
                $user->password = Hash::make($request->password);
                $user->forgot_password = null;
                $user->update();

                $status = true;
                $message = "Success! Password has been changed.";
            } else {
                $status = false;
                $message = "Incorrect phone number or verification code. Please check and try again.";
            }

            return response()->json([
                'message' => $message,
                'status' => $status
            ]);

        }
    }

    public function guard($guard = 'web')
    {
        return Auth::guard($guard);
    }
}
