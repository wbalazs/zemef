<?php
// app/Services/CashRegister/GenerateDocumentNumber.php

namespace App\Services\CashRegister;

use App\Models\CashTransaction;
use Carbon\Carbon;

class GenerateDocumentNumber
{
    public static function generate(string $prefix, Carbon $date): string
    {
        $dateFormatted = $date->format('ymd'); // EEHHNN formátumban (év-év-hónap-hónap-nap-nap)
        
        // Utolsó mai bizonylatszám keresése
        $lastTransaction = CashTransaction::where('document_number', 'like', $prefix . '-' . $dateFormatted . '/%')
            ->orderBy('document_number', 'desc')
            ->first();
        
        if ($lastTransaction) {
            // Utolsó sorszám kinyerése
            $lastNumber = (int) substr($lastTransaction->document_number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return $prefix . '-' . $dateFormatted . '/' . $newNumber;
    }
}