<?php

declare(strict_types=1);

namespace App\Services\WhatsApp;

/**
 * ReadyMessage - Message builder untuk cucian siap diambil
 * 
 * DeepCode: Extract message building dari Job.
 */
class ReadyMessage extends MessageBuilder
{
    public function build(): string
    {
        $name = $this->transaction->customer->name;
        $code = $this->transaction->transaction_code;
        $url = $this->getTrackingUrl();
        
        $total = $this->formatCurrency($this->transaction->total_cost);
        $balance = $this->formatCurrency($this->transaction->total_cost - $this->transaction->total_paid);

        return "Halo Kak {$name} 👋,\n\n"
            . "Kabar Gembira! Cucian Anda dengan kode *{$code}* sudah *SELESAI* dan siap diambil 🥳.\n\n"
            . "Total Tagihan: Rp {$total}\n"
            . "Sisa Tagihan: Rp {$balance}\n\n"
            . "Cek detail nota & lokasi:\n"
            . "{$url}\n\n"
            . "Silakan datang ke outlet kami. Terima kasih!";
    }
}
