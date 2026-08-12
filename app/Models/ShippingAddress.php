<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'customer_id', 'first_name', 'last_name', 'company', 'address_1',
        'address_2', 'city', 'state', 'postcode', 'country'
    ];
}
