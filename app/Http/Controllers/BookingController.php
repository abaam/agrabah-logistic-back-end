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
        $bookings = new BookingsCollection(Booking::paginate($page_number));

        $to_ship = Booking::where('status', 3)->orderBy('date_time', 'ASC')->get();
        $to_receive = Booking::where('status', 2)->orderBy('date_time', 'ASC')->get();
        $delivered = Booking::where('status', 1)->orderBy('date_time', 'ASC')->get();

        return response()->json(['bookings' => $bookings, 'to_ship' => $to_ship, 'to_receive' => $to_receive, 'delivered' => $delivered], 200);
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
        dd($request);
        return response()->json(['success'=>'You have successfully create a booking.']);
    }
}