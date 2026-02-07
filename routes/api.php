<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\MidtransController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| DeepState: Webhook payment gateway & AJAX endpoints.
|
*/

// Middleware 'api' tidak memproteksi CSRF, jadi aman untuk Webhook.
Route::post('/midtrans/webhook', [MidtransController::class, 'webhook']);

// Endpoint untuk Generate Snap Token (dipanggil AJAX dari halaman tracking)
// DeepUI: Membutuhkan info transaksi valid.
Route::post('/payment/{transaction:transaction_code}/snap-token', [MidtransController::class, 'createSnapToken']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
