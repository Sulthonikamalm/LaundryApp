<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Transaction;
use App\Models\TransactionStatusLog;
use App\Models\TransactionDetail;
use App\Models\Payment;
use App\Models\Admin; // Add import
use Illuminate\Support\Facades\Log;

/**
 * TransactionObserver - Centralized Business Logic
 * 
 * DeepReasoning: Memisahkan logika logging status dan pembersihan relasi
 * dari model utama untuk menjaga kerapian kode (SRP).
 * 
 * DeepState: Memantau perubahan status penting.
 * DeepSecurity: Memastikan audit trail setiap perubahan status.
 */
class TransactionObserver
{
    /**
     * Get active user ID or fallback to system/creator.
     */
    protected function getActorId(?Transaction $transaction = null): ?int
    {
        // 1. Current Auth User
        if (auth()->id()) {
            return auth()->id();
        }
        
        // 2. Transaction Creator (if available)
        if ($transaction && $transaction->created_by) {
            return $transaction->created_by;
        }

        // 3. Fallback to first Admin (System User) - DeepSafety
        // This handles console commands or jobs where no user is logged in
        // but we need a valid foreign key for strict SQL mode.
        return Admin::first()->id ?? null;
    }

    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        TransactionStatusLog::create([
            'transaction_id' => $transaction->id,
            'changed_by' => $this->getActorId($transaction),
            'previous_status' => null,
            'new_status' => $transaction->status,
            'notes' => 'Transaksi dibuat',
        ]);
        
        Log::info("Transaction {$transaction->transaction_code} created.");
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $actorId = $this->getActorId($transaction);

        // 1. Log Status Change (Business Status)
        if ($transaction->isDirty('status')) {
            TransactionStatusLog::create([
                'transaction_id' => $transaction->id,
                'changed_by' => $actorId,
                'previous_status' => $transaction->getOriginal('status'),
                'new_status' => $transaction->status,
                'notes' => 'Perubahan status transaksi',
            ]);
        }

        // 2. Log Payment Status Change
        if ($transaction->isDirty('payment_status')) {
            TransactionStatusLog::create([
                'transaction_id' => $transaction->id,
                'changed_by' => $actorId,
                'previous_status' => $transaction->getOriginal('payment_status'),
                'new_status' => $transaction->payment_status,
                'notes' => 'Perubahan status pembayaran',
            ]);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        if (!$transaction->isForceDeleting()) {
            $transaction->details()->delete();
            $transaction->payments()->delete();
        }
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        $transaction->details()->restore();
        $transaction->payments()->restore();
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        $transaction->details()->forceDelete();
        $transaction->payments()->forceDelete();
        $transaction->statusLogs()->forceDelete();
    }
}
