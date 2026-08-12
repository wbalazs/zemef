<?php
// app/Models/GuestCard.php

namespace App\Models\CustomerCard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerCardProductTypeRule  extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_card_id',
        'product_type_id',
        'percent',
    ];

    public function customerCard()
    {
        return $this->belongsTo(CustomerCard::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}