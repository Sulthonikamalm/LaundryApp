<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Payment Model - Entitas Pembayaran
 * 
 * DeepState: Memperbarui status transaksi induk secara otomatis.
 * DeepSecurity: Mencatat aktor (processed_by) secara otomatis.
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'processed_by',
        'amount',
        'payment_method', // cash, transfer, qris
        'status', // pending, completed, failed
        'gateway_provider', // demo, midtrans
        'gateway_status', // pending, approved, rejected, completed
        'approved_by',
        'approved_at',
        'rejection_reason',
        'payment_proof_url',
        'transaction_reference',
        'notes',
        'payment_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // 1. BEFORE CREATE: Set Default Processed By
        static::creating(function ($payment) {
            if (is_null($payment->processed_by) && auth()->check()) {
                $payment->processed_by = auth()->id();
            }
            if (is_null($payment->payment_date)) {
                $payment->payment_date = now();
            }
            // Default status to completed if not set (for cash payments usually)
            if (is_null($payment->status)) {
                $payment->status = 'completed';
            }
        });

        // 2. AFTER SAVE (Create/Update): Recalculate Parent Totals
        static::saved(function ($payment) {
            if ($payment->transaction) {
               // Hitung ulang total bayar & update status
               $payment->transaction->recalculateTotalPaid();
            }
        });

        // 3. AFTER DELETE: Recalculate Parent Totals
        static::deleted(function ($payment) {
            if ($payment->transaction) {
               $payment->transaction->recalculateTotalPaid();
            }
        });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }
}
