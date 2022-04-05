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
}
