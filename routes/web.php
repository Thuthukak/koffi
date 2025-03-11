<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/services', [ServicesController::class, 'index'])->name('services.index');


Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');


Route::get('/bookings-create', [BookingController::class, 'create'])->name('bookings.create');
