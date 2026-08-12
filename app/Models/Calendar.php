<?php
namespace App\Models;

use App\Tag;
use App\Category;
use App\Models\ToolType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'master_calendar';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'year','date','is_workday'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'calendar_id';

}
