<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class UnitPriceType extends Model
{
    protected $table = 'master_unit_price_types';
    public $primaryKey = 'unit_price_type_id';

}
