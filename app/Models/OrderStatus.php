<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    protected $table = 'order_statuses';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'paid',
        'reports',
        'type',
    ];

    public function orders()
    {
        return $this->hasMany(WebshopOrder::class, 'status_id');
    }
}
