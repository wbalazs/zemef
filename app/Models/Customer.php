<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\Models\CustomerMarking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;


class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','firstname','name_day', 'is_company' , 'tax_identity', 'card_number', 'mothers_name', 'birth_place', 'birth_date', 'identity_card_number', 'marketing_accept', 'terms_accept',
        'email', 'phone', 'comment', 'web_address', 'company_registration_number', 'tax_number', 'registration_number', 'payment_id', 'status','created_at',
        'vzj', 'au', 'json'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
        'json' => 'array',
    ];


    public $primaryKey = 'customer_id';


    public function customer_types(): BelongsToMany
    {
        return $this->belongsToMany(CustomerType::class);
    }


    public function addresses()
    {
        return $this->hasMany('App\Models\CustomerAddress', 'customer_id', 'customer_id')->orderBy('city', 'ASC');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\CustomerContact', 'customer_id', 'customer_id')->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC');
    }

    public function payment()
    {
        return $this->hasOne('App\Models\Payment', 'payment_id');
    }

    public function orders()
    {
        return $this->hasMany(WebshopOrder::class, 'customer_id', 'customer_id');
    }

    public function payment_methods(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class, 'customer_payment_method', 'customer_customer_id', 'payment_method_payment_method_id');
    }

    public function customer_markings(): HasMany
    {
        return $this->hasMany(CustomerMarking::class, 'customer_id', 'customer_id')->orderBy('id', 'DESC');
    }




}
