<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class VatType extends Model
{
    protected $table = 'master_vat_types';
    public $primaryKey = 'vat_type_id';

}
