<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class Karat extends Model
{
    protected $table = 'master_karats';
    public $primaryKey = 'karat_id';

}
