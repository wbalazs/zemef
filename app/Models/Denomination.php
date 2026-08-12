<?php
// app/Models/Denomination.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denomination extends Model
{
    protected $primaryKey = 'denomination_id';
    
    protected $fillable = [
        'currency_id',
        'name',
        'value',
        'type',
        'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}