<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VerificationController;

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
	Route::get('/', [BookingController::class, 'index'])->name('deliveries');
	Route::get('search', [BookingController::class, 'search'])->name('search');
	Route::post('store', [BookingController::class, 'store'])->name('store');
});