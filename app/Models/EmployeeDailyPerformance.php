<?php
// app/Models/EmployeeDailyPerformance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDailyPerformance extends Model
{
    protected $table = 'employee_daily_performance';

    protected $fillable = [
        'employee_id',
        'business_id',
        'performance_date',
        'total_sales',
        'total_gold_purchase',
        'total_expired_tickets',
        'total_pawn',
        'total_other'
    ];

    protected $casts = [
        'performance_date' => 'date',
        'total_sales' => 'decimal:2',
        'total_gold_purchase' => 'decimal:2',
        'total_expired_tickets' => 'decimal:2',
        'total_pawn' => 'decimal:2',
        'total_other' => 'decimal:2'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }
}