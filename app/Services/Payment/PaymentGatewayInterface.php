<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Transaction;

/**
 * PaymentGatewayInterface - Contract untuk payment gateway
 * 
 * DeepArchitecture: Strategy pattern untuk multiple payment providers
 * DeepScalability: Easy to add new gateways (GoPay, OVO, etc)
 */
interface PaymentGatewayInterface
{
    /**
     * Create payment session and return payment data
     * 
     * @param Transaction $transaction
     * @return array ['qr_url' => string, 'payment_id' => string, 'expires_at' => timestamp]
     */
    public function createPayment(Transaction $transaction): array;

    /**
     * Verify payment status
     * 
     * @param string $paymentId
     * @return array ['status' => string, 'amount' => float, 'paid_at' => timestamp]
     */
    public function verifyPayment(string $paymentId): array;

    /**
     * Get gateway provider name
     * 
     * @return string
     */
    public function getProviderName(): string;
}
