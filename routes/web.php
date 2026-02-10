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

// DeepUX: Direct Access via Token (Clean URL)
Route::get('/t/{token}', [TrackingController::class, 'showByToken'])->name('public.tracking.show');

// Fix: Redirection for 'login' route which is missing but expected by Laravel auth
Route::get('/login', function () {
    return redirect()->route('filament.auth.login');
})->name('login');

// Payment Routes (Public)
Route::post('/payment/{transaction}/initiate', [\App\Http\Controllers\Public\PaymentController::class, 'initiate'])
    ->name('public.payment.initiate');
Route::post('/payment/{transaction}/confirm', [\App\Http\Controllers\Public\PaymentController::class, 'confirm'])
    ->name('public.payment.confirm');
Route::get('/payment/{transaction}/success', [\App\Http\Controllers\Public\PaymentController::class, 'success'])
    ->name('public.payment.success');

// Payment Gateway Routes (Keep for Midtrans webhook - UNCOMMENT WHEN ACTIVE)
// Route::post('/payment/webhook/midtrans', [\App\Http\Controllers\Public\MidtransController::class, 'webhook'])
//     ->name('public.payment.webhook.midtrans');
Route::post('/payment/token/{transaction}', [\App\Http\Controllers\Public\MidtransController::class, 'createSnapToken'])
    ->name('public.payment.token');

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
