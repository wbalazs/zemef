<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Currency;
use App\Models\Payment;

class PurchaseTicket extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_number', 'customer_id' , 'storage_id', 'employee_id', 'business_id','currency_id','currency_value', 'payment_id', 'status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    public $primaryKey = 'purchase_ticket_id';
    
    /**
     * New relationship: inventory items coming from this purchase ticket
     */
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'source_header_id', 'purchase_ticket_id')
                    ->where('source_type', 'purchase');
    }

    /**
     * Old relationship - will be removed after migration
     * @deprecated
     */
    public function purchase_ticket_items()
    {
        return $this->hasMany('App\Models\PurchaseTicketItem', 'purchase_ticket_id', 'purchase_ticket_id')->orderBy('name', 'ASC');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'storage_id', 'warehouse_id');
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

    public function getRelatedticketnumberAttribute()
    {
        if($this->attributes['is_storno']){
            return PurchaseTicket::where('storno_pt_id', $this->attributes['purchase_ticket_id'])->first()->ticket_number;
        } elseif($this->attributes['storno_pt_id']){
            return PurchaseTicket::where('purchase_ticket_id', $this->attributes['storno_pt_id'])->first()->ticket_number;
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