<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'master_currencies';
    public $primaryKey = 'currency_id';

}
