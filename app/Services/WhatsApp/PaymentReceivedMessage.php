<?php

declare(strict_types=1);

namespace App\Services\WhatsApp;

/**
 * PaymentReceivedMessage - Message builder untuk konfirmasi pembayaran
 * 
 * DeepCode: Extract message building dari Job.
 */
class PaymentReceivedMessage extends MessageBuilder
{
    public function build(): string
    {
        $name = $this->transaction->customer->name;
        $code = $this->transaction->transaction_code;
        $url = $this->getTrackingUrl();
        $paid = $this->formatCurrency($this->transaction->total_paid);

        return "Halo Kak {$name} 👋,\n\n"
            . "Terima kasih! Pembayaran untuk nota *{$code}* telah kami terima (LUNAS) ✅.\n\n"
            . "Total Dibayar: Rp {$paid}\n"
            . "Cucian Anda akan segera kami proses.\n\n"
            . "Lihat Nota Lunas:\n"
            . "{$url}\n\n"
            . "Terima kasih!";
    }
}
