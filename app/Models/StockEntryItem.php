<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class StockEntryItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stock_entry_id', 'product_id','main_product_id','name' , 'description', 'quantity','oneprice', 'price', 'factory_number', 'weight','warehouse_to'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'stock_entry_item_id';


    public function stock_entry_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'stock_entry_item_id')->where('module', 'sei')->orderBy('created_at', 'DESC');
    }


    public function stock_entry_images2()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'stock_entry_item_id')->where('module', 'sei2')->orderBy('created_at', 'DESC');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function stock_entry()
    {
        return $this->belongsTo(StockEntry::class, 'stock_entry_id', 'stock_entry_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
