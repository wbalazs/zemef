<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeHoliday extends Model
{
    protected $table = 'employee_holidays';
    public $primaryKey = 'holiday_id';

    /**
     * Kapcsolat az Employee modellhez.
     * 
     * @return BelongsTo
     */
    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
