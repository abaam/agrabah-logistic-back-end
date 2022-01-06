<?php

namespace App\Http\Controllers;

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
            $user = new User();
            $user->phone_number = $request->phone_number;
            $user->password = Hash::make($request->password);
            $user->register_as = $request->register_as;
            $user->save();

            return response()->json([
                'message' => 'You are successfully registered. Kindly check your inbox for your verification code.'
            ]);

        }
    }

    public function guard($guard = 'web')
    {
        return Auth::guard($guard);
    }
}
