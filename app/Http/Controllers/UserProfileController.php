<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProfile;

class UserProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user_id = Auth::user()->id;
        $profile = UserProfile::where('user_id', $user_id)->first();
        // print_r($profile);
        $profile['mobile_number'] = Auth::user()->phone_number;
        return response()->json($profile);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function storeName(Request $request) {
        $user_id = Auth::user()->id;
        $user_data = $request['user'];
        $user = UserProfile::where('user_id', '=', $user_id)->firstOrFail();
        $profile = UserProfile::find($user->id);
        if(!empty($profile)) {
            $profile->first_name = $user_data['first_name'];
            $profile->middle_name = $user_data['middle_name'] != "" ? $user_data['middle_name'] : "";
            $profile->last_name = $user_data['last_name'];
            $profile->name_extension = $user_data['name_extension'] != "" ? $user_data['name_extension'] : "";
            $profile->update();
        } else {
            $profile->first_name = $user_data['first_name'];
            $profile->middle_name = $user_data['middle_name'] != "" ? $user_data['name_extension'] : "";
            $profile->last_name = $user_data['last_name'];
            $profile->name_extension = $user_data['name_extension'] != "" ? $user_data['name_extension'] : "";
            $profile->photo = $user_data['photo'] != "" ? $user_data['photo'] : "";
            $profile->email = $user_data['email'] != "" ? $user_data['email'] : "";
            $profile->house_number = $user_data['house_number'] != "" ? $user_data['house_number'] : "";
            $profile->street = $user_data['street'] != "" ? $user_data['street'] : "";
            $profile->barangay = $user_data['barangay'] != "" ? $user_data['barangay'] : "";
            $profile->city = $user_data['city'] != "" ? $user_data['city'] : "";
            $profile->province = $user_data['province'] != "" ? $user_data['province'] : "";
            $profile->zip_code = $user_data['zip_code'] != "" ? $user_data['zip_code'] : "";
            $profile->save();
        }
        
        $profile['mobile_number'] = Auth::user()->phone_number;

        return response()->json($profile);
    }

    public function storeEmail(Request $request) {
        $user_id = Auth::user()->id;
        $user_data = $request['user'];
        $user = UserProfile::where('user_id', '=', $user_id)->firstOrFail();
        $profile = UserProfile::find($user->id);
        if(!empty($profile)) {
            $profile->email = $user_data['email'];
            $profile->update();
        } else {
            $profile->first_name = $user_data['first_name'] != "" ? $user_data['first_name'] : "";
            $profile->middle_name = $user_data['middle_name'] != "" ? $user_data['name_extension'] : "";
            $profile->last_name = $user_data['last_name'] != "" ? $user_data['last_name'] : "";
            $profile->name_extension = $user_data['name_extension'] != "" ? $user_data['name_extension'] : "";
            $profile->photo = $user_data['photo'] != "" ? $user_data['photo'] : "";
            $profile->email = $user_data['email'] != "" ? $user_data['email'] : "";
            $profile->house_number = $user_data['house_number'] != "" ? $user_data['house_number'] : "";
            $profile->street = $user_data['street'] != "" ? $user_data['street'] : "";
            $profile->barangay = $user_data['barangay'] != "" ? $user_data['barangay'] : "";
            $profile->city = $user_data['city'] != "" ? $user_data['city'] : "";
            $profile->province = $user_data['province'] != "" ? $user_data['province'] : "";
            $profile->zip_code = $user_data['zip_code'] != "" ? $user_data['zip_code'] : "";
            $profile->save();
        }
        
        $profile['mobile_number'] = Auth::user()->phone_number;

        return response()->json($profile);
    }
}
