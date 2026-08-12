<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CommissionRule;
use App\Models\EmployeeCommission;

class CommissionCampaign extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'campaign_type',
        'status',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

   public function businesses()
    {
        return $this->belongsToMany(Business::class, 'commission_campaign_business', 
                                    'commission_campaign_id', 'business_id');
    }

    public function commissionRules()
    {
        return $this->hasMany(CommissionRule::class);
    }

    public function employeeCommissions()
    {
        return $this->hasMany(EmployeeCommission::class);
    }
}