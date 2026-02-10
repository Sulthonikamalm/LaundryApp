<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Helpers\PhoneHelper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

/**
 * TrackingController - Public Order Tracking
 * 
 * DeepSecurity: Stateless access dengan validasi Kode Nota + HP.
 * DeepSecrethacking: Rate limiting & dual-key validation.
 * DeepPerformance: Caching hasil pencarian.
 */
class TrackingController extends Controller
{
    /**
     * Show the tracking form.
     * 
     * @return View
     */
    public function index(): View
    {
        return view('public.tracking');
    }

    /**
     * Search order status.
     * 
     * DeepSecurity: Dual-key validation (kode nota + HP).
     * DeepSecrethacking: Rate limiting untuk mencegah brute force.
     * DeepPerformance: Cache hasil selama 2 menit.
     * 
     * @param Request $request
     * @return View
     */
    public function search(Request $request): View
    {
        // Validate input
        $validated = $request->validate([
            'transaction_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $transactionCode = strtoupper(trim($validated['transaction_code']));
        // DeepCode: Gunakan PhoneHelper untuk normalisasi
        $phone = PhoneHelper::normalizeLocal($validated['phone']);

        // DeepSecrethacking: Rate limiting - 5 attempts per minute per IP
        $rateLimitKey = 'tracking:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return view('public.tracking', [
                'error' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
                'transaction_code' => $transactionCode,
            ]);
        }

        RateLimiter::hit($rateLimitKey, 60);

        // DeepPerformance: Cache lookup selama 2 menit
        $cacheKey = "tracking:{$transactionCode}:{$phone}";
        
        $transaction = Cache::remember($cacheKey, 120, function () use ($transactionCode, $phone) {
            // DeepPerformance: Eager load semua relasi untuk mencegah N+1 query
            return Transaction::with([
                'customer', 
                'details.service', 
                'payments', 
                'shipments.courier', // DeepFix: Load courier untuk shipment
                'statusLogs' => function ($query) {
                    // DeepVisual: Load logs dengan foto, urutkan terbaru dulu
                    $query->with('changedBy') // DeepFix: Eager load admin yang mengubah status
                          ->orderBy('created_at', 'desc');
                }
            ])
                ->where('transaction_code', $transactionCode)
                ->whereHas('customer', function ($query) use ($phone) {
                    // DeepSecrethacking: WAJIB cocok dengan nomor HP
                    $query->where('phone_number', $phone)
                          ->orWhere('phone_number', 'LIKE', '%' . substr($phone, -9));
                })
                ->first();
        });

        if (!$transaction) {
            return view('public.tracking', [
                'error' => 'Data tidak ditemukan. Pastikan Kode Nota dan Nomor HP sudah benar.',
                'transaction_code' => $transactionCode,
            ]);
        }

        // DeepSecurity: Dual-key validation sudah cukup aman (Transaction Code + Phone).
        // Kita passing Model langsung agar View bisa render Timeline & Payment dengan lengkap.
        return view('public.tracking-result', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show tracking result by secure token.
     * 
     * DeepUX: Direct access without manual input.
     * DeepSecurity: Token 32-char entropy is practically unguessable.
     * 
     * @param string $token
     * @return View
     */
    public function showByToken(string $token): View
    {
        // DeepPerformance: Eager load semua relasi untuk mencegah N+1 query
        $transaction = Transaction::with([
            'customer', 
            'details.service', 
            'payments', 
            'shipments.courier', // DeepFix: Load courier untuk shipment
            'statusLogs' => function ($query) {
                $query->with('changedBy') // DeepFix: Eager load admin yang mengubah status
                      ->orderBy('created_at', 'desc');
            }
        ])
            ->where('url_token', $token)
            ->firstOrFail();

        return view('public.tracking-result', [
            'transaction' => $transaction,
        ]);
    }
}
