<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'master_banks';
    public $primaryKey = 'bank_id';

}
