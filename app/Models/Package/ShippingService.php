<?php

namespace App\Models\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'base_price',
        'supports_cod',
        'status',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'supports_cod' => 'boolean',
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('sort_order')->orderBy('name');
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'shipping_service_id');
    }
}
