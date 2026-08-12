<?php
// app/Models/GuestCard.php

namespace App\Models\CustomerCard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Customer;
use App\Models\Business;
use App\Models\Inventory;
use App\Models\CustomerCard\CustomerCardProductTypeRule;
use App\Models\CustomerCard\CustomerCardProductGroupRule;

class CustomerCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'start_date',
        'end_date',
        'is_active',
        'rules',
        'json',
    ];

    protected $casts = [
        'type' => 'string',
        'value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'rules' => 'array',
        'json' => 'array',
    ];

    // Relationships
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(
            Business::class,
            'customer_card_business',
            'customer_card_id',
            'business_id',
            'id',
            'business_id'
        );
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_card_customer',
            'customer_card_id',
            'customer_id',
            'id',
            'customer_id'
        );
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Inventory::class,
            'customer_card_inventory',
            'customer_card_id',
            'inventory_id',
            'id',
            'inventory_id'
        );
    }

    public function productTypeRules()
    {
        return $this->hasMany(CustomerCardProductTypeRule::class, 'customer_card_id', 'id');
    }

    public function productGroupRules()
    {
        return $this->hasMany(CustomerCardProductGroupRule::class, 'customer_card_id', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValidForDate($query, $date = null)
    {
        $date = $date ?: now();
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
    }

    public function scopeValidForBusiness($query, $businessId)
    {
        return $query->where(function ($q) use ($businessId) {
            $q->whereDoesntHave('businesses')
              ->orWhereHas('businesses', function ($bq) use ($businessId) {
                  $bq->where('businesses.business_id', $businessId);
              });
        });
    }

    public function scopeValidForCustomer($query, $customerId)
    {
        return $query->whereHas('customers', function ($q) use ($customerId) {
            $q->where('customers.customer_id', $customerId);
        });
    }

    // Methods
    public function isValidFor($businessId = null, $customerId = null, $date = null): bool
    {
        $date = $date ?: now();

        if (!$this->is_active) {
            return false;
        }

        if ($date < $this->start_date || $date > $this->end_date) {
            return false;
        }

        if ($businessId && $this->businesses->isNotEmpty() && !$this->businesses->contains('business_id', $businessId)) {
            return false;
        }

        if ($customerId && $this->customers->isNotEmpty() && !$this->customers->contains('customer_id', $customerId)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($product, $quantity = 1)
    {
        if (!$this->isValidFor()) {
            return 0;
        }

        switch ($this->type) {
            case 'percent':
                return $this->calculatePercentDiscount($product, $quantity);
            
            case 'price':
                return $this->calculateFixedDiscount($quantity);
            
            case 'product_item':
                return $this->calculateProductItemDiscount($product, $quantity);
            
            default:
                return 0;
        }
    }

    protected function calculatePercentDiscount($product, $quantity)
    {
        $basePrice = $product->price * $quantity;
        
        // Check for product type specific rules
        if ($product->product_type_id) {
            $typeRule = $this->productTypeRules
                ->where('product_type_id', $product->product_type_id)
                ->first();
            
            if ($typeRule) {
                return $basePrice * ($typeRule->percent / 100);
            }
        }

        // Check for product group specific rules
        if ($product->product_group_id) {
            $groupRule = $this->productGroupRules
                ->where('product_group_id', $product->product_group_id)
                ->first();
            
            if ($groupRule) {
                return $basePrice * ($groupRule->percent / 100);
            }
        }

        // Use default value
        return $basePrice * ($this->value / 100);
    }

    protected function calculateFixedDiscount($quantity)
    {
        return $this->value * $quantity;
    }

    protected function calculateProductItemDiscount($product, $quantity)
    {
        $inventoryId = $product->inventory_id ?? $product->product_id ?? $product->id ?? null;
        if (!$inventoryId || !$this->products->contains('inventory_id', $inventoryId)) {
            return 0;
        }

        return $product->price * $quantity;
    }

    public function getDiscountTypeLabel(): string
    {
        return match($this->type) {
            'percent' => $this->value . '%',
            'price' => number_format($this->value, 0, ',', ' ') . ' HUF',
            'product_item' => 'Specific Products',
            default => 'Unknown',
        };
    }

    public function getIsCurrentlyActiveAttribute(): bool
    {
        if (!$this->is_active || !$this->start_date || !$this->end_date) {
            return false;
        }

        $today = today();
        return $today->between($this->start_date->copy()->startOfDay(), $this->end_date->copy()->endOfDay());
    }

    public function getIsUpcomingAttribute(): bool
    {
        if (!$this->is_active || !$this->start_date) {
            return false;
        }

        return today()->lt($this->start_date->copy()->startOfDay());
    }

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if ($this->is_upcoming) {
            return 'Scheduled';
        } elseif (!$this->is_currently_active) {
            return 'Expired';
        } else {
            return 'Active';
        }
    }
}