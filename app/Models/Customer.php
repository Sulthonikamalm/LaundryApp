<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Customer Model - Data Pelanggan
 * 
 * DeepCode: Model yang bersih dengan relasi yang jelas.
 * DeepSecurity: Data pelanggan sensitif, harus dijaga.
 * 
 * @package App\Models
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'customer_type',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get all transactions for this customer.
     * 
     * @return HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    /**
     * Scope for individual customers.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIndividual($query)
    {
        return $query->where('customer_type', 'individual');
    }

    /**
     * Scope for corporate customers.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCorporate($query)
    {
        return $query->where('customer_type', 'corporate');
    }

    // ========================================
    // HELPERS
    // ========================================

    /**
     * Check if customer is corporate type.
     * 
     * @return bool
     */
    public function isCorporate(): bool
    {
        return $this->customer_type === 'corporate';
    }

    /**
     * Get total spent by this customer.
     * 
     * @return float
     */
    public function getTotalSpent(): float
    {
        return (float) $this->transactions()
            ->where('payment_status', 'paid')
            ->sum('total_cost');
    }
}
