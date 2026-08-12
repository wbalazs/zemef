<?php
// app/Models/DiscountPrice.php

namespace App\Models\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'valid_from',
        'valid_to',
        'usage_limit',
        'used_count',
        'status',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'status' => 'boolean',
    ];

    // Relationships
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true)
                    ->where(function($q) {
                        $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('valid_to')->orWhere('valid_to', '>=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('usage_limit')->orWhereRaw('used_count < usage_limit');
                    });
    }

    // Helpers
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'percentage' ? 'Százalék' : 'Fix összeg';
    }

    public function getDisplayValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        return number_format($this->value, 0, ',', ' ') . ' Ft';
    }

    public function isValid(): bool
    {
        if (!$this->status) return false;
        
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_to && now()->gt($this->valid_to)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        
        return true;
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}