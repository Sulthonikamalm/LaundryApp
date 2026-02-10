<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

/**
 * MidtransGateway - Production payment gateway
 * 
 * DeepProduction: Real Midtrans Snap integration
 * DeepTransition: Uncomment when ready for production
 * 
 * CURRENTLY DISABLED - Uncomment code below to activate
 */
class MidtransGateway implements PaymentGatewayInterface
{
    /**
     * Create Midtrans Snap payment
     * 
     * @param Transaction $transaction
     * @return array
     */
    public function createPayment(Transaction $transaction): array
    {
        // ============================================
        // PRODUCTION CODE - UNCOMMENT WHEN READY
        // ============================================
        
        /*
        // Configure Midtrans
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'TX-' . $transaction->id . '-' . time();
        $remainingBalance = $transaction->getRemainingBalance();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $remainingBalance,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer->name,
                'phone' => $transaction->customer->phone_number,
            ],
            'item_details' => $transaction->details->map(fn($d) => [
                'id' => $d->service_id,
                'price' => (int) $d->price_at_transaction,
                'quantity' => (int) $d->quantity,
                'name' => substr($d->service->service_name, 0, 50),
            ])->toArray(),
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            return [
                'snap_token' => $snapToken,
                'payment_id' => $orderId,
                'expires_at' => now()->addHours(24)->timestamp,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            throw new \Exception('Payment gateway error: ' . $e->getMessage());
        }
        */
        
        // ============================================
        // TEMPORARY FALLBACK (Remove when uncommented above)
        // ============================================
        throw new \Exception('Midtrans gateway is currently disabled. Set PAYMENT_GATEWAY=demo in .env');
    }

    /**
     * Verify Midtrans payment via API
     * 
     * @param string $paymentId
     * @return array
     */
    public function verifyPayment(string $paymentId): array
    {
        // ============================================
        // PRODUCTION CODE - UNCOMMENT WHEN READY
        // ============================================
        
        /*
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

        try {
            $status = \Midtrans\Transaction::status($paymentId);
            
            $paymentStatus = match($status->transaction_status) {
                'capture', 'settlement' => 'completed',
                'pending' => 'pending',
                'deny', 'cancel', 'expire' => 'failed',
                default => 'pending',
            };
            
            return [
                'status' => $paymentStatus,
                'amount' => (float) $status->gross_amount,
                'paid_at' => $paymentStatus === 'completed' ? now() : null,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Verify Error: ' . $e->getMessage());
            return [
                'status' => 'failed',
                'amount' => 0,
                'paid_at' => null,
            ];
        }
        */
        
        // ============================================
        // TEMPORARY FALLBACK
        // ============================================
        throw new \Exception('Midtrans gateway is currently disabled.');
    }

    /**
     * Get provider name
     * 
     * @return string
     */
    public function getProviderName(): string
    {
        return 'midtrans';
    }
}
