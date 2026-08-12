<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class PurchaseTicketItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_ticket_id', 'name' , 'description', 'quantity','oneprice', 'price', 'factory_number', 'weight'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'purchase_ticket_item_id';


    public function pti_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'purchase_ticket_item_id')->where('module', 'pti')->orderBy('created_at', 'DESC');
    }


    public function pti_images2()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'purchase_ticket_item_id')->where('module', 'pti2')->orderBy('created_at', 'DESC');
    }


    public function investigation_user()
    {
        return $this->belongsTo(User::class, 'investigation_user_id', 'id');
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }


    public function purchase_ticket()
    {
        return $this->belongsTo(PurchaseTicket::class, 'purchase_ticket_id', 'purchase_ticket_id');
    }


    public function goldsmith(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['delivered_at', 'received_at', 'back_to_storage_at', 'accepted_at']);
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
