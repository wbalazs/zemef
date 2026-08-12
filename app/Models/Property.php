<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Property extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_business' ,'connected_business' , 'zip','zip_id', 'city','city_id', 'address', 'floor',
        'door', 'is_franchise', 'is_own', 'stock_value', 'pledge_value', 'email', 'phone', 'facebook', 'instagram',
        'tiktok', 'status','reservation_type','reservation_to_date','reservation_expire_in_days','pledge_insurance_value'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'property_id';



    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'resource_id', 'property_id')->where('document_module', '=', 'property_doc')->orderBy('created_at', 'DESC');
    }

    public function property_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'property_id')->where('module', '=', 'property')->orderBy('created_at', 'DESC');
    }


    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class)->withPivot('value');
    }


    public function tools(): BelongsToMany
    {
        return $this->belongsToMany(Tool::class);
    }


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }


    public function contacts()
    {
        return $this->hasMany('App\Models\PropertyContact', 'property_id', 'property_id')->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC');
    }


}
