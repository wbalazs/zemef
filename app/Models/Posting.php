<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Posting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'posting_date', 'from_to' , 'purpose', 'customer_id', 'distance', 'comment', 'employee_id', 'vehicle_id'
    ];


    public $primaryKey = 'posting_id';


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    
}
