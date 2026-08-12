<?php

namespace App\Models;

use App\Tag;
use App\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductSet extends Model
{
    protected $table = 'products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'short_description', 'long_description', 'brand', 'status','weight', 'category_id',
        'storage_id','price_type','calculated_price','real_price','type','vat_type','discount_allowed','is_rent_available','code'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];


    public $primaryKey = 'product_id';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = self::generateUniqueIdentifier();
        });
    }

    private static function generateUniqueIdentifier() {
        return 'PRD-' . strtoupper(bin2hex(random_bytes(3)));
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }


    public function setItems(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_set_items', 'product_set_id', 'product_id');
    }

    public function hallmarks(): BelongsToMany
    {
        return $this->belongsToMany(Hallmark::class, 'hallmark_product', 'product_product_id', 'hallmark_hallmark_id')
                    ->withPivot('value');
    }


    public function product_colors(): BelongsToMany
    {
        return $this->belongsToMany(ProductColor::class, 'product_product_color', 'product_product_id', 'product_color_product_color_id')
                    ->withPivot('value');
    }


    public function product_statuses(): BelongsToMany
    {
        return $this->belongsToMany(ProductStatus::class, 'product_product_status','product_product_id','product_status_product_status_id')
                    ->withPivot('comment');
    }

    public function product_signs(): BelongsToMany
    {
        return $this->belongsToMany(ProductSign::class,'product_product_sign','product_product_id','product_sign_product_sign_id')
                    ->withPivot('comment');
    }

    public function product_images()
    {
        return $this->hasMany('App\Models\ZImage', 'resource_id', 'product_id')->where('module', 'product')->orderBy('created_at', 'DESC');
    }

    public function pti()
    {
        return $this->belongsTo(PurchaseTicketItem::class, 'product_id', 'product_id');
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
