<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_number', 'customer_id' , 'storage_id', 'employee_id', 'business_id','currency_id','currency_value','fulfillment_date', 'invoice_number','has_contract','contract_number','comment','status','options','is_storno','storno_se_id','header_data',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
        'header_data' => 'array',
    ];

    public $primaryKey = 'stock_entry_id';
    
    /**
     * @deprecated Ez a kapcsolat a régi tételtáblát használja. Használja helyette az inventoryItems() kapcsolatot.
     */
    public function stock_entry_items()
    {
        return $this->hasMany('App\Models\StockEntryItem', 'stock_entry_id', 'stock_entry_id')->orderBy('name', 'ASC');
    }

    /**
     * Az új inventory tételek az ékszer vásárláshoz (source_type = 'stock_entry')
     */
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'source_header_id', 'stock_entry_id')
                    ->where('source_type', 'stock_entry');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'currency_id');
    }

    public function stock_entry_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'stock_entry_id')->where('module', 'stock_entry')->orderBy('created_at', 'DESC');
    }

    public function getRelatedticketnumberAttribute()
    {
        if($this->attributes['is_storno']){
            return PurchaseTicket::where('storno_se_id', $this->attributes['stock_entry_id'])->first()->ticket_number;
        } elseif($this->attributes['storno_se_id']){
            return PurchaseTicket::where('stock_entry_id', $this->attributes['storno_se_id'])->first()->ticket_number;
        } else {
            return false;
        }
    }

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 2:
                return 'Feldolgozva';
            case 3:
                return 'Sztornó';
            case 4:
                return 'Sztornózva';
            default:
                return 'Folyamatban';
        }
    }
}