<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionStatusLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'changed_by',
        'previous_status',
        'new_status',
        'activity_type',
        'notes',
        'photo_url',
        'is_milestone',
    ];

    protected $casts = [
        'is_milestone' => 'boolean',
    ];

    /**
     * Activity Types - Source of Truth
     * 
     * DeepClean: Centralized enum untuk konsistensi.
     */
    public static function getActivityTypes(): array
    {
        return [
            'washing' => '🧺 Sedang Dicuci',
            'rinsing' => '💧 Sedang Dibilas',
            'drying' => '☀️ Sedang Dijemur',
            'ironing' => '🔥 Sedang Disetrika',
            'folding' => '📦 Sedang Dilipat',
            'packing' => '🎁 Sedang Dipacking',
            'quality_check' => '✅ Quality Check',
            'ready_pickup' => '🏁 Siap Diambil',
            'other' => '📝 Aktivitas Lain',
        ];
    }

    /**
     * Get activity label with emoji.
     */
    public function getActivityLabel(): string
    {
        $types = self::getActivityTypes();
        return $types[$this->activity_type] ?? ucfirst($this->activity_type ?? 'Update');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }
}
