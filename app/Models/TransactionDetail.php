<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TransactionDetail Model
 * 
 * DeepSecurity: Implements strict price snapshot logic.
 * DeepState: Automatically updates parent Transaction total_cost.
 */
class TransactionDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'service_id',
        'quantity',
        'price_at_transaction',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_at_transaction' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // 1. BEFORE CREATE: Snapshot Price & Calculate Subtotal
        static::creating(function ($detail) {
            if (is_null($detail->price_at_transaction)) {
                $service = Service::find($detail->service_id);
                // DeepSecurity: Lock price from master data
                if ($service) {
                    $detail->price_at_transaction = $service->base_price;
                }
            }
            
            // Auto-calculate subtotal
            $detail->subtotal = $detail->quantity * $detail->price_at_transaction;
        });

        // 2. BEFORE UPDATE: Recalculate Subtotal
        static::updating(function ($detail) {
             if ($detail->isDirty('quantity') || $detail->isDirty('price_at_transaction')) {
                $detail->subtotal = $detail->quantity * $detail->price_at_transaction;
            }
        });

        // 3. AFTER SAVED (Create/Update): Update Parent Transaction Total
        static::saved(function ($detail) {
            $detail->transaction->recalculateTotalCost();
        });

        // 4. AFTER DELETED: Update Parent Transaction Total
        static::deleted(function ($detail) {
            $detail->transaction->recalculateTotalCost();
        });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
