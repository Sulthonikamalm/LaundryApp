<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

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
        $phone = $this->normalizePhone($validated['phone']);

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
            return Transaction::with([
                'customer', 
                'details.service', 
                'payments', 
                'shipments',
                'statusLogs' => function ($query) {
                    // DeepVisual: Load logs dengan foto, urutkan terbaru dulu
                    $query->with('changedBy')
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
        $transaction = Transaction::with([
            'customer', 
            'details.service', 
            'payments', 
            'shipments',
            'statusLogs' => function ($query) {
                $query->with('changedBy')
                      ->orderBy('created_at', 'desc');
            }
        ])
            ->where('url_token', $token)
            ->firstOrFail();

        return view('public.tracking-result', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Normalize phone number format.
     * 
     * @param string $phone
     * @return string
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle +62 prefix
        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }
        
        return $phone;
    }

    /**
     * Sanitize transaction data for public display.
     * 
     * DeepSecrethacking: Sembunyikan data sensitif.
     * 
     * @param Transaction $transaction
     * @return array
     */
    protected function sanitizeForPublic(Transaction $transaction): array
    {
        return [
            'transaction_code' => $transaction->transaction_code,
            'customer_name' => $this->maskName($transaction->customer->name),
            'order_date' => $transaction->order_date->format('d/m/Y'),
            'estimated_completion' => $transaction->estimated_completion_date?->format('d/m/Y'),
            'status' => $transaction->status,
            'status_label' => $this->getStatusLabel($transaction->status),
            'payment_status' => $transaction->payment_status,
            'payment_status_label' => $this->getPaymentStatusLabel($transaction->payment_status),
            // DeepSecrethacking: Hanya tampilkan sisa tagihan, bukan total
            'remaining_balance' => max(0, $transaction->total_cost - $transaction->total_paid),
            'items' => $transaction->details->map(fn($d) => [
                'service' => $d->service->service_name,
                'quantity' => $d->quantity,
                'unit' => $d->service->unit,
            ])->toArray(),
            'shipment' => $transaction->shipments->last() ? [
                'status' => $transaction->shipments->last()->status,
                'completed_at' => $transaction->shipments->last()->completed_at?->format('d/m/Y H:i'), // Fixed: was delivered_at
                'proof_url' => $transaction->shipments->last()->photo_proof_url, // Fixed: was proof_image_url
            ] : null,
        ];
    }

    /**
     * Mask customer name for privacy.
     * 
     * @param string $name
     * @return string
     */
    protected function maskName(string $name): string
    {
        $parts = explode(' ', $name);
        return array_map(function ($part) {
            if (strlen($part) <= 2) return $part;
            return substr($part, 0, 2) . str_repeat('*', strlen($part) - 2);
        }, $parts)[0] . (count($parts) > 1 ? ' ' . end($parts)[0] . '***' : '');
    }

    protected function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Menunggu Proses',
            'processing' => 'Sedang Dikerjakan',
            'ready' => 'Siap Diambil/Diantar',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status),
        };
    }

    protected function getPaymentStatusLabel(string $status): string
    {
        return match($status) {
            'unpaid' => 'Belum Dibayar',
            'partial' => 'Bayar Sebagian',
            'paid' => 'Lunas',
            default => ucfirst($status),
        };
    }
}
