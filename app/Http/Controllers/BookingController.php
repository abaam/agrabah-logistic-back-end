<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingsCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Booking;
use App\Models\Sale;
use App\Models\Delivery;
use App\Models\TrackingUpdate;
use App\Models\Tracking;
use App\Models\UserProfile;

class BookingController extends Controller
{
    public function index()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings_customer = new BookingsCollection(Booking::whereIn('payment_status', [0, 1])->paginate($page_number));
        $bookings_driver = new BookingsCollection(Booking::where('payment_status', 2)->paginate($page_number));
        $bookings_admin = new BookingsCollection(Booking::where('payment_status', '!=', 1)->paginate($page_number));

        $to_ship = Booking::where('status', [3, 5])->whereIn('payment_status', [0, 1])->orderBy('date_time', 'ASC')->get();
        $to_receive = Booking::where('status', 2)->whereIn('payment_status', [0, 1])->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->whereIn('payment_status', [0, 1])->orderBy('date_time', 'ASC')->get();

        $to_ship_admin = Booking::where('status', [3, 5])->where('payment_status', 0)->orderBy('date_time', 'ASC')->get();
        $to_receive_admin = Booking::where('status', 2)->where('payment_status', 0)->orderBy('date_time', 'ASC')->get();
        $delivered_admin = Booking::where('status', 1)->where('payment_status', 0)->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings_customer' => $bookings_customer, 'bookings_driver' => $bookings_driver, 'bookings_admin' => $bookings_admin, 'to_ship' => $to_ship, 'to_receive' => $to_receive, 'delivered' => $delivered, 'to_ship_admin' => $to_ship_admin, 'to_receive_admin' => $to_receive_admin, 'delivered_admin' => $delivered_admin], 200);
    }

    public function transactions()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings = new BookingsCollection(Booking::whereIn('payment_status', [2, 3])->paginate($page_number));

        $to_ship = Booking::where('status', [3, 5])->whereIn('payment_status', [2, 3])->orderBy('date_time', 'ASC')->get();
        $to_receive = Booking::where('status', 2)->whereIn('payment_status', [2, 3])->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->whereIn('payment_status', [2, 3])->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings' => $bookings, 'to_ship' => $to_ship, 'to_receive' => $to_receive, 'delivered' => $delivered], 200);
    }

    public function pendingApproval()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings = new BookingsCollection(Booking::where('payment_status', 1)->paginate($page_number));

        return response()->json(['bookings' => $bookings], 200);
    }

    public function bookingDetails($id)
    {
        $booking = Booking::where('booking_id', $id)->first();
        return response()->json($booking);
    }

    public function search()
    {
        $key = \Request::get('q');
        $entries = \Request::get('entries');
        $page_number = $entries;
        $page = \Request::get('page');
        $role = \Request::get('role');

        $Booking = Booking::where('package_item','LIKE',"%{$key}%")
        ->orWhere('package_quantity','LIKE',"%{$key}%")
        ->orWhere('package_unit','LIKE',"%{$key}%")
        ->orWhere('package_note','LIKE',"%{$key}%")
        ->orWhere('receiver_name','LIKE',"%{$key}%")
        ->orWhere('receiver_contact','LIKE',"%{$key}%")
        ->orWhere('vehicle_type','LIKE',"%{$key}%")
        ->orWhere('drop_off','LIKE',"%{$key}%")
        ->orWhere('pick_up','LIKE',"%{$key}%")
        ->orWhere('date_time','LIKE',"%{$key}%")
        ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' END) LIKE '%{$key}%'")
        ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
        ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' WHEN status = 3 THEN 'To Ship' ELSE 'Cancelled' END) LIKE '%{$key}%'")
        ->paginate($page_number);

        if ($page == "booking") {
            if ($role == "customer") {
                $Booking->whereIn('payment_status', [0, 1]);
            } else if ($role == "driver") {
                $Booking->whereIn('payment_status', 2);
            } else if ($role == "admin") {
                $Booking->whereIn('payment_status', '!=', 1);
            }
        }

        if ($page == "transaction") {
            $Booking->whereIn('payment_status', [2, 3]);
        }

        if ($page == "pending_approval") {
            $Booking->where('payment_status', 1)->where('status', '!=', 4);
        }

        $bookings = new BookingsCollection($Booking);

        return response()->json(['bookings' => $bookings], 200);
    }

    public function store(Request $request)
    {   
        $input_names = array_column($request['booking_form'], 0);
        $booking_form = array_combine($input_names, $request['booking_form']);

        $booking = new Booking();
        $booking->booking_id = $booking_form['booking_id'][1];
        $booking->package_item = $booking_form['package_item'][1];
        $booking->package_quantity = $booking_form['package_quantity'][1];
        $booking->package_unit = $booking_form['package_unit'][1];
        $booking->package_note = $booking_form['package_note'][1];
        $booking->receiver_name = $booking_form['receiver_name'][1];
        $booking->receiver_contact = $booking_form['contact_number'][1];
        $booking->vehicle_type = $booking_form['vehicle_form'][1];
        $booking->pick_up = $booking_form['pick_up'][1];
        $booking->drop_off = $booking_form['drop_off'][1];
        $booking->date_time = date('F j, Y h:i A', strtotime($booking_form['date_time'][1]));

        if ($booking_form['payment_method'][1] == 'Paymaya') {
            $payment_method = 0;
        }else{
            $payment_method = 1;
        }

        $booking->payment_total = $booking_form['payment_total'][1];
        $booking->payment_method = $payment_method;
        $booking->payment_status = 0;
        $booking->status = 3;

        $booking->save();

        return response()->json('You have successfully booked a delivery.');
    }

    public function payBooking(Request $request)
    {   
        Booking::where('booking_id', $request['booking_id'])->update([
            'payment_status' => 1
        ]);

        return response()->json('Wait for your payment approval. Thank you!');
    }

    public function cancelBooking(Request $request)
    {   
        Booking::where('booking_id', $request['booking_id'])->update([
            'payment_status' => 3,
            'status' => 4
        ]);

        return response()->json('Your booking has been cancelled.');
    }

    public function approvePayment(Request $request)
    {   
        $booking = new Sale();
        $booking->booking_id = $request['booking_id'];
        $booking->first_name = $request['first_name'];
        $booking->last_name = $request['last_name'];
        $booking->amount = $request['amount'];
        $booking->ref_number = $request['ref_number'];
        
        $booking->save();

        Booking::where('booking_id', $request['booking_id'])->update([
            'payment_status' => 2
        ]);

        return response()->json('The payment has been approved.');
    }

    public function acceptBooking(Request $request)
    {   
        Sale::where('booking_id', $request['booking_id'])->update([
            'driver_id' => $request['driver_id']
        ]);

        Booking::where('booking_id', $request['booking_id'])->update([
            'status' => 5
        ]);

        $delivery = new Delivery();
        $delivery->booking_id = $request['booking_id'];
        $delivery->driver_id = $request['driver_id'];
        
        $delivery->save();

        return response()->json('Booking has been successfully accepted.');
    }

    public function updateTracking(Request $request)
    {   
        $tracking_id = mt_rand(10000000,99999999);
        $driver = UserProfile::where('user_id', $request['driver_id'])->first();
        if ($driver) {
            $driver_name = $driver->first_name . ' ' . $driver->middle_name . ' ' . $driver->last_name;
        } else {
            $driver_name = 'N/A';
        }

        // $trackingupdate = new TrackingUpdate();
        // $trackingupdate->tracking_id = $tracking_id;
        // $trackingupdate->booking_id = $request['booking_id'];
        // $trackingupdate->driver_name = $driver_name;
        // $trackingupdate->receiver_name = $request['receiver_name'];
        // $trackingupdate->tracking_status = $request['tracking_status'];
        // $trackingupdate->location = $request['location'];
        // $trackingupdate->save();
        
        $tracking = new Tracking();
        $tracking->tracking_id = $tracking_id;
        $tracking->booking_id = $request['booking_id'];
        $tracking->url = $request['url'] .'/tracking/'. $tracking_id;
        $tracking->save();


        // Tracking::where('booking_id', $request['booking_id'])->update([
        //     'driver_id' => $request['driver_id']
        // ]);

        return response()->json('Tracking has been successfully updated.');
    }
}