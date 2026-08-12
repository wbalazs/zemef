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

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class CustomerType extends Model
{
    protected $table = 'customer_types';
    public $primaryKey = 'customer_type_id';

    protected $fillable = ['name', 'status', 'parent_id'];

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class);
    }


    public function children()
    {
        return $this->hasMany(CustomerType::class, 'parent_id', 'customer_type_id');
    }
}
