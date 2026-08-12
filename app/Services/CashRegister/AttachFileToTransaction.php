<?php
// app/Services/CashRegister/AttachFileToTransaction.php

namespace App\Services\CashRegister;

use App\Models\CashTransaction;
use App\Models\TransactionAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AttachFileToTransaction
{
    protected CashTransaction $transaction;
    protected UploadedFile $file;
    
    public function __construct(CashTransaction $transaction, UploadedFile $file)
    {
        $this->transaction = $transaction;
        $this->file = $file;
    }
    
    public function handle(): TransactionAttachment
    {
         // Fájlnév generálás egyedi azonosítóval
        $fileName = time() . '_' . $this->file->getClientOriginalName();

        // Fájl mentés a storage/app/public/cash-transactions/{transaction_id} mappába
        $path = $this->file->storeAs(
            'cash-transactions/' . $this->transaction->transaction_id,
            $fileName,
            'public'
        );
        
        return TransactionAttachment::create([
            'transaction_id' => $this->transaction->transaction_id,
            'file_name' => $this->file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $this->file->getMimeType(),
            'file_size' => $this->file->getSize()
        ]);
    }
}