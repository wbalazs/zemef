<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $table = 'employee_educations';
    public $primaryKey = 'education_id';


    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
