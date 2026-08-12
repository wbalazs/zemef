<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = 'master_product_types';
    public $primaryKey = 'product_type_id';

    protected $fillable = [
        'product_type_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public static function getName($productTypeId)
    {
        $productType = self::find($productTypeId);
        return $productType ? $productType->name : null;
    }
}
