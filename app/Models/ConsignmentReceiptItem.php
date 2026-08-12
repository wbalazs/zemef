<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class ConsignmentReceiptItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consignment_receipt_id', 'name' , 'description', 'quantity','oneprice', 'price', 'factory_number', 'weight'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'consignment_receipt_item_id';


    public function consignment_receipt_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'consignment_receipt_item_id')->where('module', 'cri')->orderBy('created_at', 'DESC');
    }


    public function consignment_receipt_images2()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'consignment_receipt_item_id')->where('module', 'cri2')->orderBy('created_at', 'DESC');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function consignment_receipt()
    {
        return $this->belongsTo(StockEntry::class, 'consignment_receipt_id', 'consignment_receipt_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
