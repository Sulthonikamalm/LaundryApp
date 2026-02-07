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
        'notes',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }
}
