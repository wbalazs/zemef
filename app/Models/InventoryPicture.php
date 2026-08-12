<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryPicture extends Model
{
    protected $table = 'inventory_pictures';
    protected $primaryKey = 'inventory_picture_id';

    protected $fillable = [
        'inventory_id',
        'image_path',
        'image_date',
        'description',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }
}
