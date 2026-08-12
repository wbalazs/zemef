<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessMarking extends Model
{
    protected $table = 'business_markings';

    protected $fillable = [
        'business_id',
        'name',
        'product_status_id',
        'status',
        'default',
    ];

    protected $casts = [
        'status' => 'boolean',
        'default' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    public function productStatus(): BelongsTo
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id', 'product_status_id');
    }
}
