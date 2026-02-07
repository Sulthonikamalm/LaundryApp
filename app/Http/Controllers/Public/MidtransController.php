<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * MidtransController - Payment Gateway Integration
 * 
 * DeepState: Webhook otomatis update payment_status.
 * DeepSecurity: Signature validation untuk mencegah injection.
 */
class MidtransController extends Controller
{
    /**
     * Handle Midtrans webhook notification.
     * 
     * DeepState: Otomatis update status pembayaran berdasarkan callback.
     * DeepSecurity: Validate signature key.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', $payload);

        // DeepSecurity: Validate signature
        if (!$this->validateSignature($payload)) {
            Log::warning('Midtrans Webhook: Invalid signature', $payload);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? 'accept';
        $grossAmount = $payload['gross_amount'] ?? 0;
        $paymentType = $payload['payment_type'] ?? 'unknown';

        if (!$orderId) {
            return response()->json(['status' => 'error', 'message' => 'Missing order_id'], 400);
        }

        // Parse order_id format: TX-{transaction_id}-{timestamp}
        preg_match('/TX-(\d+)-/', $orderId, $matches);
        $transactionId = $matches[1] ?? null;

        if (!$transactionId) {
            Log::error("Midtrans Webhook: Cannot parse order_id: {$orderId}");
            return response()->json(['status' => 'error', 'message' => 'Invalid order_id format'], 400);
        }

        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            Log::error("Midtrans Webhook: Transaction not found: {$transactionId}");
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        // DeepState: Process based on transaction status
        $this->processPaymentStatus($transaction, $transactionStatus, $fraudStatus, $grossAmount, $paymentType, $payload);

        return response()->json(['status' => 'success']);
    }

    /**
     * Process payment status and update records.
     * 
     * @param Transaction $transaction
     * @param string $status
     * @param string $fraudStatus
     * @param float $amount
     * @param string $paymentType
     * @param array $payload
     */
    protected function processPaymentStatus(
        Transaction $transaction, 
        string $status, 
        string $fraudStatus,
        float $amount,
        string $paymentType,
        array $payload
    ): void {
        $paymentStatus = 'pending';

        // Determine payment record status
        switch ($status) {
            case 'capture':
            case 'settlement':
                $paymentStatus = ($fraudStatus === 'accept') ? 'completed' : 'pending';
                break;
            
            case 'pending':
                $paymentStatus = 'pending';
                break;
            
            case 'deny':
            case 'cancel':
            case 'expire':
                $paymentStatus = 'failed';
                break;
            
            case 'refund':
                $paymentStatus = 'refunded';
                break;
        }

        // Create or update payment record
        $payment = Payment::updateOrCreate(
            [
                'transaction_id' => $transaction->id,
                'transaction_reference' => $payload['transaction_id'] ?? $payload['order_id'],
            ],
            [
                'amount' => (float) $amount,
                'payment_method' => $this->mapPaymentMethod($paymentType),
                'status' => $paymentStatus,
                'payment_date' => now(),
                'notes' => "Midtrans: {$status}",
                'processed_by' => null, // System processed
            ]
        );

        // DeepState: Payment model event akan trigger recalculateTotalPaid()
        // yang akan otomatis update payment_status di Transaction

        Log::info("Midtrans Payment Processed", [
            'transaction_id' => $transaction->id,
            'payment_id' => $payment->id,
            'status' => $paymentStatus,
            'amount' => $amount,
        ]);
    }

    /**
     * Map Midtrans payment type to our format.
     * 
     * @param string $paymentType
     * @return string
     */
    protected function mapPaymentMethod(string $paymentType): string
    {
        return match($paymentType) {
            'credit_card' => 'credit_card',
            'bank_transfer', 'echannel' => 'transfer',
            'gopay', 'shopeepay', 'qris' => 'qris',
            'cstore', 'alfamart', 'indomaret' => 'cash',
            default => 'other',
        };
    }

    /**
     * Validate Midtrans signature.
     * 
     * DeepSecurity: Mencegah fake webhook injection.
     * 
     * @param array $payload
     * @return bool
     */
    protected function validateSignature(array $payload): bool
    {
        $serverKey = config('services.midtrans.server_key');
        
        if (!$serverKey) {
            // Skip validation if not configured (development mode)
            Log::warning('Midtrans: Server key not configured, skipping signature validation');
            return true;
        }

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        // Midtrans signature formula: SHA512(order_id + status_code + gross_amount + server_key)
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Create Snap token for payment.
     * 
     * DeepUI: Generate payment URL untuk pelanggan.
     * 
     * @param Request $request
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function createSnapToken(Request $request, Transaction $transaction): JsonResponse
    {
        // DeepPerformance: Eager load relations to prevent lazy loading exception in local env
        $transaction->load(['customer', 'details.service']);

        $remainingBalance = $transaction->total_cost - $transaction->total_paid;

        if ($remainingBalance <= 0) {
            return response()->json(['error' => 'Transaksi sudah lunas'], 400);
        }

        // Midtrans configuration
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $orderId = 'TX-' . $transaction->id . '-' . time();

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
            
            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment gateway error'], 500);
        }
    }
}
