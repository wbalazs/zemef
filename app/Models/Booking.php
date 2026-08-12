<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'master_bookings';
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'duration_months',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}
