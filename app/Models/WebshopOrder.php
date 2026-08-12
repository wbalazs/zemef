<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebshopOrder extends Model
{
    use HasFactory;

    protected $table = 'webshop_orders';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id', 'customer_id', 'order_date', 'status', 'total', 'status_id','shipping_name', 'shipping_cost', 'cod_fee', 'json'
    ];
    protected $casts = [
        'json' => 'array',
    ];
     // Helper method to get a specific JSON field
    public function getJsonField($field)
    {
        $json = $this->json;

        return $json[$field] ?? null;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(WebshopOrderItem::class, 'order_id', 'id');
    }

    public function meta()
    {
        return $this->hasMany(WebshopOrderMeta::class, 'order_id', 'id');
    }

    public function order_status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id', 'id');
    }
}
