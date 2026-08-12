<?php
// app/Services/CashRegister/CreateCashTransaction.php

namespace App\Services\CashRegister;

use App\Models\CashTransaction;
use App\Models\CashRegister;
use App\Models\DataTransferObjects\CashTransactionData;
use App\Services\CashRegister\GenerateDocumentNumber;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateCashTransaction
{
    protected CashTransactionData $data;
    
    public function __construct(CashTransactionData $data)
    {
        $this->data = $data;
    }
    
    public static function from(CashTransactionData $data): self
    {
        return new self($data);
    }
    
    public function handle(): CashTransaction
    {
        return $this->save($this->data);
    }
    
    protected function save(CashTransactionData $data): CashTransaction
    {
        // Dátum validáció
        //$this->validateDate($data->transaction_date);
        
        return DB::transaction(function () use ($data) {
            $cashRegister = CashRegister::findOrFail($data->cash_register_id);
            
            // Deviza validáció
            $this->validateAmount($data->amount, $cashRegister->currency->sign);
            
            // Aktuális egyenleg lekérése
            $balanceBefore = $cashRegister->getBalanceForDate($data->transaction_date);
            
            // Új egyenleg számítás
            $balanceAfter = $data->type === 'income' 
                ? $balanceBefore + $data->amount 
                : $balanceBefore - $data->amount;
            
            // Bizonylatszám generálás, ha nincs megadva
            $documentNumber = GenerateDocumentNumber::generate($cashRegister->prefix, $data->transaction_date);
            
            // Tranzakció létrehozása
            $transaction = CashTransaction::create([
                'cash_register_id' => $data->cash_register_id,
                'document_number' => $documentNumber,
                'transaction_date' => $data->transaction_date,
                'type' => $data->type,
                'amount' => $data->amount,
                'payer_payee' => $data->payer_payee,
                'reason' => $data->reason,
                'reason_custom' => $data->reason_custom,
                'description' => $data->description,
                'created_by' => Auth::id(),
                'exchange_rate' => $data->exchange_rate,
                'denominations' => $data->denominations,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter
            ]);
            
            return $transaction;
        });
    }
    
    protected function validateDate(Carbon $date): void
    {
        $today = Carbon::today();
        $firstOfMonth = Carbon::today()->startOfMonth();
        
        // Előre nem lehet dátumozni
        if ($date->isAfter($today)) {
            throw ValidationException::withMessages([
                'transaction_date' => 'Nem lehet jövőbeli dátumot megadni!'
            ]);
        }
        
        // Csak az adott hónapra lehet visszadátumozni
        if ($date->isBefore($firstOfMonth)) {
            throw ValidationException::withMessages([
                'transaction_date' => 'Csak az aktuális hónapra lehet visszadátumozni!'
            ]);
        }
        
        // Figyelmeztetés, ha visszadátumoz
        if ($date->isBefore($today)) {
            session()->flash('warning', 'Visszadátumozott tranzakciót rögzítesz!');
        }
    }
    
    protected function validateAmount(float $amount, string $currencyCode): void
    {
        if ($currencyCode === 'HUF') {
            // Ft-os pénztárnál egész szám
            if (floor($amount) != $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Forint esetén az összeg csak egész szám lehet!'
                ]);
            }
        }
    }
}