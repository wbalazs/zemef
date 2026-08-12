<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
namespace App\Models;

use App\Tag;
use App\Category;
use App\Models\CustomerMarking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductStatus extends Model
{
    protected $table = 'master_product_statuses';
    public $primaryKey = 'product_status_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_status_id',
        'name',
        'status',
        'price',
        'vat_type_id',
        'from',
        'label'
        ];

    protected $casts = [
        'status' => 'boolean',
        'label' => 'boolean',
        'from' => 'date',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function vatType(): BelongsTo
    {
        return $this->belongsTo(VatType::class, 'vat_type_id', 'vat_type_id');
    }

    public function customerMarkings(): HasMany
    {
        return $this->hasMany(CustomerMarking::class, 'product_status_id', 'product_status_id');
    }
}
