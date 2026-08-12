<?php
// app/Models/ItemListItem/ItemListItem.php

namespace App\Models\ItemListItem;

use Illuminate\Database\Eloquent\Model;
use App\Models\ItemList\ItemList;
use App\Models\Product;
use App\User;

class ItemListItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_list_items';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_list_id',
        'product_id',
        'main_product_id',
        'name',
        'description',
        'quantity',
        'oneprice',
        'price',
        'factory_number',
        'weight',
        'investigation',
        'investigated_at',
        'investigation_user_id',
        'hallmark',
        'repair',
        'is_fake',
        'warehouse_from',
        'warehouse_to',
        'comment',
        'inv_comment',
        'created_at',
        'updated_at',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'investigation' => 'boolean',
        'hallmark' => 'boolean',
        'repair' => 'boolean',
        'is_fake' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'investigated_at' => 'datetime',
        'weight' => 'decimal:2'
    ];

    /**
     * Get the item list that owns this item.
     */
    public function itemList()
    {
        return $this->belongsTo(ItemList::class, 'item_list_id', 'id');
    }

    /**
     * Get the product associated with this item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the main product associated with this item.
     */
    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'main_product_id', 'product_id');
    }

    /**
     * Get the user who performed the investigation.
     */
    public function investigationUser()
    {
        return $this->belongsTo(User::class, 'investigation_user_id', 'id');
    }
}