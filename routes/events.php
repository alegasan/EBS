<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('events', [EventController::class, 'index'])
        ->name('events.index');
    Route::get('events/create', [EventController::class, 'create'])
        ->name('events.create');
});
