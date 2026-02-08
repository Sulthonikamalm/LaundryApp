<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * DeepSecurity: Pengecualian ini WAJIB dikompensasi dengan validasi
     * Signature Key (HMAC SHA-512) di Controller penerima webhook.
     * Lihat: MidtransController::validateSignature()
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/midtrans/webhook',
        'midtrans/webhook',
    ];
}
