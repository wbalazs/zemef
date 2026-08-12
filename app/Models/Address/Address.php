<?php

namespace App\Models\Address;

use App\Enums\Address\TypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    public const FA_ICON = 'fa-plus';
    public const FA_ICON_GROUP = 'fa-solid';

    protected $fillable = [
        'country_id',
        'zip_id',
        'city_id',
        'street_type_id',
        'street',
        'house',
        'floor',
        'door',
        'parcel_number',
        'type',

        'tax_number',
        'eu_tax_number',
        'name',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'zip_id' => 'integer',
        'city_id' => 'integer',
        'street_type_id' => 'integer',
        'street' => 'string',
        'house' => 'string',
        'floor' => 'string',
        'door' => 'string',
        'parcel_number' => 'string',
        'type' => TypeEnum::class,

        'tax_number' => 'string',
        'eu_tax_number' => 'string',
        'name' => 'string',
    ];

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function zip()
    {
        return $this->hasOne(Zip::class, 'id', 'zip_id');
    }

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function streetType()
    {
        return $this->hasOne(StreetType::class, 'id', 'street_type_id');
    }

}
