<?php
// app/Models/TransactionAttachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionAttachment extends Model
{
    protected $primaryKey = 'attachment_id';
    
    protected $fillable = [
        'transaction_id',
        'file_name',
        'file_path',
        'mime_type',
        'file_size'
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CashTransaction::class, 'transaction_id');
    }

    // Accessor a fájl URL eléréséhez
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
    
    // Accessor a fájl méret formázásához
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}