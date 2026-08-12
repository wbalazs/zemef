<?php
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // new is_itemlist field
    protected $fillable = [
        'name', 'status', 'is_central', 'is_receiving', 'is_store', 'is_fake', 'is_hallmark', 'is_goldsmith', 'is_broken_silver', 'is_broken_gold', 'is_repair','is_central_connected','is_consignment'
    ];


    public $primaryKey = 'warehouse_id';

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_warehouse', 'warehouse_warehouse_id', 'business_business_id');
    }


    
}
