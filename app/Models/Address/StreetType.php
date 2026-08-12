<?php

namespace App\Models\Address;

use App\Traits\getListByName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\City\StreetType
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType newQuery()
 * @method static \Illuminate\Database\Query\Builder|StreetType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType query()
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StreetType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|StreetType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|StreetType withoutTrashed()
 * @mixin \Eloquent
 */
class StreetType extends Model
{
    use HasFactory, SoftDeletes, getListByName;

    public const FA_ICON = 'fa-city';
    public const FA_ICON_GROUP = 'fas';

    public $table = 'street_types';

    protected $fillable = [
        'name',
        'short_name',
    ];
}
