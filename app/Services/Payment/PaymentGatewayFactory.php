<?php

declare(strict_types=1);

namespace App\Services\Payment;

/**
 * PaymentGatewayFactory - Factory untuk instantiate payment gateway
 * 
 * DeepPattern: Factory pattern untuk clean dependency injection
 * DeepConfig: Single source of truth via env variable
 */
class PaymentGatewayFactory
{
    /**
     * Create payment gateway instance based on config
     * 
     * DeepLogic: Switch gateway via PAYMENT_GATEWAY env variable
     * 
     * @return PaymentGatewayInterface
     * @throws \Exception
     */
    public static function make(): PaymentGatewayInterface
    {
        $gateway = config('services.payment.gateway', 'demo');
        
        return match($gateway) {
            'demo' => new DemoGateway(),
            'midtrans' => new MidtransGateway(),
            default => throw new \Exception("Unsupported payment gateway: {$gateway}"),
        };
    }

    /**
     * Check if current gateway is demo mode
     * 
     * @return bool
     */
    public static function isDemo(): bool
    {
        return config('services.payment.gateway', 'demo') === 'demo';
    }

    /**
     * Check if current gateway is production mode
     * 
     * @return bool
     */
    public static function isProduction(): bool
    {
        return config('services.payment.gateway', 'demo') === 'midtrans';
    }
}
