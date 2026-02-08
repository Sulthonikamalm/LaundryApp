<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * CleanupExpiredPaymentsJob
 * 
 * DeepAdvanced: Atomic Job untuk membersihkan payment records yang expired.
 * DeepState: Soft-delete untuk audit trail.
 */
class CleanupExpiredPaymentsJob implements ShouldQueue
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
        Log::info('[Scheduler] CleanupExpiredPaymentsJob started');

        // Hapus payment pending yang sudah > 24 jam (kemungkinan abandoned)
        $expiredPayments = Payment::query()
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        $cleanedCount = 0;

        foreach ($expiredPayments as $payment) {
            // Update status ke expired, bukan delete (audit trail)
            $payment->update([
                'status' => 'expired',
            ]);

            Log::info('[Scheduler] Payment marked as expired', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'created_at' => $payment->created_at,
            ]);

            $cleanedCount++;
        }

        Log::info("[Scheduler] CleanupExpiredPaymentsJob completed", [
            'cleaned_count' => $cleanedCount,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[Scheduler] CleanupExpiredPaymentsJob FAILED', [
            'error' => $exception->getMessage(),
        ]);
    }
}
