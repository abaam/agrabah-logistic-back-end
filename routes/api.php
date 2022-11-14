<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\SaleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/verify', [VerificationController::class, 'verify'])->name('verify');
Route::post('/resend', [VerificationController::class, 'resend'])->name('resend');

Route::group(['prefix' => 'bookings', 'middleware' => ['auth:sanctum']], function () {
	//Booking
	Route::get('/', [BookingController::class, 'index']);
	Route::get('transactions', [BookingController::class, 'transactions'])->name('transactions');
	Route::get('deliveries', [BookingController::class, 'deliveries'])->name('deliveries');
	Route::get('payment-approval', [BookingController::class, 'pendingApproval'])->name('pendingApproval');
	Route::get('search', [BookingController::class, 'search'])->name('search');
	Route::get('details/{id}', [BookingController::class, 'bookingDetails']);
	Route::get('payment/details/{id}', [BookingController::class, 'paymentDetails']);
	Route::post('store', [BookingController::class, 'store'])->name('store');
	Route::post('payBooking', [BookingController::class, 'payBooking'])->name('payBooking');
	Route::post('cancelBooking', [BookingController::class, 'cancelBooking'])->name('cancelBooking');
	Route::post('approvePayment', [BookingController::class, 'approvePayment'])->name('approvePayment');
	Route::post('acceptBooking', [BookingController::class, 'acceptBooking'])->name('acceptBooking');
	Route::post('updateTracking', [BookingController::class, 'updateTracking'])->name('updateTracking');
});

Route::group(['prefix' => 'sales', 'middleware' => ['auth:sanctum']], function () {
	//Sale
	Route::get('/', [SaleController::class, 'index']);
	Route::get('wallet', [SaleController::class, 'wallet'])->name('wallet');
	Route::get('search', [SaleController::class, 'search'])->name('search');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum']], function () {
	//User
	Route::get('profile', [UserProfileController::class, 'show'])->name('profile');
	Route::post('storeName', [UserProfileController::class, 'storeName'])->name('storeName');
	Route::post('storeEmail', [UserProfileController::class, 'storeEmail'])->name('storeEmail');
	Route::post('storeAddress', [UserProfileController::class, 'storeAddress'])->name('storeAddress');
});

Route::group(['prefix' => 'tracking', 'middleware' => ['auth:sanctum']], function () {
	//tracking
	Route::get('details/{id}', [bookingController::class, 'trackingDetails'])->name('trackingDetails');
});