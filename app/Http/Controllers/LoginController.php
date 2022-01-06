<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

                'message' => 'The username or password is incorrect.'
            ], 500);
        }

        $token = $this->guard()->user()->createToken('auth-token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function guard($guard = 'web')
    {
        return Auth::guard($guard);
    }
}
