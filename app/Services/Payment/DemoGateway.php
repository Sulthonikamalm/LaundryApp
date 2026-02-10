<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Transaction;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

/**
 * DemoGateway - Demo payment untuk portofolio
 * 
 * DeepDemo: Generate QR dummy, manual approval via admin
 * DeepTransition: Easy switch ke real gateway via env
 */
class DemoGateway implements PaymentGatewayInterface
{
    /**
     * Create demo payment with static QR code
     * 
     * DeepLogic: Generate unique QR per transaction untuk realism
     * 
     * @param Transaction $transaction
     * @return array
     */
    public function createPayment(Transaction $transaction): array
    {
        $paymentId = 'DEMO-' . $transaction->transaction_code . '-' . time();
        $qrContent = "DEMO-PAYMENT\nTransaction: {$transaction->transaction_code}\nAmount: Rp " . number_format($transaction->getRemainingBalance(), 0, ',', '.');
        
        // Generate QR code image
        $qrCode = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->generate($qrContent);
        
        // Save to storage
        $filename = "qr-codes/demo-{$transaction->id}-" . time() . ".png";
        Storage::disk('public')->put($filename, $qrCode);
        
        return [
            'qr_url' => asset('storage/' . $filename),
            'payment_id' => $paymentId,
            'expires_at' => now()->addHours(24)->timestamp,
            'instructions' => 'Scan QR code ini dengan aplikasi mobile banking Anda, lalu klik tombol "Saya Sudah Bayar" di bawah.',
        ];
    }

    /**
     * Verify demo payment (always return pending, needs admin approval)
     * 
     * @param string $paymentId
     * @return array
     */
    public function verifyPayment(string $paymentId): array
    {
        return [
            'status' => 'pending',
            'amount' => 0,
            'paid_at' => null,
            'message' => 'Pembayaran Anda sedang diverifikasi oleh admin. Biasanya memakan waktu 5-10 menit.',
        ];
    }

    /**
     * Get provider name
     * 
     * @return string
     */
    public function getProviderName(): string
    {
        return 'demo';
    }
}
