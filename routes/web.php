<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Api\ProfileController;


// Admin Authentication Routes
Route::prefix('admin')->group(function () {

    // Guest Middleware (only for login & register pages)
    Route::middleware('guest')->group(function () {
        Route::get('/auth', function () {
            return view('admin.auth');
        })->name('admin.auth');
    });
    
    // Authenticated Middleware (for authenticated admin actions)
    Route::middleware('auth')->group(function () {
        Route::get('/{any}', function () { return view('admin.dashboard'); })->where('any', '.*')->name('dashboard.any');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/add-service', [ServicesController::class, 'create'])->name('services.create');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/skip-booking/{bookingId}', [BookingController::class, 'skipBooking'])->name('skip.booking');
        
    });
});

Route::post('/bookings-store', [BookingController::class, 'store'])->name('bookings.create');

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

    
});

// Authenticated and guest routes
Route::post('/book', [BookingController::class, 'create'])->name('book');
// Route::post('/home', [DasboardController::class, 'home'])->name('home');



// API Routes (Public API)
Route::prefix('api')->group(function () {
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/data', [BookingController::class, 'adminBookingsData'])->name('admin.bookings.data');
    Route::get('/queue', [BookingController::class, 'liveQueue']); 
});

require __DIR__.'/auth.php';
