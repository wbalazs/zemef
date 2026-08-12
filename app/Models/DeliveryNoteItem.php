<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryNoteItem extends Model
{
    protected $table = 'delivery_note_items';
    protected $primaryKey = 'note_items_id';

    protected $fillable = [
        'delivery_note_id',
        'inventory_item_id',
        'product_name',
        'product_description',
        'factory_number',
        'weight',
        'quantity',
        'price',
        'warehouse_from',
        'warehouse_to',
        'accepted_employee_id',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'weight' => 'decimal:2',
    ];

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class, 'delivery_note_id', 'delivery_note_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_item_id', 'inventory_id');
    }

    // Ha szükséges, egy accessor a termék nevéhez (az inventory-n keresztül)
    public function getProductNameFromInventoryAttribute()
    {
        return $this->inventoryItem->product->name ?? $this->product_name;
    }
}