<?php
// app/Models/CashRegister.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    protected $primaryKey = 'cash_register_id';
    
    protected $fillable = [
        'name',
        'prefix',
        'currency_id',
        'opening_balance',
        'opening_balance_date',
        'business_id',
        'employee_id',
        'is_active'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'cash_register_id');
    }

    public function getCurrentBalanceAttribute()
    {
        $totalIncome = $this->transactions()
            ->where('type', 'income')
            ->sum('amount');
            
        $totalExpense = $this->transactions()
            ->where('type', 'expense')
            ->sum('amount');
            
        return $this->opening_balance + $totalIncome - $totalExpense;
    }

    public function getBalanceForDate($date)
    {
        $transactions = $this->transactions()
            ->whereDate('transaction_date', '<=', $date)
            ->get();
            
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        
        return $this->opening_balance + $income - $expense;
    }
}