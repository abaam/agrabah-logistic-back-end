<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserProfile;
use App\Models\User;
use Hash;
use App\Http\Resources\CustomersCollection;
use App\Http\Resources\DriversCollection;

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
        // if(empty($profile)) {
            // print_r('test');
            $profile['mobile_number'] = Auth::user()->phone_number;
        // }
        
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
        // print_r("Hello World");
        $user_id = Auth::user()->id;
        $user_data = $request['user'];
        $user = UserProfile::where('user_id', '=', $user_id)->first();
        $profile;

        if(!empty($user)) {
            $profile = UserProfile::find($user->id);
        }

        if(!empty($profile)) {
            $profile->first_name = $user_data['first_name'];
            $profile->middle_name = $user_data['middle_name'] != "" ? $user_data['middle_name'] : "";
            $profile->last_name = $user_data['last_name'];
            $profile->name_extension = $user_data['name_extension'] != "" ? $user_data['name_extension'] : "";
            $profile->update();
        } else {
            $profile = new UserProfile;
            $profile->user_id = $user_id;
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
        $profile;

        if(!empty($user)) {
            $profile = UserProfile::find($user->id);
        }

        if(!empty($profile)) {
            $profile->email = $user_data['email'];
            $profile->update();
        } else {
            $profile = new UserProfile;
            $profile->user_id = $user_id;
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

    public function storeAddress(Request $request) {
        $user_id = Auth::user()->id;
        $user_data = $request['user'];
        $user = UserProfile::where('user_id', '=', $user_id)->firstOrFail();
        $profile;

        if(!empty($user)) {
            $profile = UserProfile::find($user->id);
        }

        if(!empty($profile)) {
            $profile->house_number = $user_data['house_number'] != "" ? $user_data['house_number'] : "";
            $profile->street = $user_data['street'] != "" ? $user_data['street'] : "";
            $profile->barangay = $user_data['barangay'] != "" ? $user_data['barangay'] : "";
            $profile->city = $user_data['city'] != "" ? $user_data['city'] : "";
            $profile->province = $user_data['province'] != "" ? $user_data['province'] : "";
            $profile->zip_code = $user_data['zip_code'] != "" ? $user_data['zip_code'] : "";
            $profile->update();
        } else {
            $profile = new UserProfile;
            $profile->user_id = $user_id;
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

    public function checkProfile()
    {
        $user_id = Auth::user()->id;
        $profile = UserProfile::where('user_id', $user_id)->first();
        
        return response()->json($profile);
    }

    public function changePassword(Request $request) {
        $credentials = $request->validate([
            'new_password' => 'required'
        ]);

        if (!$credentials) {
            return response()->json();
        } else {
            // $credentials = $request['data'];
            // echo($credentials['new_password']);
            $user_id = Auth::user()->id;
            $user = User::where('id', '=', $user_id)->firstOrFail();
            $user->password = Hash::make($request->new_password);
            $user->update();

            $status = true;
            $message = "Success! Password has been changed.";

            return response()->json([
                'message' => $message,
                'status' => $status
            ]);

        }
    }

    public function viewCustomers()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $customers = new CustomersCollection(User::where('register_as', 2)->paginate($page_number));

        $verified = User::where('register_as', 2)->where('verified', 1)->orderBy('created_at', 'ASC')->get();
        $not_verified = User::where('register_as', 2)->where('verified', 0)->orderBy('created_at', 'ASC')->get();

        return response()->json(['customers' => $customers, 'verified' => $verified, 'not_verified' => $not_verified], 200);
    }

    public function viewDrivers()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $drivers = new DriversCollection(User::where('register_as', 1)->paginate($page_number));

        $verified = User::where('register_as', 2)->where('verified', 1)->orderBy('created_at', 'ASC')->get();
        $not_verified = User::where('register_as', 2)->where('verified', 0)->orderBy('created_at', 'ASC')->get();

        return response()->json(['drivers' => $drivers, 'verified' => $verified, 'not_verified' => $not_verified], 200);
    }

    public function search()
    {
        $key = \Request::get('q');
        $entries = \Request::get('entries');
        $page_number = $entries;

        $Customer = User::where('phone_number','LIKE',"%{$key}%")
        ->orWhere('pin','LIKE',"%{$key}%")
        ->orWhereRaw("(CASE WHEN verified = 0 THEN 'Not Verified' WHEN verified = 1 THEN 'verified' END) LIKE '%{$key}%'")
        ->where('register_as', 2)
        ->paginate($page_number);

        $Driver = User::where('phone_number','LIKE',"%{$key}%")
        ->orWhere('pin','LIKE',"%{$key}%")
        ->orWhereRaw("(CASE WHEN verified = 0 THEN 'Not Verified' WHEN verified = 1 THEN 'verified' END) LIKE '%{$key}%'")
        ->where('register_as', 1)
        ->paginate($page_number);

        $customers = new CustomersCollection($Customer);
        $drivers = new DriversCollection($Driver);

        return response()->json(['customers' => $customers, 'drivers' => $drivers], 200);
    }

    public function viewCustomerDetails($id)
    {
        $customer = UserProfile::where('user_id', $id)->first();

        return response()->json(['customer' => $customer], 200);
    }

    public function viewDriverDetails($id)
    {
        $driver = UserProfile::where('user_id', $id)->first();

        return response()->json(['driver' => $driver], 200);
    }

    public function mobileVerification(Request $request)
    {
        $credentials = $request->validate([
            'new_mobile_number' => 'required'
        ]);

        if (!$credentials) {
            return response()->json();
        } else {
            if(Auth::check()) {
                $code = random_int(100000, 999999);
                
                $user = Auth::user();
                $user->mobile_verification_code = $code;
                $user->update();

                // Semaphore
                $ch = curl_init();
                $parameters = array(
                    'apikey' => env('SEMAPHORE_KEY'), //Your API KEY
                    'number' => $request->new_mobile_number,
                    'message' => "Your Agrabah Logistics Verification code is ".$code.". Enter this to change your registered number.",
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
            }

            return response()->json([
                'message' => $message,
                'status' => $status
            ]);

        }
    }

    public function changeMobileNumber(Request $request)
    {
        $requestData = $request->validate([
            'new_mobile_number' => 'required',
            'verification_code' => 'required'
        ]);

        if (!$requestData) {
            return response()->json();
        } else {
            $user = Auth::user();

            if($request->verification_code === $user->mobile_verification_code) {
                $user->phone_number = $request->new_mobile_number;
                $user->mobile_verification_code = null;
                $user->update();

                return response()->json([
                    'message' => "Phone number successfully changed",
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'message' => "Incorrect verification code",
                    'status' => false
                ]);
            }
        }
    }
}
