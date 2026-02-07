<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\TrackingController;
use App\Http\Controllers\Driver\DriverAuthController;
use App\Http\Controllers\Driver\ShipmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| DeepThinking: Public access points tanpa login customer.
| Driver Portal terpisah dengan PIN authentication.
|
*/

// 1. PUBLIC LANDING & TRACKING
Route::get('/', [TrackingController::class, 'index'])->name('public.tracking');
Route::post('/tracking', [TrackingController::class, 'search'])->name('public.tracking.search');

// Fix: Redirection for 'login' route which is missing but expected by Laravel auth
Route::get('/login', function () {
    return redirect()->route('filament.auth.login');
})->name('login');

// 2. DRIVER AUTH (Guest)
Route::prefix('driver')->name('driver.')->group(function () {
    Route::get('/login', [DriverAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [DriverAuthController::class, 'login'])->name('login.submit');
});

// 3. DRIVER DASHBOARD (Protected)
Route::prefix('driver')->name('driver.')->middleware('auth:driver')->group(function () {
    Route::post('/logout', [DriverAuthController::class, 'logout'])->name('logout');
    
    // Dashboard & Job List
    Route::get('/dashboard', [ShipmentController::class, 'dashboard'])->name('dashboard');
    
    // Delivery Workflow
    Route::post('/delivery/{transaction}/start', [ShipmentController::class, 'startDelivery'])
        ->name('delivery.start');
        
    Route::get('/delivery/{transaction}', [ShipmentController::class, 'show'])
        ->name('delivery.show');
        
    Route::post('/delivery/{transaction}/complete', [ShipmentController::class, 'complete'])
        ->name('delivery.complete');
});
