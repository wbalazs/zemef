<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseTransferItem extends Model
{
    protected $table = 'warehouse_transfer_items';
    protected $primaryKey = 'transfer_items_id';

    protected $fillable = [
        'warehouse_transfer_id',
        'inventory_item_id',
        'quantity',
        'warehouse_from',
        'warehouse_to',
        'note',
        'back_to_storage_at',
        'accepted_employee_id',
        'accepted_at',
        'is_repair',
        'repair_price',
        'repaired_at',
    ];

    protected $casts = [
        'back_to_storage_at' => 'datetime',
        'accepted_at' => 'datetime',
        'repaired_at' => 'datetime',
        'is_repair' => 'boolean',
    ];

    /**
     * A mozgatás fejrekordja
     */
    public function warehouseTransfer(): BelongsTo
    {
        return $this->belongsTo(WarehouseTransfer::class, 'warehouse_transfer_id', 'warehouse_transfer_id');
    }

    /**
     * Az érintett inventory tétel
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_item_id', 'inventory_id');
    }
}