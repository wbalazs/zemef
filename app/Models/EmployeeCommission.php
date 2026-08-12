<?php
// app/Models/EmployeeCommission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCommission extends Model
{
    protected $table = 'employee_commissions';

    protected $fillable = [
        'employee_id',
        'business_id',
        'commission_campaign_id',
        'commission_rule_id',
        'source_type',
        'source_id',
        'amount',
        'base_amount',
        'quantity',
        'commission_date',
        'status',
        'paid_date',
        'payment_method',
        'note'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'quantity' => 'decimal:2',
        'commission_date' => 'date',
        'paid_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    public function campaign()
    {
        return $this->belongsTo(CommissionCampaign::class, 'commission_campaign_id');
    }

    public function rule()
    {
        return $this->belongsTo(CommissionRule::class, 'commission_rule_id');
    }
}