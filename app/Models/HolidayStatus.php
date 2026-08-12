<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayStatus extends Model
{
    use HasFactory;

    protected $table = 'holiday_statuses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'status_name',
        'description'
    ];

 
}