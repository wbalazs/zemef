<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'plate_number' , 'insurance_number', 'insurance_date',
        'insurance_days', 'insurance_employee_id', 'casco_number',
        'casco_date', 'casco_days', 'casco_employee_id', 'highway_ticket',
        'highway_date', 'highway_days', 'highway_employee_id', 'tech_date',
        'tech_days', 'tech_employee_id', 'responsible_id', 'start_km',
        'end_km', 'condition', 'condition_date', 'status', 'is_fleet',
        'owner_customer_id', 'summer_tyre_size', 'summer_tyre_type',
        'summer_tyre_location', 'summer_tyre_bought_at',
        'summer_tyre_condition', 'winter_tyre_size',
        'winter_tyre_type', 'winter_tyre_location',
        'winter_tyre_bought_at', 'winter_tyre_condition', 'fuel', 'cylinder',
        'note','note_date'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'vehicle_id';


    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'resource_id', 'vehicle_id')->where('document_module', '=', 'vehicle_doc')->orderBy('created_at', 'DESC');
    }

    public function vehicle_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'vehicle_id')->where('module', '=', 'vehicle')->orderBy('created_at', 'DESC');
    }

    public function conditions()
    {
        return $this->hasMany('App\Models\VehicleCondition', 'vehicle_id', 'vehicle_id')->orderBy('created_at', 'DESC');
    }

}
