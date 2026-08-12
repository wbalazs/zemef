<?php

namespace App\Models;

use App\Models\Package\PackageItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WebshopOrderItem extends Model
{
    use HasFactory;

    protected $table = 'webshop_order_items';

    protected $fillable = [
        'order_id', 'shop_product_id','erp_product_id','product_name', 'quantity', 'price', 'total', 'sku'
    ];

    public function order()
    {
        return $this->belongsTo(WebshopOrder::class, 'order_id', 'id');
    }

    public function packageItems(): MorphMany
    {
        return $this->morphMany(PackageItem::class, 'itemable');
    }
}
