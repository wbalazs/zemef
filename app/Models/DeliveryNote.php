<?php
namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    protected $table = 'delivery_note';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'delivery_note_number', 'employee_id' , 'business_id', 'customer_id', 'warehouse_from', 'warehouse_to', 'is_storno', 'storno_reason', 'received_at', 'receive_employee_id', 'printed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'delivery_note_id';

    public function delivery_note_items()
    {
        return $this->hasMany('App\Models\DeliveryNoteItem', 'delivery_note_id', 'delivery_note_id');
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

    public function delivery_note_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'delivery_note_id')->where('module', 'delivery_note')->orderBy('created_at', 'DESC');
    }

    public function delivery_note_documents()
    {
        return $this->hasMany('App\Models\Document', 'resource_id', 'delivery_note_id')->where('document_module', '=', 'delivery_note_doc')->orderBy('created_at', 'DESC');
    }

}
