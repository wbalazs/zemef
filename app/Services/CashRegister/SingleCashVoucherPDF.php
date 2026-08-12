<?php
// app/Services/CashRegister/CashVoucherPDF.php

namespace App\Services\CashRegister;

use App\Models\CashTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SingleCashVoucherPDF
{
    protected CashTransaction $transaction;
    
    public function __construct(CashTransaction $transaction)
    {
        $this->transaction = $transaction;
    }
    
    public function generate()
    {
        $transaction = $this->transaction->load(['cashRegister.currency', 'createdBy']);
        
        // Példányszám: 2 (egy oldalon két bizonylat)
        $data = [
            'transaction' => $transaction,
            'cashRegister' => $transaction->cashRegister,
            'currency' => $transaction->cashRegister->currency,
            'date' => Carbon::now(),
            'voucherType' => $transaction->type === 'income' ? 'bevételi' : 'kiadási',
            'voucherNumber' => $transaction->document_number,
            'amountFormatted' => number_format(
                $transaction->amount, 
                $transaction->cashRegister->currency->code === 'HUF' ? 0 : 2, 
                ',', 
                ' '
            ),
            'amountText' => $this->numberToWords($transaction->amount, $transaction->cashRegister->currency->code),
        ];
        
        $pdf = Pdf::loadView('pdf.cash-voucher', $data);
        $pdf->setPaper('A4');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        return $pdf;
    }
    
    public function printVoucher(CashTransaction $cashTransaction)
    {
        // Egyedi tranzakció nyomtatása - használjuk a régi logikát
        $cashRegister = $cashTransaction->cashRegister;
        $date = $cashTransaction->transaction_date;
        $type = $cashTransaction->type;
        
        $pdfGenerator = new CashVoucherPDF($cashRegister, $type, $date);
        $pdf = $pdfGenerator->generate();
        
        return $pdf->stream($cashTransaction->document_number . '.pdf');
    }
    /**
     * Számok szöveggé alakítása (magyar nyelvű)
     */
    protected function numberToWords($number, $currency = 'HUF')
    {
        $number = floor($number); // Csak egész rész kell
        
        $words = [
            0 => 'nulla', 1 => 'egy', 2 => 'kettő', 3 => 'három', 4 => 'négy',
            5 => 'öt', 6 => 'hat', 7 => 'hét', 8 => 'nyolc', 9 => 'kilenc',
            10 => 'tíz', 11 => 'tizenegy', 12 => 'tizenkettő', 13 => 'tizenhárom',
            14 => 'tizennégy', 15 => 'tizenöt', 16 => 'tizenhat', 17 => 'tizenhét',
            18 => 'tizennyolc', 19 => 'tizenkilenc', 20 => 'húsz', 30 => 'harminc',
            40 => 'negyven', 50 => 'ötven', 60 => 'hatvan', 70 => 'hetven',
            80 => 'nyolcvan', 90 => 'kilencven'
        ];
        
        if ($number < 21) {
            $text = $words[$number];
        } elseif ($number < 100) {
            $tens = floor($number / 10) * 10;
            $units = $number % 10;
            $text = $words[$tens];
            if ($units > 0) {
                $text .= $words[$units];
            }
        } elseif ($number < 1000) {
            $hundreds = floor($number / 100);
            $remainder = $number % 100;
            $text = $words[$hundreds] . 'száz';
            if ($remainder > 0) {
                $text .= $this->numberToWords($remainder);
            }
        } elseif ($number < 1000000) {
            $thousands = floor($number / 1000);
            $remainder = $number % 1000;
            
            if ($thousands == 1) {
                $text = 'egyezer';
            } else {
                $text = $this->numberToWords($thousands) . 'ezer';
            }
            
            if ($remainder > 0) {
                $text .= $this->numberToWords($remainder);
            }
        } else {
            $text = $number; // Ha túl nagy, marad a szám
        }
        
        // Pénznem hozzáadása
        $currencyText = $currency === 'HUF' ? 'forint' : $currency;
        
        return $text . ' ' . $currencyText;
    }
}