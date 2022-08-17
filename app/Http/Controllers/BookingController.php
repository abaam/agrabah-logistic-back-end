<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingsCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings = new BookingsCollection(Booking::where('payment_status', 0)->orWhere('payment_status', 1)->paginate($page_number));

        $to_ship = Booking::where('status', 3)->where('payment_status', 0)->orWhere('payment_status', 1)->orderBy('date_time', 'ASC')->get();
        $to_receive = Booking::where('status', 2)->where('payment_status', 0)->orWhere('payment_status', 1)->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->where('payment_status', 0)->orWhere('payment_status', 1)->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings' => $bookings, 'to_ship' => $to_ship, 'to_receive' => $to_receive, 'delivered' => $delivered], 200);
    }

    public function transactions()
    {
        $entries = \Request::get('entries');
        $page_number = $entries;
        $bookings = new BookingsCollection(Booking::where('payment_status', 2)->orWhere('payment_status', 3)->paginate($page_number));

        $to_ship = Booking::where('status', 3)->where('payment_status', 2)->orWhere('payment_status', 3)->orderBy('date_time', 'ASC')->get();
        $to_receive = Booking::where('status', 2)->where('payment_status', 2)->orWhere('payment_status', 3)->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->where('payment_status', 2)->orWhere('payment_status', 3)->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings' => $bookings, 'to_ship' => $to_ship, 'to_receive' => $to_receive, 'delivered' => $delivered], 200);
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

        $Booking = Booking::where('package_item','LIKE',"%{$key}%")
        ->orWhere('package_item','LIKE',"%{$key}%")
        ->orWhere('package_quantity','LIKE',"%{$key}%")
        ->orWhere('package_unit','LIKE',"%{$key}%")
        ->orWhere('package_note','LIKE',"%{$key}%")
        ->orWhere('receiver_name','LIKE',"%{$key}%")
        ->orWhere('receiver_contact','LIKE',"%{$key}%")
        ->orWhere('vehicle_type','LIKE',"%{$key}%")
        ->orWhere('drop_off','LIKE',"%{$key}%")
        ->orWhere('date_time','LIKE',"%{$key}%")
        ->orWhereRaw("(CASE WHEN payment_method = 0 THEN 'Paymaya' WHEN payment_method = 1 THEN 'Gcash' END) LIKE '%{$key}%'")
        ->orWhereRaw("(CASE WHEN payment_status = 0 THEN 'Pending' WHEN payment_status = 1 THEN 'Paid' END) LIKE '%{$key}%'")
        ->orWhereRaw("(CASE WHEN status = 1 THEN 'Delivered' WHEN status = 2 THEN 'To Receive' ELSE 'To Ship' END) LIKE '%{$key}%'")
        ->paginate($page_number);

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
}