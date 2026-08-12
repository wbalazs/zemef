<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class ConsignmentReceipt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_number', 'customer_id' , 'storage_id', 'employee_id', 'business_id','currency_id','currency_value','expire_date', 'invoice_number','is_hallmark_requested','contract_number', 'header_data'
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

    public $primaryKey = 'consignment_receipt_id';
    
    /**
     * @deprecated Ez a kapcsolat a régi tételtáblát használja. Használja helyette az inventoryItems() kapcsolatot.
     */
    public function consignment_receipt_items()
    {
        return $this->hasMany('App\Models\ConsignmentReceiptItem', 'consignment_receipt_id', 'consignment_receipt_id')->orderBy('name', 'ASC');
    }

    /**
     * Az új inventory tételek a bizományosi átvételhez (source_type = 'consignment')
     */
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'source_header_id', 'consignment_receipt_id')
                    ->where('source_type', 'consignment');
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

    public function consignment_receipt_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'consignment_receipt_id')->where('module', 'consignment_receipt')->orderBy('created_at', 'DESC');
    }

    public function getRelatedticketnumberAttribute()
    {
        if($this->attributes['is_storno']){
            return ConsignmentReceipt::where('storno_cr_id', $this->attributes['consignment_receipt_id'])->first()->ticket_number;
        } elseif($this->attributes['storno_cr_id']){
            return ConsignmentReceipt::where('consignment_receipt_id', $this->attributes['storno_cr_id'])->first()->ticket_number;
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