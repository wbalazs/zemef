<?php

namespace App\Models\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashOnDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'amount',
        'bank_account_number',
        'bank_name',
        'recipient_name',
        'currency',
        'notes',
        'status',
        'collected_at',
        'transferred_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collected_at' => 'datetime',
        'transferred_at' => 'datetime',
    ];

    // Relationships
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCollected($query)
    {
        return $query->where('status', 'collected');
    }

    // Helpers
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'Függőben',
            'collected' => 'Beszedve',
            'transferred' => 'Átutalva',
            'cancelled' => 'Törölve',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function markAsCollected()
    {
        $this->update([
            'status' => 'collected',
            'collected_at' => now(),
        ]);
    }

    public function markAsTransferred()
    {
        $this->update([
            'status' => 'transferred',
            'transferred_at' => now(),
        ]);
    }
}