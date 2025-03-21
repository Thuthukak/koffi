<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PasswordResetController;
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
Route::get('/api/services', [ServicesController::class, 'index']);
Route::get('/api/barbers', [BarberController::class, 'index']);

Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');

// Client Routes (Public Routes - Non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/bookings', function () {
        return view('bookings');
    });

    Route::post('/book', [BookingController::class, 'create'])->name('book');
});

// API Routes (Public API)
Route::prefix('api')->group(function () {
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');
    Route::get('/bookings', [BookingController::class, 'index']);
});

require __DIR__.'/auth.php';
