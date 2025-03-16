<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/bookings-store', [BookingController::class, 'store'])->name('bookings.store');

Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
Route::get('/api/services', [ServicesController::class, 'index']);
Route::get('/api/barbers', [BarberController::class, 'index']);

Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');