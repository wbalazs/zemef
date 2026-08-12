<?php

namespace App\Models;

use App\Tag;
use App\Category;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class InventoryControl extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_id', 'employee_id', 'business_id','inventory_control_date','responsible_user_id','inspector_user_id','inventory_control_number','is_closed'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'inventory_control_id';
    

    public function inventory_control_items()
    {
        return $this->hasMany('App\Models\InventoryControlItem', 'inventory_control_id', 'inventory_control_id')->orderBy('name', 'ASC');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'warehouse_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function responsible_user()
    {
        return $this->belongsTo(User::class, 'responsible_user_id', 'id');
    }

    public function inspector_user()
    {
        return $this->belongsTo(User::class, 'inspector_user_id', 'id');
    }

}
