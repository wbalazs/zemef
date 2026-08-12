<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempSaleItem extends Model
{
    protected $table = 'temp_sale_items';

    protected $fillable = [
        'temp_sale_id', 'product_id', 'product_name', 'description',
        'image_path', 'quantity', 'price_net', 'price_gross',
        'vat', 'warehouse_id'
    ];

    public function sale()
    {
        return $this->belongsTo(TempSale::class, 'temp_sale_id');
    }
}
