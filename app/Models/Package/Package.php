<?php

namespace App\Models\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Package\ShippingService;
use App\Models\WebshopOrder;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_number',
        'order_id',
        'shipping_service_id',
        'shipping_service',
        'shipping_price',
        'tracking_number',
        'weight',
        'notes',
        'status',
        'shipped_at',
        'delivered_at',
        'package_size_id', // new
        'cash_on_delivery_id', // new
        'discount_price_id', // new
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'shipping_price' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

     protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->package_number)) {
                $package->package_number = 'PKG-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });

        static::deleting(function ($package) {
            $package->cashOnDelivery()->delete();
        });
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(WebshopOrder::class, 'order_id');
    }

    public function shippingService(): BelongsTo
    {
        return $this->belongsTo(ShippingService::class, 'shipping_service_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }

    public function packageItems(): HasMany
    {
        return $this->hasMany(PackageItem::class);
    }

    // Scopes
    public function scopeCreated($query)
    {
        return $query->where('status', 'created');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }
    
    public function scopeWithMPL($query)
    {
        return $query->where(function ($q) {
            $q->where('shipping_service', 'MPL')
                ->orWhereHas('shippingService', function ($shippingServiceQuery) {
                    $shippingServiceQuery->where('code', 'MPL');
                });
        });
    }

    // Helpers
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'created' => 'Létrehozva',
            'shipped' => 'Kiszállítva',
            'delivered' => 'Kézbesítve',
            'cancelled' => 'Törölve',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
    public function hasCashOnDelivery(): bool
    {
        return $this->cashOnDelivery()->exists();
    }

    public function isMPLShipping(): bool
    {
        $code = $this->shippingService?->code ?? $this->shipping_service;

        return $code === 'MPL';
    }

    public function getShippingServiceNameAttribute(): string
    {
        return $this->shippingService?->name ?? ($this->shipping_service ?: 'Ismeretlen szolgáltató');
    }
    
    public function markAsShipped()
    {
        $this->update([
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }
    // Add relationships
    public function cashOnDelivery()
    {
        return $this->belongsTo(CashOnDelivery::class);
    }

    public function discountPrice()
    {
        return $this->belongsTo(DiscountPrice::class);
    }

    public function packageSize()
    {
        return $this->belongsTo(PackageSize::class);
    }

    // Calculate final price with discount
    public function getFinalPriceAttribute(): float
    {
        $basePrice = $this->packageSize ? $this->packageSize->price : 0;
        
        if ($this->discountPrice && $this->discountPrice->isValid()) {
            if ($this->discountPrice->type === 'percentage') {
                $discount = $basePrice * ($this->discountPrice->value / 100);
                if ($this->discountPrice->max_discount_amount) {
                    $discount = min($discount, $this->discountPrice->max_discount_amount);
                }
                return $basePrice - $discount;
            } else {
                return max(0, $basePrice - $this->discountPrice->value);
            }
        }
        
        return $basePrice;
    }
}