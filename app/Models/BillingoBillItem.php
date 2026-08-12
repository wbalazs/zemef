<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BillingoBillItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'billingo_bill_items';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'billingo_bill_id',
        'name',
        'description',
        'unit_price',
        'unit_price_type',
        'quantity',
        'unit',
        'vat',
        'vat_amount',
        'net_total',
        'gross_total',
        'billingo_item_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'decimal:3',
        'vat_amount' => 'decimal:2',
        'net_total' => 'decimal:2',
        'gross_total' => 'decimal:2',
        'billingo_item_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'billingo_item_data',
    ];

    /**
     * The attributes that should be appended.
     *
     * @var array
     */
    protected $appends = [
        'formatted_unit_price',
        'formatted_quantity',
        'formatted_vat_amount',
        'formatted_net_total',
        'formatted_gross_total',
        'vat_rate_percentage',
    ];

    /**
     * Relationship with the parent bill
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(BillingoBill::class, 'billingo_bill_id');
    }

    /**
     * Scope for items with specific VAT rate
     */
    public function scopeWithVatRate($query, $vatRate)
    {
        return $query->where('vat', 'like', $vatRate . '%');
    }

    /**
     * Scope for items with non-zero VAT
     */
    public function scopeWithVat($query)
    {
        return $query->where('vat', '!=', '0%')->whereNotNull('vat');
    }

    /**
     * Scope for items with zero VAT
     */
    public function scopeWithoutVat($query)
    {
        return $query->where('vat', '=', '0%')->orWhereNull('vat');
    }

    /**
     * Get VAT rate as percentage (without % sign)
     */
    protected function vatRatePercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->vat)) {
                    return 0;
                }
                
                $vatString = (string) $this->vat;
                $vatString = str_replace('%', '', $vatString);
                
                return is_numeric($vatString) ? floatval($vatString) : 0;
            }
        );
    }

    /**
     * Get formatted unit price
     */
    protected function formattedUnitPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->bill) {
                    return number_format($this->unit_price, 2, ',', ' ') . ' HUF';
                }
                
                return number_format($this->unit_price, 2, ',', ' ') . ' ' . $this->bill->currency;
            }
        );
    }

    /**
     * Get formatted quantity
     */
    protected function formattedQuantity(): Attribute
    {
        return Attribute::make(
            get: function () {
                return number_format($this->quantity, 3, ',', ' ') . ' ' . $this->unit;
            }
        );
    }

    /**
     * Get formatted VAT amount
     */
    protected function formattedVatAmount(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->bill) {
                    return number_format($this->vat_amount, 2, ',', ' ') . ' HUF';
                }
                
                return number_format($this->vat_amount, 2, ',', ' ') . ' ' . $this->bill->currency;
            }
        );
    }

    /**
     * Get formatted net total
     */
    protected function formattedNetTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->bill) {
                    return number_format($this->net_total, 2, ',', ' ') . ' HUF';
                }
                
                return number_format($this->net_total, 2, ',', ' ') . ' ' . $this->bill->currency;
            }
        );
    }

    /**
     * Get formatted gross total
     */
    protected function formattedGrossTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->bill) {
                    return number_format($this->gross_total, 2, ',', ' ') . ' HUF';
                }
                
                return number_format($this->gross_total, 2, ',', ' ') . ' ' . $this->bill->currency;
            }
        );
    }

    /**
     * Calculate item totals
     */
    public function calculateTotals(): void
    {
        $netTotal = $this->unit_price * $this->quantity;
        $vatRate = $this->vatRatePercentage;
        
        $this->net_total = round($netTotal, 2);
        $this->vat_amount = round($netTotal * ($vatRate / 100), 2);
        $this->gross_total = round($this->net_total + $this->vat_amount, 2);
    }

    /**
     * Get Billingo API formatted item data
     */
    public function toBillingoFormat(): array
    {
        return [
            'name' => $this->name,
            'unit_price' => floatval($this->unit_price),
            'unit_price_type' => $this->unit_price_type ?? 'net',
            'quantity' => floatval($this->quantity),
            'unit' => $this->unit ?? 'db',
            'vat' => $this->vat ?? '27%',
            'comment' => $this->description ?? '',
            'entitlement' => '',
        ];
    }

    /**
     * Check if item is in global register (≤ 50,000 Ft)
     */
    public function isGlobalRegister(): bool
    {
        // This should check against the corresponding purchase price
        // For now, we'll check based on net total
        return $this->net_total <= 50000;
    }

    /**
     * Check if item is in individual register (> 50,000 Ft)
     */
    public function isIndividualRegister(): bool
    {
        return $this->net_total > 50000;
    }

    /**
     * Get register type for VAT reporting
     */
    public function getRegisterTypeAttribute(): string
    {
        return $this->isGlobalRegister() ? 'global' : 'individual';
    }

    /**
     * Get register type label
     */
    public function getRegisterTypeLabelAttribute(): string
    {
        return $this->isGlobalRegister() 
            ? 'Globális (≤ 50.000 Ft)' 
            : 'Egyedi (> 50.000 Ft)';
    }

    /**
     * Get VAT summary for this item
     */
    public function getVatSummary(): array
    {
        return [
            'net' => $this->net_total,
            'vat_rate' => $this->vatRatePercentage,
            'vat_amount' => $this->vat_amount,
            'gross' => $this->gross_total,
            'register_type' => $this->register_type,
        ];
    }

    /**
     * Find corresponding stock entry item (if exists)
     */
    public function findStockEntryItem()
    {
        // Implement based on your StockEntryItem model
        // This is a placeholder - you'll need to implement based on your data structure
        return null;
    }

    /**
     * Update from Billingo API data
     */
    public function updateFromBillingoData(array $billingoData): bool
    {
        try {
            $this->billingo_item_data = $billingoData;
            $this->save();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to update bill item from Billingo data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create from Billingo API data
     */
    public static function createFromBillingoData(int $billId, array $itemData, array $billingoData = []): self
    {
        $vat = $itemData['vat'] ?? '27%';
        $vatRate = str_replace('%', '', $vat);
        $unitPrice = floatval($itemData['unit_price'] ?? 0);
        $quantity = floatval($itemData['quantity'] ?? 1);
        $netTotal = $unitPrice * $quantity;
        $vatAmount = $netTotal * ($vatRate / 100);
        
        return self::create([
            'billingo_bill_id' => $billId,
            'name' => $itemData['name'] ?? 'Termék',
            'description' => $itemData['comment'] ?? $itemData['description'] ?? '',
            'unit_price' => $unitPrice,
            'unit_price_type' => $itemData['unit_price_type'] ?? 'net',
            'quantity' => $quantity,
            'unit' => $itemData['unit'] ?? 'db',
            'vat' => $vat,
            'vat_amount' => $vatAmount,
            'net_total' => $netTotal,
            'gross_total' => $netTotal + $vatAmount,
            'billingo_item_data' => $billingoData,
        ]);
    }

    /**
     * Get items grouped by VAT rate for reporting
     */
    public static function getGroupedByVatRate($billId = null): array
    {
        $query = self::query();
        
        if ($billId) {
            $query->where('billingo_bill_id', $billId);
        }
        
        return $query->selectRaw('
                vat,
                COUNT(*) as item_count,
                SUM(net_total) as total_net,
                SUM(vat_amount) as total_vat,
                SUM(gross_total) as total_gross
            ')
            ->groupBy('vat')
            ->orderByRaw('CAST(REPLACE(vat, "%", "") AS DECIMAL(5,2)) DESC')
            ->get()
            ->toArray();
    }

    /**
     * Get VAT summary for reporting period
     */
    public static function getVatSummaryForPeriod($startDate, $endDate): array
    {
        return self::selectRaw('
                vat,
                COUNT(*) as item_count,
                SUM(net_total) as total_net,
                SUM(vat_amount) as total_vat,
                SUM(gross_total) as total_gross
            ')
            ->whereHas('bill', function ($query) use ($startDate, $endDate) {
                $query->where('type', 'outgoing')
                      ->whereBetween('fulfillment_date', [$startDate, $endDate])
                      ->where('paid', true);
            })
            ->where('vat', '!=', '0%')
            ->groupBy('vat')
            ->orderByRaw('CAST(REPLACE(vat, "%", "") AS DECIMAL(5,2)) DESC')
            ->get()
            ->toArray();
    }
}