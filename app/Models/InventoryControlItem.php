<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class InventoryControlItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_control_id', 'name' , 'description', 'book_quantity', 'inventory_quantity', 'inventory_weight', 'book_weight', 'book_price', 'inventory_price'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'inventory_control_item_id';


    public function inventory_control_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'inventory_control_item_id')->where('module', 'ici')->orderBy('created_at', 'DESC');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function inventory_control()
    {
        return $this->belongsTo(InventoryControl::class, 'inventory_control_id', 'inventory_control_id');
    }

}
