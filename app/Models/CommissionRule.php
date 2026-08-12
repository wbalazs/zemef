<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    protected $fillable = [
        'commission_campaign_id',
        'rule_type', // 'basic', 'target'
        'commission_type', // 'percentage', 'fixed_amount', 'per_gram', 'difference_percentage'
        'threshold_min',
        'threshold_max',
        'commission_value',
        'period_type', // 'weekly', 'monthly'
        'period_week', // 1,2,3,4 for weekly
        'priority'
    ];

    protected $casts = [
        'threshold_min' => 'decimal:2',
        'threshold_max' => 'decimal:2',
        'commission_value' => 'decimal:2'
    ];

    public function campaign()
    {
        return $this->belongsTo(CommissionCampaign::class);
    }
}