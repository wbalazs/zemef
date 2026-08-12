<?php
// app/Models/GuestCard.php

namespace App\Models\CustomerCard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerCardProductGroupRule extends Model
{
    use HasFactory;

     use HasFactory;

    protected $fillable = [
        'customer_card_id',
        'product_group_id',
        'percent',
    ];

    public function customerCard()
    {
        return $this->belongsTo(CustomerCard::class);
    }
/*
    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }
        */
}

