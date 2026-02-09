<?php

declare(strict_types=1);

namespace App\Services\WhatsApp;

/**
 * NewOrderMessage - Message builder untuk order baru
 * 
 * DeepCode: Extract complex message building dari Job.
 */
class NewOrderMessage extends MessageBuilder
{
    public function build(): string
    {
        $name = $this->transaction->customer->name;
        $code = $this->transaction->transaction_code;
        $appName = $this->getAppName();
        $url = $this->getTrackingUrl();

        // DeepFix: Ensure details and payments are loaded
        $this->transaction->loadMissing(['details.service', 'payments']);

        $itemsList = $this->buildItemsList();
        $total = $this->formatCurrency($this->transaction->total_cost);
        $paid = $this->formatCurrency($this->transaction->total_paid);
        $balance = $this->formatCurrency($this->transaction->total_cost - $this->transaction->total_paid);
        
        $cashier = $this->transaction->creator->name ?? 'Kasir';
        $orderDate = $this->transaction->created_at->format('d/m/Y - H:i');
        $estDate = $this->formatEstimatedDate();
        $paidStatus = $this->getPaymentStatus();

        return "Halo Kak {$name} 👋,\n"
            . "Terima kasih telah mempercayakan pakaian kesayanganmu di *{$appName}*.\n\n"
            . "{$appName}\n"
            . config('app.address') . ", No. HP " . config('app.phone') . "\n"
            . "====================\n"
            . "Tanggal : {$orderDate}\n"
            . "No Nota : {$code}\n"
            . "Kasir : {$cashier}\n"
            . "Nama : {$name}\n"
            . "===================\n\n"
            . "{$itemsList}\n"
            . "===================\n"
            . "Subtotal = Rp. {$total},-\n"
            . "Diskon = Rp. 0,-\n"
            . "Bayar = Rp. {$total},-\n"
            . "Dibayar = Rp. {$paid},-\n"
            . "Sisa Tagihan = Rp. {$balance},-\n"
            . "====================\n"
            . "Perkiraan Selesai :\n"
            . "{$estDate}\n"
            . "====================\n"
            . "Status : {$paidStatus}\n"
            . "====================\n"
            . "Ketentuan:\n"
            . "1. Pakaian luntur bukan menjadi tanggung jawab kami.\n"
            . "2. Komplain maksimal 1x24 jam sejak diambil.\n"
            . "3. Pengambilan wajib membawa nota.\n"
            . "4. Laundry tak diambil > 1 bulan risiko sendiri.\n"
            . "Terimakasih atas kunjungan anda.\n"
            . "====================\n"
            . "Klik link dibawah ini untuk melihat nota digital & status:\n"
            . "{$url}\n";
    }

    /**
     * Build items list.
     * 
     * @return string
     */
    private function buildItemsList(): string
    {
        $itemsList = "";
        
        foreach ($this->transaction->details as $detail) {
            $serviceName = strtoupper($detail->service->service_name ?? 'LAYANAN');
            $qty = $detail->quantity;
            $unit = $detail->unit ?? 'kg';
            $price = $this->formatCurrency($detail->price_at_transaction);
            $subtotal = $this->formatCurrency($detail->subtotal);
            
            $itemsList .= "{$serviceName} / {$qty} " . strtoupper($unit) . "\n";
            $itemsList .= "{$qty} x Rp. {$price},- = Rp. {$subtotal},-\n";
        }

        return $itemsList;
    }

    /**
     * Format estimated completion date.
     * 
     * @return string
     */
    private function formatEstimatedDate(): string
    {
        $estDate = $this->transaction->estimated_completion_date->format('d/m/Y');
        
        if ($this->transaction->estimated_completion_date->format('H:i') != '00:00') {
            $estDate .= " - " . $this->transaction->estimated_completion_date->format('H:i');
        }
        
        return $estDate;
    }

    /**
     * Get payment status with method.
     * 
     * @return string
     */
    private function getPaymentStatus(): string
    {
        $statusPayment = match($this->transaction->payment_status) {
            'paid' => 'Lunas',
            'partial' => 'Sebagian',
            default => 'Belum Lunas'
        };
        
        // Payment Method detection
        $lastPayment = $this->transaction->payments()->latest()->first();
        $paymentMethodName = "";
        
        if ($lastPayment) {
            $methodMap = [
                'cash' => 'Tunai',
                'transfer' => 'Transfer',
                'qris' => 'QRIS',
            ];
            $val = $lastPayment->payment_method ?? '';
            $paymentMethodName = " (" . ($methodMap[$val] ?? ucfirst($val)) . ")";
        }
        
        return $statusPayment . $paymentMethodName;
    }
}
