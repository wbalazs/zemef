<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Services\Billingo\BillingoService;

class BillingoBill extends Model
{
    protected $table = 'billingo_bills';
    
    protected $fillable = [
        'billingo_id',
        'bill_number',
        'type',
        'partner_id',
        'partner_name',
        'net_total',
        'gross_total',
        'currency',
        'fulfillment_date',
        'due_date',
        'payment_status',
        'payment_method',
        'electronic',
        'paid',
        'paid_date',
        'comment',
        'billingo_data',
        'additional_data',
        'pdf_path',
        'public_url',
    ];
    
    protected $casts = [
        'net_total' => 'decimal:2',
        'gross_total' => 'decimal:2',
        'electronic' => 'boolean',
        'paid' => 'boolean',
        'fulfillment_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'billingo_data' => 'array',
        'additional_data' => 'array',
    ];
    
    /**
     * Relationship with bill items
     */
    public function items(): HasMany
    {
        return $this->hasMany(BillingoBillItem::class, 'billingo_bill_id');
    }
    
    /**
     * Scope for outgoing bills
     */
    public function scopeOutgoing($query)
    {
        return $query->where('type', 'outgoing');
    }
    
    /**
     * Scope for incoming bills
     */
    public function scopeIncoming($query)
    {
        return $query->where('type', 'incoming');
    }
    
    /**
     * Scope for paid bills
     */
    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }
    
    /**
     * Scope for unpaid bills
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }
    
    /**
     * Scope for overdue bills
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('paid', false)
                    ->where('payment_status', '!=', 'paid');
    }
    
    /**
     * Get bill status with colors for UI
     */
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: function () {
                $statusMap = [
                    'not_paid' => ['color' => 'danger', 'text' => 'Fizetésre vár'],
                    'partially_paid' => ['color' => 'warning', 'text' => 'Részben fizetve'],
                    'paid' => ['color' => 'success', 'text' => 'Fizetve'],
                    'overdue' => ['color' => 'dark', 'text' => 'Lejárt'],
                ];
                
                return $statusMap[$this->payment_status] ?? ['color' => 'secondary', 'text' => $this->payment_status];
            }
        );
    }
    
    /**
     * Check if bill is overdue
     */
    public function isOverdue(): bool
    {
        return !$this->paid && 
               $this->due_date && 
               $this->due_date->isPast() &&
               $this->payment_status !== 'paid';
    }
    
    /**
     * Download and save PDF locally
     */
    public function downloadPdf(): bool
    {
        $billingoService = app(BillingoService::class);
        
        try {
            $pdfPath = $billingoService->downloadAndSavePdf(
                $this->billingo_id,
                $this->id,
                $this->type
            );
            
            $this->pdf_path = $pdfPath;
            $this->save();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to download PDF for bill ' . $this->id . ': ' . $e->getMessage());
            return false;
        }
    }
}

// Bill Item Model
class BillingoBillItem extends Model
{
    protected $table = 'billingo_bill_items';
    
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
    
    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'decimal:3',
        'vat_amount' => 'decimal:2',
        'net_total' => 'decimal:2',
        'gross_total' => 'decimal:2',
        'billingo_item_data' => 'array',
    ];
    
    /**
     * Relationship with parent bill
     */
    public function bill()
    {
        return $this->belongsTo(BillingoBill::class, 'billingo_bill_id');
    }

    /**
     * Relationship with bill items
     */
    public function items()
    {
        return $this->hasMany(BillingoBillItem::class, 'billingo_bill_id');
    }
}
