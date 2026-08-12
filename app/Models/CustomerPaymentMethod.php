<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPaymentMethod extends Model
{
    protected $table = 'customer_payment_method';

    protected $fillable = [
        'payment_method_payment_method_id', 
        'customer_customer_id', 
        'value'
    ];

    public $timestamps = false;

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_method_payment_method_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_customer_id');
    }
}
