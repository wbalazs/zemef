<?php
namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class NAVStandard extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'benzin_1000',
        'benzin_1500',
        'benzin_2000',
        'benzin_3000',
        'benzin_5000',
        'dizel_1500',
        'dizel_2000',
        'dizel_3000',
        'dizel_5000',
        'amort'
    ];

    protected $table = 'master_standards';
    public $primaryKey = 'standard_id';
}
