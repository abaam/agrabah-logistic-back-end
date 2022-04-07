<?php

namespace App\Http\Controllers\Auth;

use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

        $token = $this->guard()->user()->createToken('auth-token')->plainTextToken;
        $verified = User::where('phone_number', $request->phone_number)->where('verified', 1)->first();
        $not_verified = User::where('phone_number', $request->phone_number)->where('verified', 0)->first();

        if($verified){
            $response = [
                'verified' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => $verified->register_as,
            ];
        } else {
            $response = [
                'verified' => false,
                'message' => 'Account has not been verified yet.',
                'phone_number' => $request->phone_number,
                'access_token' => $token,
                'token_type' => 'Bearer',
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

    public function guard($guard = 'web')
    {
        return Auth::guard($guard);
    }
}
