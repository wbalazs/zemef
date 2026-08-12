<?php

namespace App\Models\Address;

use App\Traits\getListByName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Zip extends Model
{
    use HasFactory, SoftDeletes, getListByName;

    public const FA_ICON = 'fa-mail-bulk';
    public const FA_ICON_GROUP = 'fas';

    public $table = 'zips';

    protected $fillable = [
        'country_id',
        'name',
    ];

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function cities()
    {
        return $this->belongsToMany(City::class, 'zip_city');
    }
}
