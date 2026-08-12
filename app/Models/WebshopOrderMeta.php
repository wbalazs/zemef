<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebshopOrderMeta extends Model
{
    use HasFactory;

    protected $table = 'webshop_order_meta';

    protected $fillable = [
        'order_id', 'meta_key', 'meta_value'
    ];

    public function order()
    {
        return $this->belongsTo(WebshopOrder::class, 'order_id', 'id');
    }
}
