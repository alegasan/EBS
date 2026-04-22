<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:60,1'])->group(function () {

    Route::get('bookings', [BookingController::class, 'index'])
        ->name('bookings.index');
    Route::post('bookings', [BookingController::class, 'store'])
        ->name('bookings.store')
        ->middleware('can:create, App\Models\Booking');
    Route::post('bookings/{id}/confirm', [BookingController::class, 'confirm'])
    ->name('bookings.confirm');
    Route::post('bookings/{id}/cancel', [BookingController::class, 'cancel'])
    ->name('bookings.cancel');
});
