<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMarking extends Model
{
    protected $table = 'customer_markings';

    protected $fillable = [
        'customer_id',
        'name',
        'product_status_id',
        'status',
        'default',
    ];

    protected $casts = [
        'status' => 'boolean',
        'default' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function productStatus(): BelongsTo
    {
        return $this->belongsTo(ProductStatus::class, 'product_status_id', 'product_status_id');
    }
}
