<?php

namespace App\Models\Package;

use App\Models\Inventory;
use App\Models\WebshopOrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'itemable_type',
        'itemable_id',
        'order_item_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relationships
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    // Backward compatible alias for existing eager loads and templates.
    public function orderItem(): MorphTo
    {
        return $this->morphTo('itemable', 'itemable_type', 'itemable_id');
    }

    public function isWebshopOrderItem(): bool
    {
        return $this->itemable_type === WebshopOrderItem::class || (empty($this->itemable_type) && !empty($this->order_item_id));
    }

    public function isInventoryItem(): bool
    {
        return $this->itemable_type === Inventory::class;
    }

    // Helpers
    public function getProductNameAttribute(): string
    {
        $itemable = $this->resolveItemableModel();

        if ($this->isWebshopOrderItem()) {
            return $itemable?->product_name ?? 'Ismeretlen termék';
        }

        if ($this->isInventoryItem()) {
            return $itemable?->name ?? 'Ismeretlen termék';
        }

        return 'Ismeretlen termék';
    }

    public function getSkuAttribute(): string
    {
        return $this->resolveItemableModel()?->sku ?? 'Ismeretlen SKU';
    }

    public function getUnitPriceAttribute(): float
    {
        $itemable = $this->resolveItemableModel();

        if ($this->isWebshopOrderItem()) {
            return (float) ($itemable?->price ?? 0);
        }

        if ($this->isInventoryItem()) {
            return (float) ($itemable?->price ?? $itemable?->oneprice ?? 0);
        }

        return 0.0;
    }

    protected function resolveItemableModel(): ?EloquentModel
    {
        if (!empty($this->itemable_type) && !empty($this->itemable_id)) {
            return $this->itemable;
        }

        if (!empty($this->order_item_id)) {
            return WebshopOrderItem::query()->find($this->order_item_id);
        }

        return null;
    }
}