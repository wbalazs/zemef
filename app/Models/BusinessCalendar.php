<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BusinessCalendar extends Model
{
    use HasFactory;

    /**
     * A tábla neve.
     *
     * @var string
     */
    protected $table = 'business_calendars';

    /**
     * A tömeges hozzárendeléshez engedélyezett mezők.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'day',
        'is_workday',
    ];

    /**
     * Kapcsolat a Business modellel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Átalakítások.
     *
     * @var array
     */
    protected $casts = [
        'is_workday' => 'boolean',
        'day' => 'date',
    ];

    // Accessor a nap nevéhez magyarul
    public function getDayNameAttribute()
    {
        return Carbon::parse($this->day)->locale('hu')->translatedFormat('l');
    }
}
