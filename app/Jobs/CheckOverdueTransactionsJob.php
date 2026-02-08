<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * CheckOverdueTransactionsJob
 * 
 * DeepAdvanced: Atomic Job untuk cek transaksi yang melewati estimasi selesai.
 * DeepPerformance: Chunked processing untuk efisiensi memori.
 * DeepState: Bisa ditambahkan notifikasi WhatsApp/SMS di sini.
 */
class CheckOverdueTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying.
     */
    public int $backoff = 60;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('[Scheduler] CheckOverdueTransactionsJob started');

        $overdueCount = 0;

        // DeepPerformance: Chunked processing untuk memori efisien
        Transaction::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('estimated_completion_date', '<', now())
            ->chunk(100, function ($transactions) use (&$overdueCount) {
                foreach ($transactions as $transaction) {
                    $overdueCount++;
                    
                    // Log untuk monitoring
                    Log::warning('[Scheduler] Overdue transaction detected', [
                        'transaction_id' => $transaction->id,
                        'code' => $transaction->transaction_code,
                        'estimated' => $transaction->estimated_completion_date,
                        'days_late' => now()->diffInDays($transaction->estimated_completion_date),
                    ]);

                    // TODO: Kirim notifikasi ke admin/customer
                    // WhatsAppService::notify($transaction->customer->phone, "...");
                }
            });

        Log::info("[Scheduler] CheckOverdueTransactionsJob completed", [
            'overdue_count' => $overdueCount,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[Scheduler] CheckOverdueTransactionsJob FAILED', [
            'error' => $exception->getMessage(),
        ]);
    }
}
