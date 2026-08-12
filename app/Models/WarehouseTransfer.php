<?php
namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    protected $table = 'warehouse_transfer';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'warehouse_transfer_number',
        'employee_id' ,
        'employee_name' ,
        'business_id',
        'business_name',
        'business_address',
        'customer_id',
        'warehouse_from',
        'warehouse_from_name',
        'warehouse_to',
        'warehouse_to_name',
        'is_storno',
        'storno_reason',
        'received_at',
        'receive_employee_id',
        'printed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'warehouse_transfer_id';

    public function warehouse_transfer_items()
    {
        return $this->hasMany('App\Models\WarehouseTransferItem', 'warehouse_transfer_id', 'warehouse_transfer_id');
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

    public function warehouse_transfer_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'warehouse_transfer_id')->where('module', 'warehouse_transfer')->orderBy('created_at', 'DESC');
    }

    public function warehouse_transfer_documents()
    {
        return $this->hasMany('App\Models\Document', 'resource_id', 'warehouse_transfer_id')->where('document_module', '=', 'warehouse_transfer_doc')->orderBy('created_at', 'DESC');
    }


}
