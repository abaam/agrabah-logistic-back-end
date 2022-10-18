<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\UserProfileController;

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
	Route::get('payment-approval', [BookingController::class, 'pendingApproval'])->name('pendingApproval');
	Route::get('search', [BookingController::class, 'search'])->name('search');
	Route::get('details/{id}', [BookingController::class, 'bookingDetails']);
	Route::post('store', [BookingController::class, 'store'])->name('store');
	Route::post('payBooking', [BookingController::class, 'payBooking'])->name('payBooking');
	Route::post('cancelBooking', [BookingController::class, 'cancelBooking'])->name('cancelBooking');
	Route::post('approvePayment', [BookingController::class, 'approvePayment'])->name('approvePayment');
	Route::post('acceptBooking', [BookingController::class, 'acceptBooking'])->name('acceptBooking');
	Route::post('updateTracking', [BookingController::class, 'updateTracking'])->name('updateTracking');
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum']], function () {
	//User
	Route::get('profile', [UserProfileController::class, 'show'])->name('profile');
	Route::post('storeName', [UserProfileController::class, 'storeName'])->name('storeName');
	Route::post('storeEmail', [UserProfileController::class, 'storeEmail'])->name('storeEmail');
});