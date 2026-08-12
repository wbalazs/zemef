<?php
namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class Hallmark extends Model
{
    protected $table = 'master_hallmarks';
    public $primaryKey = 'hallmark_id';


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

}
