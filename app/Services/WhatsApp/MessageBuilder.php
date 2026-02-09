<?php

declare(strict_types=1);

namespace App\Services\WhatsApp;

use App\Models\Transaction;

/**
 * MessageBuilder - Base class untuk WhatsApp message builders
 * 
 * DeepCode: Template pattern untuk message building.
 * DeepReasoning: Setiap tipe message punya format berbeda, extract ke class terpisah.
 */
abstract class MessageBuilder
{
    protected Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Build message content.
     * 
     * @return string
     */
    abstract public function build(): string;

    /**
     * Get tracking URL.
     * 
     * @return string
     */
    protected function getTrackingUrl(): string
    {
        return route('public.tracking.show', ['token' => $this->transaction->url_token]);
    }

    /**
     * Get app name.
     * 
     * @return string
     */
    protected function getAppName(): string
    {
        return config('app.name', 'SiLaundry');
    }

    /**
     * Format currency.
     * 
     * @param float $amount
     * @return string
     */
    protected function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}
