<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\DashboardController;



// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/auth', function () {return view('admin.auth');})->name('admin.auth');
        
    });
    
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard',[DashboardController::class, 'index'])->name('admin.dashboard');
        

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});


// Clients Unuthentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {return view('welcome');});
    Route::get('/bookings', function () {return view('bookings');});
    Route::post('/book', [BookingController::class, 'create'])->name('book');

});


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::prefix('api')->group(function () {
    Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/barbers', [BarberController::class, 'index'])->name('barbers.index');
    Route::get('/bookings', [BookingController::class, 'index']);

    
});

require __DIR__.'/auth.php';
