<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempSale extends Model
{
    protected $table = 'temp_sales';

    protected $fillable = [
        'customer_id', 'customer_name', 'customer_address', 'sale_type',
        'total_net', 'total_gross', 'discount', 'coupon', 'giftcard',
        'loyalty', 'final_total_net', 'final_total_gross', 'payment_method',
        'employee_id', 'employee_name','booking_max_date','booking_price',
        'is_printed', 'printed_at', 'company_id', 'business_id'
    ];

    public function items()
    {
        return $this->hasMany(TempSaleItem::class, 'temp_sale_id');
    }

    public function business(): BelongsTo
    {
        return $this->BelongsTo(Business::class, 'business_id', 'business_id');
    }
    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class, 'company_id', 'company_id');
    }
}
