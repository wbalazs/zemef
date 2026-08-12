<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\BankAccount;


class Employee extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname' , 'maiden_name', 'birth_place',
        'birth_date','mothers_name', 'tax_number', 'social_number', 'address_card_number',
        'identity_card_number','identity_card_number_expire_date', 'home_zip','home_zip_id', 'home_city','home_city_id', 'home_address',
        'home_floor', 'home_door', 'mailing_zip','mailing_zip_id', 'mailing_city','mailing_city_id', 'mailing_address',
        'mailing_floor', 'mailing_door', 'private_phone', 'company_phone', 'private_email','private_bank_account',
        'temporary_address', 'temporary_zip','temporary_zip_id', 'temporary_city','temporary_city_id', 'temporary_floor','temporary_door',
        'start_date', 'end_date', 'bank_contact', 'status', 'card', 'card_expiration_year','has_temporary',
        'card_expiration_month', 'card_limit','internet_limit','buy_limit','note','note_date',
        'nice_card_number','nice_card_expire_date','nice_card_bank_number','active_company_id','active_business_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'employee_id';


    public function bank_accounts(): BelongsToMany
    {
        return $this->belongsToMany(BankAccount::class);
    }

    public function businesses(): BelongsToMany
{
    return $this->belongsToMany(Business::class, 'business_employee', 
                                'employee_employee_id', 'business_business_id');
}


    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class);
    }


    public function documents()
    {
        return $this->hasMany('App\Models\Document', 'resource_id', 'employee_id')->where('document_module', '=', 'employee_doc')->orderBy('created_at', 'DESC');
    }


    public function educations()
    {
        return $this->hasMany('App\Models\EmployeeEducation', 'employee_id')->orderBy('education_start', 'ASC');
    }

    public function holidays()
    {
        return $this->hasMany('App\Models\EmployeeHoliday', 'employee_id')->orderBy('holiday_year', 'DESC');
    }

    public function all_holiday()
    {
        return $this->hasMany('App\Models\Holiday', 'employee_id')->orderBy('start_date', 'DESC');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'employee_id', 'responsible_id');
    }
// Jutalékok
    public function commissions()
    {
        return $this->hasMany(EmployeeCommission::class, 'employee_id', 'employee_id');
    }

    // Napi teljesítmények
    public function dailyPerformances()
    {
        return $this->hasMany(EmployeeDailyPerformance::class, 'employee_id', 'employee_id');
    }

    public function getActiveBusiness()
    {
        if ($this->active_business_id) {
            return Business::find($this->active_business_id);
        }
        
        // If no active business, return the first associated business
        return $this->businesses()->first();
    }
}
