<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\Auth\PasswordController;

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
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/picture', [ProfileController::class, 'uploadPicture']);
        Route::delete('/profile/picture', [ProfileController::class, 'removePicture']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);
        Route::get('/skip-booking/{bookingId}', [BookingController::class, 'skipBooking'])->name('skip.booking');
        Route::put('/user/password', [PasswordController::class, 'update']);
        
    });
    Route::prefix('api')->group(function () {
        Route::get('/get/profile-data', [ProfileController::class, 'profileData'])->name('profile.data');
    });
});

// Client Routes (Public Routes - Non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/bookings', function () {
        return view('bookings');
    });

    Route::get('/grooming', function () {
        return view('grooming');
    });

    Route::get('/contactUs', function () {
        return view('contactUs');
    });

    
});

// Authenticated and guest routes
Route::post('/book', [BookingController::class, 'create'])->name('book');
Route::post('/book-walkins', [BookingController::class, 'createWalkins'])->name('book.walkins');
// Route::post('/home', [DasboardController::class, 'home'])->name('home');



// API Routes (Public API)
Route::prefix('api')->group(function () {
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::post('/services', [ServicesController::class, 'create']);
    Route::get('/services/{id}', [ServicesController::class, 'show']);
    Route::put('/services/{id}', [ServicesController::class, 'update']);
    Route::delete('/services/{id}', [ServicesController::class, 'destroy']);
    Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/data', [BookingController::class, 'adminBookingsData'])->name('admin.bookings.data');
    Route::get('/queue', [BookingController::class, 'liveQueue']); 
    Route::get('/user/data', [DashboardController::class, 'userData'])->name('user.data');


     // Queue Management Routes
    Route::get('/queue/current', [QueueController::class, 'getCurrentQueue']);
    Route::post('/queue/start', [QueueController::class, 'startQueue']);
    Route::post('/queue/next', [QueueController::class, 'nextClient']);
    Route::post('/queue/skip/{id}', [QueueController::class, 'skipClient']);
    Route::post('/queue/add-walkin', [QueueController::class, 'addWalkinClient']);
    Route::get('/bookings/completed-today', [QueueController::class, 'getCompletedBookingsToday']);
    Route::post('/queue/reopen/{id}', [QueueController::class, 'reopenBooking']);
    Route::get('/queue/stats', [QueueController::class, 'getQueueStats']);
    Route::get('/server-time', [QueueController::class, 'getServerTime']);
    Route::get('/total-revenue', [QueueController::class, 'totalRevenue']);

    // Profile
    Route::get('/profile/data', [ProfileController::class, 'show'])->name('profile.data');
});

require __DIR__.'/auth.php';