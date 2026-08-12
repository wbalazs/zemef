<?php

namespace App\Models\Address;

use App\Traits\getListByName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Country extends Model
{
    use HasFactory, SoftDeletes, getListByName;

    public const FA_ICON = 'fa-globe-americas';
    public const FA_ICON_GROUP = 'fas';

    public $table = 'countries';

    protected $fillable = [
        'name',
    ];

    public function zips()
    {
        return $this->hasMany(Zip::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
