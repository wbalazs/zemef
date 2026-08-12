<?php
// app/Services/CashRegister/CashVoucherPDF.php

namespace App\Services\CashRegister;

use App\Models\CashRegister;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CashVoucherPDF
{
    protected CashRegister $cashRegister;
    protected $type; // 'income', 'expense', vagy 'both'
    protected $date;
    
    public function __construct(CashRegister $cashRegister, $type, $date = null)
    {
        $this->cashRegister = $cashRegister;
        $this->type = $type;
        $this->date = $date ? Carbon::parse($date) : Carbon::today();
    }
    
    public function generate()
    {
        $data = [
            'cashRegister' => $this->cashRegister,
            'currency' => $this->cashRegister->currency,
            'date' => $this->date,
            'dateFormatted' => $this->date->format('Y. m. d.'),
        ];
        
        if ($this->type === 'both') {
            // Mindkét típus betöltése
            $data['incomeTransactions'] = $this->getTransactions('income');
            $data['expenseTransactions'] = $this->getTransactions('expense');
            $data['incomeTotal'] = $data['incomeTransactions']->sum('amount');
            $data['expenseTotal'] = $data['expenseTransactions']->sum('amount');
            $data['incomeTotalFormatted'] = $this->formatAmount($data['incomeTotal']);
            $data['expenseTotalFormatted'] = $this->formatAmount($data['expenseTotal']);
            $data['incomeTotalText'] = $this->numberToWords($data['incomeTotal']);
            $data['expenseTotalText'] = $this->numberToWords($data['expenseTotal']);
            $data['documentNumber'] = $this->generateDocumentNumber('both');
            $data['type'] = 'both';
            $data['typeName'] = 'bevételi és kiadási';
            
            $view = 'pdf.cash-register-voucher-both';
        } else {
            // Egy típus betöltése
            $data['transactions'] = $this->getTransactions($this->type);
            $data['totalAmount'] = $data['transactions']->sum('amount');
            $data['totalAmountFormatted'] = $this->formatAmount($data['totalAmount']);
            $data['totalAmountText'] = $this->numberToWords($data['totalAmount']);
            $data['documentNumber'] = $this->generateDocumentNumber($this->type);
            $data['type'] = $this->type;
            $data['typeName'] = $this->type === 'income' ? 'bevételi' : 'kiadási';
            
            $view = 'pdf.cash-register-voucher-single';
        }
        
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('A4');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        return $pdf;
    }
    
    protected function getTransactions($type)
    {
        return $this->cashRegister->transactions()
            ->with('createdBy')
            ->whereDate('transaction_date', $this->date)
            ->where('type', $type)
            ->orderBy('created_at')
            ->get();
    }
    
    protected function formatAmount($amount)
    {
        return number_format(
            $amount, 
            $this->cashRegister->currency->code === 'HUF' ? 0 : 2, 
            ',', 
            ' '
        );
    }
    
    /**
     * Bizonylatszám generálás
     */
    protected function generateDocumentNumber($type)
    {
        $dateFormatted = $this->date->format('ymd');
        
        switch($type) {
            case 'income':
                $typePrefix = 'BEV';
                break;
            case 'expense':
                $typePrefix = 'KAD';
                break;
            case 'both':
                $typePrefix = 'OSSZ';
                break;
            default:
                $typePrefix = 'BIZ';
        }
        
        // Számláló a napon belül
        $count = $this->cashRegister->transactions()
            ->whereDate('transaction_date', $this->date)
            ->where('type', $type === 'both' ? null : $type)
            ->count();
        
        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        
        return $this->cashRegister->prefix . '-' . $dateFormatted . '/' . $typePrefix . $sequence;
    }
    
    /**
     * Számok szöveggé alakítása (magyar nyelvű)
     */
    protected function numberToWords($number)
    {
        $number = floor($number); // Csak egész rész kell
        $currency = $this->cashRegister->currency->code;
        
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
    /**
     * Időszaki pénztárjelentés generálása
     */
    public function generatePeriodReport($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        
        // Tranzakciók lekérése az időszakra
        $transactions = $this->cashRegister->transactions()
            ->with('createdBy')
            ->whereDate('transaction_date', '>=', $startDate)
            ->whereDate('transaction_date', '<=', $endDate)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Nyitó egyenleg számítása (időszak kezdete előtti nap)
        $openingBalance = $this->cashRegister->getBalanceForDate($startDate->copy()->subDay());
        
        // Összesítések
        $incomeCount = $transactions->where('type', 'income')->count();
        $expenseCount = $transactions->where('type', 'expense')->count();
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $totalTurnover = $totalIncome + $totalExpense;
        $closingBalance = $openingBalance + $totalIncome - $totalExpense;
        
        // Futó egyenleg számítása soronként
        $runningBalance = $openingBalance;
        $transactionsWithBalance = $transactions->map(function ($transaction) use (&$runningBalance) {
            if ($transaction->type === 'income') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }
            
            $transaction->running_balance = $runningBalance;
            return $transaction;
        });
        
        $data = [
            'cashRegister' => $this->cashRegister,
            'currency' => $this->cashRegister->currency,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateFormatted' => $startDate->format('Y. m. d.'),
            'endDateFormatted' => $endDate->format('Y. m. d.'),
            'transactions' => $transactionsWithBalance,
            'openingBalance' => $openingBalance,
            'openingBalanceFormatted' => $this->formatAmount($openingBalance),
            'closingBalance' => $closingBalance,
            'closingBalanceFormatted' => $this->formatAmount($closingBalance),
            'incomeCount' => $incomeCount,
            'expenseCount' => $expenseCount,
            'totalIncome' => $totalIncome,
            'totalIncomeFormatted' => $this->formatAmount($totalIncome),
            'totalExpense' => $totalExpense,
            'totalExpenseFormatted' => $this->formatAmount($totalExpense),
            'totalTurnover' => $totalTurnover,
            'totalTurnoverFormatted' => $this->formatAmount($totalTurnover),
            'reportNumber' => $this->generateReportNumber($startDate, $endDate),
        ];
        
        $pdf = Pdf::loadView('pdf.cash-register-period-report', $data);
        $pdf->setPaper('A4', 'landscape'); // Fekvő formátum a jobb áttekinthetőségért
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        return $pdf;
    }
    /**
     * Jelentésszám generálás
     */
    protected function generateReportNumber($startDate, $endDate)
    {
        $startFormatted = $startDate->format('ymd');
        $endFormatted = $endDate->format('ymd');
        $sequence = rand(100, 999); // Véletlenszám, de lehetne számláló is
        
        return $this->cashRegister->prefix . '-JELENTES-' . $startFormatted . '-' . $endFormatted . '/' . $sequence;
    }
}