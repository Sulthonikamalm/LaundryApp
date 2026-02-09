<?php

declare(strict_types=1);

namespace App\Services\WhatsApp;

/**
 * ManualResendMessage - Message builder untuk kirim ulang nota
 * 
 * DeepCode: Extract message building dari Job.
 */
class ManualResendMessage extends MessageBuilder
{
    public function build(): string
    {
        $name = $this->transaction->customer->name;
        $code = $this->transaction->transaction_code;
        $url = $this->getTrackingUrl();
        $status = ucfirst($this->transaction->status);

        return "Halo Kak {$name} 👋,\n\n"
            . "*[KIRIM ULANG NOTA]*\n"
            . "Nota: *{$code}*\n"
            . "Status: {$status}\n\n"
            . "Link Nota Digital:\n"
            . "{$url}\n\n"
            . "Terima kasih telah menggunakan jasa kami!";
    }
}
