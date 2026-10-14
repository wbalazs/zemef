<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAlias extends Model
{
    protected $table = 'business_aliases';

    protected $fillable = [
        'business_id',
        'name',
        'aktiv',
        'warehouse_id',
    ];

    protected $casts = [
        'aktiv' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'warehouse_id');
    }
}
