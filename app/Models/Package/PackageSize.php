<?php
namespace App\Models\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageSize extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'length',
        'width',
        'height',
        'weight_limit',
        'price',
        'status',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight_limit' => 'decimal:2',
        'price' => 'decimal:2',
        'status' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('id')->orderBy('name');
    }

    // Relationships
    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    // Helpers
    public function getDimensionsAttribute(): string
    {
        if ($this->length && $this->width && $this->height) {
            return "{$this->length} × {$this->width} × {$this->height} cm";
        }
        return 'Nincs megadva';
    }

    public function getVolumeAttribute(): float
    {
        if ($this->length && $this->width && $this->height) {
            return $this->length * $this->width * $this->height;
        }
        return 0;
    }
}