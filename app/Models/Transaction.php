<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Transaction Model - Transaksi Laundry
 * 
 * DeepCode: Core business model dengan relasi lengkap.
 * DeepSecurity: Audit trail via created_by.
 * DeepPerformance: Optimized dengan proper indexing di migration.
 * 
 * @package App\Models
 */
class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     * 
     * DeepSecurity: Hati-hati dengan total_cost dan total_paid.
     * Seharusnya dihitung otomatis, bukan dari input user.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_code',
        'customer_id',
        'created_by',
        'order_date',
        'estimated_completion_date',
        'actual_completion_date',
        'total_cost',
        'total_paid',
        'payment_status',
        'status',
        'customer_notes',
        'internal_notes',
        'is_delivery',
        'delivery_cost',
        'delivery_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'estimated_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'total_cost' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'is_delivery' => 'boolean',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get the customer that owns this transaction.
     * 
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the admin who created this transaction.
     * 
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Get all details for this transaction.
     * 
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Get all payments for this transaction.
     * 
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all shipments for this transaction.
     * 
     * @return HasMany
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Get all status logs for this transaction.
     * 
     * @return HasMany
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(TransactionStatusLog::class);
    }

    // ========================================
    // SCOPES (DeepPerformance: Query Optimization)
    // ========================================

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for processing transactions.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for ready transactions.
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    /**
     * Scope for completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for unpaid transactions.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Scope for today's transactions.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('order_date', today());
    }

    /**
     * Scope for overdue transactions (past estimated completion).
     * 
     * DeepDive: Query yang reusable untuk laporan cucian terlambat.
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'processing'])
            ->where('estimated_completion_date', '<', now());
    }

    /**
     * Scope for transactions with outstanding balance.
     * 
     * DeepDive: Query untuk piutang.
     */
    public function scopeOutstanding($query)
    {
        return $query->whereIn('payment_status', ['unpaid', 'partial'])
            ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope for date range filter.
     * 
     * DeepPerformance: Memanfaatkan index pada order_date.
     */
    public function scopeDateRange($query, string $startDate, string $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Check if transaction is editable.
     * 
     * DeepSecurity: Transaksi completed tidak boleh diedit.
     * 
     * @return bool
     */
    public function isEditable(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Get remaining balance.
     * 
     * @return float
     */
    public function getRemainingBalance(): float
    {
        return (float) ($this->total_cost - $this->total_paid);
    }

    /**
     * Check if fully paid.
     * 
     * @return bool
     */
    public function isFullyPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Recalculate total cost from details.
     * 
     * DeepSecurity: Total harus dihitung dari detail, bukan input manual.
     * 
     * @return float
     */
    public function recalculateTotalCost(): float
    {
        $total = $this->details()->sum('subtotal');
        $this->update(['total_cost' => $total]);
        return (float) $total;
    }

    /**
     * Recalculate total paid from payments.
     * 
     * DeepSecurity: Total paid harus dari bukti pembayaran.
     * 
     * @return float
     */
    public function recalculateTotalPaid(): float
    {
        $total = $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
        
        $this->update(['total_paid' => $total]);
        $this->updatePaymentStatus();
        
        return (float) $total;
    }

    /**
     * Update payment status based on paid amount.
     * 
     * @return void
     */
    public function updatePaymentStatus(): void
    {
        $status = 'unpaid';
        
        if ($this->total_paid > 0) {
            $status = $this->total_paid >= $this->total_cost ? 'paid' : 'partial';
        }
        
        $this->update(['payment_status' => $status]);
    }

    // ========================================
    // ENUMS & OPTIONS (DeepClean: Centralized Source of Truth)
    // ========================================

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Proses',
            'ready' => 'Siap Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public static function getPaymentStatusOptions(): array
    {
        return [
            'unpaid' => 'Belum Bayar',
            'partial' => 'DP/Sebagian',
            'paid' => 'Lunas',
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            'secondary' => 'pending',
            'warning' => 'processing',
            'success' => 'ready',
            'primary' => 'completed',
            'danger' => 'cancelled',
        ];
    }

    public static function getPaymentStatusColors(): array
    {
        return [
            'danger' => 'unpaid',
            'warning' => 'partial',
            'success' => 'paid',
        ];
    }

    // ========================================
    // BOOT (DeepSecurity: Auto-generate transaction code)
    // ========================================

    protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_code)) {
                $transaction->transaction_code = self::generateTransactionCode();
            }
            if (empty($transaction->url_token)) {
                $transaction->url_token = Str::random(32);
            }
        });
    }

    /**
     * Generate unique transaction code.
     * 
     * Format: LDR-YYYY-XXXX
     * 
     * @return string
     */
    public static function generateTransactionCode(): string
    {
        $year = date('Y');
        $lastTransaction = self::whereYear('created_at', $year)
            ->orderBy('transaction_code', 'desc')
            ->first();
        
        if (!$lastTransaction) {
            return sprintf('LDR-%s-%04d', $year, 1);
        }

        // DeepFix: Robust parsing to handle malformed codes (e.g. LDR-2026--001)
        if (preg_match('/(\d+)$/', $lastTransaction->transaction_code, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            // Fallback if no digits found at end
            $nextNumber = (int) substr($lastTransaction->transaction_code, -4) + 1;
        }
        
        return sprintf('LDR-%s-%04d', $year, $nextNumber);
    }
}
