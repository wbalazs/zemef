<?php

namespace App\Models;

use App\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'short_description',
        'long_description',
        'brand',
        'status',
        'weight',
        'category_id',
        'hallmark_type',
        // 'storage_id' - ELTÁVOLÍTVA
        'price_type',
        'calculated_price',
        'real_price',
        'type',
        'discount_allowed',
        'is_rent_available',
        'code',
        'sku',
        'ws_product_id',
        'au', // új mező hozzáadva
    ];

    protected $casts = [
        'options' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = self::generateUniqueIdentifier();
        });
    }

    private static function generateUniqueIdentifier()
    {
        return 'PRD-' . strtoupper(bin2hex(random_bytes(3)));
    }

    // Kapcsolatok

    public function unitPriceType()
    {
        return $this->belongsTo(UnitPriceType::class, 'price_type', 'unit_price_type_id');
    }

    public function vatType()
    {
        return $this->belongsTo(VatType::class, 'vat_type', 'vat_type_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Törölt warehouse() kapcsolat (mert a storage_id mezőt eltávolítottuk)

    public function hallmarks(): BelongsToMany
    {
        return $this->belongsToMany(Hallmark::class)->withPivot('value');
    }

    public function product_colors(): BelongsToMany
    {
        return $this->belongsToMany(ProductColor::class)->withPivot('value');
    }

    public function product_statuses(): BelongsToMany
    {
        return $this->belongsToMany(ProductStatus::class)->withPivot('comment');
    }

    public function product_signs(): BelongsToMany
    {
        return $this->belongsToMany(ProductSign::class)->withPivot('comment');
    }

    public function product_images()
    {
        return $this->hasMany(ZImage::class, 'resource_id', 'product_id')->where('module', 'product')->orderBy('created_at', 'DESC');
    }

    /**
     * Az inventory tételek, amelyek ehhez a terméktípushoz tartoznak
     */
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'product_id');
    }

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
                return 'Aktív';
            case 2:
                return 'Inaktív';
            case 3:
                return 'Feldolgozás alatt';
            case 4:
                return 'Sztornózva';
        }
    }
}