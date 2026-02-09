<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\FonnteService;
use App\Services\WhatsApp\NewOrderMessage;
use App\Services\WhatsApp\ReadyMessage;
use App\Services\WhatsApp\ManualResendMessage;
use App\Services\WhatsApp\PaymentReceivedMessage;
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

    /**
     * Build message using appropriate message builder.
     * 
     * DeepCode: Gunakan message builder pattern untuk clean separation.
     * 
     * @return string
     */
    private function buildMessage(): string
    {
        $builder = match($this->type) {
            'new_order' => new NewOrderMessage($this->transaction),
            'ready' => new ReadyMessage($this->transaction),
            'manual_resend' => new ManualResendMessage($this->transaction),
            'payment_received' => new PaymentReceivedMessage($this->transaction),
            default => null,
        };

        if ($builder) {
            return $builder->build();
        }

        // Fallback untuk tipe yang tidak dikenali
        $name = $this->transaction->customer->name;
        $code = $this->transaction->transaction_code;
        $url = route('public.tracking.show', ['token' => $this->transaction->url_token]);
        
        return "Halo Kak {$name}, Link Detail Transaksi {$code}: {$url}";
    }
}
