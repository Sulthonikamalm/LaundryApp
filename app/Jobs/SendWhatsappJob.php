<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;
    
    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60; // Wait 60 seconds between retries

    protected $transaction;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @param Transaction $transaction
     * @param string $type 'new_order' | 'ready' | 'manual_resend'
     */
    public function __construct(Transaction $transaction, string $type)
    {
        $this->transaction = $transaction;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customer = $this->transaction->customer;

        if (!$customer || empty($customer->phone_number)) {
            Log::info("[SendWhatsappJob] Skipped: No phone number for Transaction {$this->transaction->transaction_code}");
            return;
        }

        $message = $this->buildMessage();
        
        Log::info("[SendWhatsappJob] Sending '{$this->type}' message to {$customer->phone_number} for {$this->transaction->transaction_code}");

        $result = FonnteService::sendMessage($customer->phone_number, $message);

        if (!$result['success']) {
            $errorMsg = $result['message'];
            
            // DeepFix: Handle device disconnected gracefully
            if (isset($result['device_error']) && $result['device_error'] === true) {
                Log::warning("[SendWhatsappJob] Device disconnected for {$this->transaction->transaction_code}. Error: {$errorMsg}");
                
                // Don't retry if device is disconnected - it won't help
                if ($this->attempts() >= 2) {
                    Log::error("[SendWhatsappJob] Giving up after {$this->attempts()} attempts. Device still disconnected.");
                    // Don't throw exception - just log and skip
                    return;
                }
                
                // Throw to trigger retry with backoff
                throw new \Exception("WhatsApp device disconnected. Will retry. Reason: {$errorMsg}");
            }
            
            // DeepError: Throw clear exception with the actual reason for other errors
            throw new \Exception("Failed to send WhatsApp via FonnteService. Reason: {$errorMsg}");
        }
        
        Log::info("[SendWhatsappJob] Successfully sent '{$this->type}' message for {$this->transaction->transaction_code}");
    }
    
    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("[SendWhatsappJob] FAILED after all retries for {$this->transaction->transaction_code}. Error: {$exception->getMessage()}");
        
        // Optional: You could update transaction with a flag that WhatsApp failed
        // $this->transaction->update(['whatsapp_notification_failed' => true]);
    }

    private function buildMessage(): string
    {
        $name = $this->transaction->customer->name;
        $code = $this->transaction->transaction_code;
        $appName = "SiLaundry"; // Fixed name

        $url = route('public.tracking.show', ['token' => $this->transaction->url_token]);

        switch ($this->type) {
            case 'new_order':
                $itemsList = "";
                // DeepFix: Ensure details and payments are loaded
                $this->transaction->loadMissing(['details.service', 'payments']);
                
                foreach ($this->transaction->details as $detail) {
                    $serviceName = strtoupper($detail->service->service_name ?? 'LAYANAN');
                    $qty = $detail->quantity;
                    $unit = $detail->unit ?? 'kg';
                    $price = number_format($detail->price_at_transaction, 0, ',', '.');
                    $subtotal = number_format($detail->subtotal, 0, ',', '.');
                    
                    $itemsList .= "{$serviceName} / {$qty} " . strtoupper($unit) . "\n";
                    $itemsList .= "{$qty} x Rp. {$price},- = Rp. {$subtotal},-\n";
                }

                $total = number_format($this->transaction->total_cost, 0, ',', '.');
                $paid = number_format($this->transaction->total_paid, 0, ',', '.');
                $balance = number_format($this->transaction->total_cost - $this->transaction->total_paid, 0, ',', '.');
                
                $cashier = $this->transaction->creator->name ?? 'Kasir';
                $orderDate = $this->transaction->order_date->format('d/m/Y - H:i');
                // Use current time/date of creation if order_date is just date
                if ($this->transaction->created_at) {
                    $orderDate = $this->transaction->created_at->format('d/m/Y - H:i');
                }
                
                $estDate = $this->transaction->estimated_completion_date->format('d/m/Y');
                if ($this->transaction->estimated_completion_date->format('H:i') == '00:00') {
                    // If no time specific, maybe add default time or just date
                     // $estDate .= " - 20:00"; // Optional based on request
                } else {
                     $estDate .= " - " . $this->transaction->estimated_completion_date->format('H:i');
                }
                
                // Status Payment Logic
                $statusPayment = match($this->transaction->payment_status) {
                    'paid' => 'Lunas',
                    'partial' => 'Sebagian',
                    default => 'Belum Lunas'
                };
                
                // Payment Method detection (simple check of last payment)
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
                
                $paidStatusFull = $statusPayment . $paymentMethodName;

                return "Halo Kak {$name} 👋,\n"
                    . "Terima kasih telah mempercayakan pakaian kesayanganmu di *SiLaundry*.\n\n"
                    . "Dicuci-in Laundry\n" // Request used "Dicuci-in Laundry" in header text, but "SiLaundry" in intro? Check user prompt carefully.
                    // User prompt: "ganti di cuciin laundry itu silaundry... Dicuci-in Laundry ... Jl. Jetis..." 
                    // User said: "ingat nama laundry kita itu adalah silaundry" 
                    // But in the sample text he pasted: "Dicuci-in Laundry\nJl. Jetis..."
                    // Correct Interpretation: REPLACE "Dicuci-in Laundry" with "SiLaundry".
                    . "SiLaundry\n"
                    . "Jl. Manyung 1 / 23 Pacungan, No. HP 0821 8846 7793\n"
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
                    . "Bayar = Rp. {$total},-\n" // This line in sample was "Bayar", usually means "Total Bill" or "To Pay". Sample: "Bayar = Rp. 25k, Dibayar = Rp. 25k".
                    // Let's stick to standard: Total, Paid, Balance.
                    . "Dibayar = Rp. {$paid},-\n"
                    . "Sisa Tagihan = Rp. {$balance},-\n" // Changed from "Kembalian" to "Sisa Tagihan" as per second example in prompt?
                    // User prompt example 1: "Kembalian = Rp 0".
                    // User prompt example 2: "Sisa Tagihan = Rp 0".
                    // I will use "Sisa Tagihan" as it is safer for partial payments.
                    . "====================\n"
                    . "Perkiraan Selesai :\n"
                    . "{$estDate}\n"
                    . "====================\n"
                    . "Status : {$paidStatusFull}\n"
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

            case 'ready':
                return "Halo Kak {$name} 👋,\n\n"
                    . "Kabar Gembira! Cucian Anda dengan kode *{$code}* sudah *SELESAI* dan siap diambil 🥳.\n\n"
                    . "Total Tagihan: Rp " . number_format($this->transaction->total_cost, 0, ',', '.') . "\n"
                    . "Sisa Tagihan: Rp " . number_format($this->transaction->total_cost - $this->transaction->total_paid, 0, ',', '.') . "\n\n"
                    . "Cek detail nota & lokasi:\n"
                    . "{$url}\n\n"
                    . "Silakan datang ke outlet kami. Terima kasih!";

            case 'manual_resend':
               return "Halo Kak {$name} 👋,\n\n"
                    . "*[KIRIM ULANG NOTA]*\n"
                    . "Nota: *{$code}*\n"
                    . "Status: " . ucfirst($this->transaction->status) . "\n\n"
                    . "Link Nota Digital:\n"
                    . "{$url}\n\n"
                    . "Terima kasih telah menggunakan jasa kami!";

            case 'payment_received':
                 return "Halo Kak {$name} 👋,\n\n"
                    . "Terima kasih! Pembayaran untuk nota *{$code}* telah kami terima (LUNAS) ✅.\n\n"
                    . "Total Dibayar: Rp " . number_format($this->transaction->total_paid, 0, ',', '.') . "\n"
                    . "Cucian Anda akan segera kami proses.\n\n"
                    . "Lihat Nota Lunas:\n"
                    . "{$url}\n\n"
                    . "Terima kasih!";

            default:
                return "Halo Kak {$name}, Link Detail Transaksi {$code}: {$url}";
        }
    }
}
