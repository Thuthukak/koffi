<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bookings', function () {
    return view('bookings');
});



Route::prefix('api')->group(function () {
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');
    Route::get('/bookings', [BookingController::class, 'index']);

    
});

Route::post('/book', [BookingController::class, 'create'])->name('book');