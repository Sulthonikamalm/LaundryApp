<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;

class Admin extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'is_active',
        'pin',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Determine if the user can access Filament admin panel.
     * 
     * DeepSecurity: Hanya user yang aktif dengan role owner atau kasir
     * yang boleh mengakses admin panel.
     * Courier login via endpoint terpisah dengan PIN.
     */
    public function canAccessFilament(): bool
    {
        // Hanya izinkan user yang aktif
        if (!$this->is_active) {
            return false;
        }

        // Hanya role owner dan kasir yang boleh akses panel admin
        return in_array($this->role, ['owner', 'kasir']);
    }

    // ========================================
    // RELATIONSHIP METHODS
    // ========================================

    /**
     * Transaksi yang dibuat oleh admin ini.
     */
    public function createdTransactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }

    /**
     * Pembayaran yang diproses oleh admin ini.
     */
    public function processedPayments()
    {
        return $this->hasMany(Payment::class, 'processed_by');
    }

    /**
     * Shipments yang ditangani oleh courier ini.
     */
    public function assignedShipments()
    {
        return $this->hasMany(Shipment::class, 'courier_id');
    }

    /**
     * Log status yang diubah oleh admin ini.
     */
    public function statusChanges()
    {
        return $this->hasMany(TransactionStatusLog::class, 'changed_by');
    }

    // ========================================
    // SCOPE METHODS (DeepPerformance: Query Optimization)
    // ========================================

    /**
     * Scope untuk filter hanya admin yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter berdasarkan role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk mendapatkan semua courier.
     */
    public function scopeCouriers($query)
    {
        return $query->where('role', 'courier');
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Check apakah admin adalah owner.
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Check apakah admin adalah kasir.
     */
    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    /**
     * Check apakah admin adalah courier.
     */
    public function isCourier(): bool
    {
        return $this->role === 'courier';
    }
}
