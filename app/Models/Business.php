<?php

namespace App\Models;

//businessobserver
use App\Observers\BusinessObserver;
use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Business extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::observe(BusinessObserver::class);
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'zip' ,'zip_id', 'city','city_id', 'address', 'floor', 'door', 'status','note','note_date','discount_type','discount_value'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'business_id';

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(
            Warehouse::class,          // Related model
            'business_warehouse',      // Pivot table name
            'business_business_id',    // Foreign key on pivot for this model (businesses)
            'warehouse_warehouse_id'   // Foreign key on pivot for the related model (warehouses)
        );
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(BusinessCalendar::class, 'business_id', 'business_id');
    }
     // Kapcsolat a kampányokkal
    public function commissionCampaigns()
    {
        return $this->belongsToMany(CommissionCampaign::class, 'commission_campaign_business', 'business_id', 'commission_campaign_id');
    }

    // Napi teljesítmények
    public function dailyPerformances()
    {
        return $this->hasMany(BusinessDailyPerformance::class, 'business_id', 'business_id');
    }

    public function business_markings(): HasMany
    {
        return $this->hasMany(BusinessMarking::class, 'business_id', 'business_id');
    }

    public function business_aliases(): HasMany
    {
        return $this->hasMany(BusinessAlias::class, 'business_id', 'business_id');
    }
}
