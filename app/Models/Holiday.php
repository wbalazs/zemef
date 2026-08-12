<?php
namespace App\Models;

use App\Tag;
use App\Category;
use App\Models\Employee;
use App\Models\HolidayStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $table = 'holidays';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id', 
        'start_date', 
        'end_date', 
        'days_taken', 
        'reason', 
        'approved_by', 
        'status_id', 
        'year', 
        'notes',
        'approved_employee',
        'approved_at',
        'declined_employee',
        'declined_at'
    ];

    /**
     * Get the employee associated with the holiday.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Get the status associated with the holiday.
     */
    public function status()
    {
        return $this->belongsTo(HolidayStatus::class, 'status_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_employee');
    }

    public function declinedBy()
    {
        return $this->belongsTo(Employee::class, 'declined_employee');
    }

}
