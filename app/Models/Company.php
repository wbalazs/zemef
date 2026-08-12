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

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'web' , 'company_registration_number', 'tax_number', 'registration_number', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'company_id';


    public function addresses()
    {
        return $this->hasMany('App\Models\CompanyAddress', 'company_id', 'company_id')->orderBy('created_at', 'DESC');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\CompanyContact', 'company_id', 'company_id')->orderBy('created_at', 'DESC');
    }

    public function loans()
    {
        return $this->hasMany('App\Models\CompanyLoan', 'company_id', 'company_id')->orderBy('created_at', 'DESC');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'company_employee', 'company_company_id', 'employee_employee_id')
            ->orderBy('lastname', 'ASC')
            ->orderBy('firstname', 'ASC');
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class);
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'resource_id', 'company_id')->where('document_module', '=', 'company_doc')->orderBy('created_at', 'DESC');
    }

    public function bank_accounts(): BelongsToMany
    {
        return $this->belongsToMany(BankAccount::class);
    }
}
