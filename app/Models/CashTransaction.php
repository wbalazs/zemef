<?php
// app/Models/CashTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;

class CashTransaction extends Model
{
    protected $primaryKey = 'transaction_id';
    
    protected $fillable = [
        'cash_register_id',
        'document_number',
        'transaction_date',
        'type',
        'amount',
        'payer_payee',
        'reason',
        'reason_custom',
        'description',
        'created_by',
        'exchange_rate',
        'denominations',
        'balance_before',
        'balance_after'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'denominations' => 'array',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2'
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TransactionAttachment::class, 'transaction_id');
    }
}