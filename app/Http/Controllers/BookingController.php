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
        $bookings_customer = new BookingsCollection(Booking::whereIn('status', [2, 3, 5])->whereIn('payment_status', [0, 1, 2])->paginate($page_number));
        $bookings_driver = new BookingsCollection(Booking::whereIn('status', [3])->whereIn('payment_status', [2])->orWhere('payment_method', [2])->paginate($page_number));
        $bookings_admin = new BookingsCollection(Booking::whereIn('status', [2, 3, 5])->whereIn('payment_status', [0, 2])->paginate($page_number));

        $for_pick_up = Booking::whereIn('status', [3, 5])->whereIn('payment_status', [0, 1, 2])->orderBy('date_time', 'ASC')->get();
        $to_receive = Booking::where('status', 2)->whereIn('payment_status', [0, 1, 2])->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->whereIn('payment_status', [0, 1, 2])->orderBy('date_time', 'ASC')->get();

        $for_pick_up_driver = Booking::whereIn('status', [3])->whereIn('payment_status', [2, 0])->orderBy('date_time', 'ASC')->get();

        $for_pick_up_admin = Booking::whereIn('status', [2, 3, 5])->whereIn('payment_status', [0, 2])->orderBy('date_time', 'ASC')->get();
        $to_receive_admin = Booking::where('status', 2)->whereIn('payment_status', [0, 1, 2])->orderBy('date_time', 'ASC')->get();
        $delivered_admin = Booking::where('status', 1)->where('payment_status', 0)->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings_customer' => $bookings_customer, 'bookings_driver' => $bookings_driver, 'bookings_admin' => $bookings_admin, 'for_pick_up' => $for_pick_up, 'to_receive' => $to_receive, 'delivered' => $delivered, 'for_pick_up_driver' => $for_pick_up_driver, 'for_pick_up_admin' => $for_pick_up_admin, 'to_receive_admin' => $to_receive_admin, 'delivered_admin' => $delivered_admin], 200);
    }

    public function transactions()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings = new BookingsCollection(Booking::where('status', 1)->whereIn('payment_status', [2, 3])->paginate($page_number));

        $to_receive = Booking::where('status', 2)->whereIn('payment_status', [2, 0])->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->whereIn('payment_status', [2])->orderBy('date_time', 'ASC')->get();
        $cancelled = Booking::where('status', 4)->whereIn('payment_status', [3])->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings' => $bookings, 'to_receive' => $to_receive, 'delivered' => $delivered, 'cancelled' => $cancelled], 200);
    }

    public function deliveries()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings = new BookingsCollection(Booking::whereIn('status', [1, 2, 4, 5])->whereIn('payment_status', [0, 2, 3])->paginate($page_number));

        $to_receive = Booking::whereIn('status', [2, 5])->whereIn('payment_status', [2, 0])->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->whereIn('payment_status', [2])->orderBy('date_time', 'ASC')->get();
        $cancelled = Booking::where('status', 4)->whereIn('payment_status', [3])->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings' => $bookings, 'to_receive' => $to_receive, 'delivered' => $delivered, 'cancelled' => $cancelled], 200);
    }

    public function pendingApproval()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings = new BookingsCollection(Booking::where('payment_status', 1)->where('payment_method', '!=', 2)->paginate($page_number));

        $for_pick_up = Booking::where('status', [3, 5])->whereIn('payment_status', [1])->orderBy('date_time', 'ASC')->get();
        $to_receive = Booking::where('status', 2)->whereIn('payment_status', [1])->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->whereIn('payment_status', [1])->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings' => $bookings, 'for_pick_up' => $for_pick_up, 'to_receive' => $to_receive, 'delivered' => $delivered], 200);
    }

    public function bookingDetails($id)
    {
        $booking = Booking::where('booking_id', $id)->first();
        $tracking = Tracking::where('booking_id', $id)->first();

        if ($tracking) {
            $qr_code = $tracking;
        } else {
            $qr_code = 'https://example.com';
        }

        if ($booking->tracking_id) {
            $booking_has_tracking = 'true';
        } else {
            $booking_has_tracking = 'false';
        }

        return response()->json(['booking' => $booking, 'qr_code' => $qr_code, 'booking_has_tracking' => $booking_has_tracking]);
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
        ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' WHEN payment_method = 2 THEN 'Cash On Delivery' END) LIKE '%{$key}%'")
        ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
        ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' WHEN status = 3 THEN 'For Pickup' ELSE 'Cancelled' END) LIKE '%{$key}%'")
        ->paginate($page_number);

        if ($page == "booking") {
            if ($role == 1) {
                $Booking = Booking::where('package_item','LIKE',"%{$key}%")
                ->whereIn('status', [3])
                ->whereIn('payment_status', [0, 2])
                ->orWhere('package_quantity','LIKE',"%{$key}%")
                ->orWhere('package_unit','LIKE',"%{$key}%")
                ->orWhere('package_note','LIKE',"%{$key}%")
                ->orWhere('receiver_name','LIKE',"%{$key}%")
                ->orWhere('receiver_contact','LIKE',"%{$key}%")
                ->orWhere('vehicle_type','LIKE',"%{$key}%")
                ->orWhere('drop_off','LIKE',"%{$key}%")
                ->orWhere('pick_up','LIKE',"%{$key}%")
                ->orWhere('date_time','LIKE',"%{$key}%")
                ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' WHEN payment_method = 2 THEN 'Cash On Delivery' END) LIKE '%{$key}%'")
                ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
                ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' WHEN status = 3 THEN 'For Pickup' ELSE 'Cancelled' END) LIKE '%{$key}%'")
                ->paginate($page_number);
            } else if ($role == 2) {
                $Booking = Booking::where('package_item','LIKE',"%{$key}%")
                ->whereIn('status', [2, 3, 5])
                ->whereIn('payment_status', [0, 1, 2])
                ->orWhere('package_quantity','LIKE',"%{$key}%")
                ->orWhere('package_unit','LIKE',"%{$key}%")
                ->orWhere('package_note','LIKE',"%{$key}%")
                ->orWhere('receiver_name','LIKE',"%{$key}%")
                ->orWhere('receiver_contact','LIKE',"%{$key}%")
                ->orWhere('vehicle_type','LIKE',"%{$key}%")
                ->orWhere('drop_off','LIKE',"%{$key}%")
                ->orWhere('pick_up','LIKE',"%{$key}%")
                ->orWhere('date_time','LIKE',"%{$key}%")
                ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' WHEN payment_method = 2 THEN 'Cash On Delivery' END) LIKE '%{$key}%'")
                ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
                ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' WHEN status = 3 THEN 'For Pickup' ELSE 'Cancelled' END) LIKE '%{$key}%'")
                ->paginate($page_number);
            } else if ($role == 3) {
                $Booking = Booking::where('package_item','LIKE',"%{$key}%")
                ->whereIn('status', [2, 3, 5])
                ->whereIn('payment_status', [0, 2])
                ->orWhere('package_quantity','LIKE',"%{$key}%")
                ->orWhere('package_unit','LIKE',"%{$key}%")
                ->orWhere('package_note','LIKE',"%{$key}%")
                ->orWhere('receiver_name','LIKE',"%{$key}%")
                ->orWhere('receiver_contact','LIKE',"%{$key}%")
                ->orWhere('vehicle_type','LIKE',"%{$key}%")
                ->orWhere('drop_off','LIKE',"%{$key}%")
                ->orWhere('pick_up','LIKE',"%{$key}%")
                ->orWhere('date_time','LIKE',"%{$key}%")
                ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' WHEN payment_method = 2 THEN 'Cash On Delivery' END) LIKE '%{$key}%'")
                ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
                ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' WHEN status = 3 THEN 'For Pickup' ELSE 'Cancelled' END) LIKE '%{$key}%'")
                ->paginate($page_number);
            }
        }

        if ($page == "transaction") {
            $Booking = Booking::where('package_item','LIKE',"%{$key}%")
            ->where('status', 1)
            ->whereIn('payment_status', [2, 3])
            ->orWhere('package_quantity','LIKE',"%{$key}%")
            ->orWhere('package_unit','LIKE',"%{$key}%")
            ->orWhere('package_note','LIKE',"%{$key}%")
            ->orWhere('receiver_name','LIKE',"%{$key}%")
            ->orWhere('receiver_contact','LIKE',"%{$key}%")
            ->orWhere('vehicle_type','LIKE',"%{$key}%")
            ->orWhere('drop_off','LIKE',"%{$key}%")
            ->orWhere('pick_up','LIKE',"%{$key}%")
            ->orWhere('date_time','LIKE',"%{$key}%")
            ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' WHEN payment_method = 2 THEN 'Cash On Delivery' END) LIKE '%{$key}%'")
            ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
            ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' WHEN status = 3 THEN 'For Pickup' ELSE 'Cancelled' END) LIKE '%{$key}%'")
            ->paginate($page_number);
        }

        if ($page == "pending_approval") {
            $Booking = Booking::where('package_item','LIKE',"%{$key}%")
            ->where('payment_status', 1)
            ->where('payment_method', '!=', 2)
            ->orWhere('package_quantity','LIKE',"%{$key}%")
            ->orWhere('package_unit','LIKE',"%{$key}%")
            ->orWhere('package_note','LIKE',"%{$key}%")
            ->orWhere('receiver_name','LIKE',"%{$key}%")
            ->orWhere('receiver_contact','LIKE',"%{$key}%")
            ->orWhere('vehicle_type','LIKE',"%{$key}%")
            ->orWhere('drop_off','LIKE',"%{$key}%")
            ->orWhere('pick_up','LIKE',"%{$key}%")
            ->orWhere('date_time','LIKE',"%{$key}%")
            ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' WHEN payment_method = 2 THEN 'Cash On Delivery' END) LIKE '%{$key}%'")
            ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
            ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' WHEN status = 3 THEN 'For Pickup' ELSE 'Cancelled' END) LIKE '%{$key}%'")
            ->paginate($page_number);
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
        $booking->pick_up_complete_address = $booking_form['pick_up_complete_address'][1];
        $booking->drop_off = $booking_form['drop_off'][1];
        $booking->drop_off_complete_address = $booking_form['drop_off_complete_address'][1];
        $booking->date_time = date('F j, Y h:i A', strtotime($booking_form['date_time'][1]));

        if ($booking_form['payment_method'][1] == 'Paymaya') {
            $payment_method = 0;
        } else if ($booking_form['payment_method'][1] == 'GCash') {
            $payment_method = 1;
        } else {
            $payment_method = 2;
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

        if ($request['payment_method'] == 'Paymaya') {
            $payment_method = 0;
        } else if ($request['payment_method'] == 'GCash') {
            $payment_method = 1;
        } else {
            $payment_method = 2;
        }

        $sale = new Sale();
        $sale->booking_id = $request['booking_id'];
        $sale->full_name = $request['full_name'];
        $sale->mobile_number = $request['mobile_number'];
        $sale->amount = $request['amount'];
        $sale->ref_number = $request['ref_number'];
        $sale->payment_method = $payment_method;
        
        $sale->save();

        return response()->json('Wait for your payment approval. Thank you!');
    }

    public function cancelBooking(Request $request)
    {   
        $driver = UserProfile::where('user_id', $request['driver_id'])->first();

        if ($driver) {
            $driver_name = $driver->first_name . ' ' . $driver->middle_name . ' ' . $driver->last_name;
        } else {
            $driver_name = 'N/A';
        }

        Booking::where('booking_id', $request['booking_id'])->update([
            'payment_status' => 3,
            'status' => 4,
            'driver_name' => $driver_name
        ]);

        return response()->json('Your booking has been cancelled.');
    }

    public function approvePayment(Request $request)
    {   
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

        $driver = UserProfile::where('user_id', $request['driver_id'])->first();

        if ($driver) {
            $driver_name = $driver->first_name . ' ' . $driver->middle_name . ' ' . $driver->last_name;
        } else {
            $driver_name = 'N/A';
        }

        if ($request['payment_method'] != 'Cash On Delivery') {
            Booking::where('booking_id', $request['booking_id'])->update([
                'driver_name' => $driver_name
            ]);
        }
        
        $delivery->save();

        return response()->json('Booking has been successfully accepted.');
    }

    public function updateTracking(Request $request)
    {   
        $driver = UserProfile::where('user_id', $request['driver_id'])->first();
        $tracking = Tracking::where('booking_id', $request['booking_id'])->first();

        if ($driver) {
            $driver_name = $driver->first_name . ' ' . $driver->middle_name . ' ' . $driver->last_name;
        } else {
            $driver_name = 'N/A';
        }

        if ($request['location']) {
            $location = $request['location'];
        } else {
            if ($request['tracking_status'] == 'Item has been picked up by our driver') {
                $location = $request['pick_up_location'];
            } else if ($request['tracking_status'] == 'Item has been delivered') {
                $location = $request['drop_off_location'];
            } else {
                $location = 'N/A';
            }
        }

        if ($tracking) {
            $tracking_id = $tracking->tracking_id;

            Tracking::where('tracking_id', $tracking_id)->update([
                'tracking_id' => $tracking_id
            ]);
        } else {
            $tracking_id = mt_rand(100000000000,999999999999);

            $tracking = new Tracking();
            $tracking->tracking_id = $tracking_id;
            $tracking->booking_id = $request['booking_id'];
            $tracking->url = $request['url'] .'/tracking/'. $tracking_id;
            $tracking->save();
        }

        $tracking_update = new TrackingUpdate();
        $tracking_update->tracking_id = $tracking_id;
        $tracking_update->booking_id = $request['booking_id'];
        $tracking_update->driver_name = $driver_name;
        $tracking_update->receiver_name = $request['receiver_name'];
        $tracking_update->tracking_status = $request['tracking_status'];
        $tracking_update->location = $location;
        $tracking_update->save();


        Booking::where('booking_id', $request['booking_id'])->update([
            'tracking_id' => $tracking_id
        ]);

        if ($request['tracking_status'] == 'Item has been delivered') {
            Booking::where('booking_id', $request['booking_id'])->update([
                'status' => 1
            ]);
        } else {
            Booking::where('booking_id', $request['booking_id'])->update([
                'status' => 2
            ]);
        }

        if ($request['payment_method'] == 'Paymaya') {
            $payment_method = 0;
        } else if ($request['payment_method'] == 'GCash') {
            $payment_method = 1;
        } else {
            $payment_method = 2;
        }

        if ($request['tracking_status'] == 'Item has been delivered' && $request['payment_method'] == 'Cash On Delivery') {
            $sale = new Sale();
            $sale->booking_id = $request['booking_id'];
            $sale->driver_id = $request['driver_id'];
            $sale->amount = $request['amount'];
            $sale->payment_method = $payment_method;
            $sale->save();

            Booking::where('booking_id', $request['booking_id'])->update([
                'payment_status' => 2
            ]);
        }

        if ($request['tracking_status'] == 'Item has been delivered' && $request['payment_method'] != 'Cash On Delivery') {
            Booking::where('booking_id', $request['booking_id'])->update([
                'driver_name' => $driver_name
            ]);
        }

        return response()->json('Tracking has been successfully updated.');
    }

    public function paymentDetails($id)
    {
        $sale = Sale::where('booking_id', $id)->first();

        return response()->json(['sale' => $sale]);
    }
}