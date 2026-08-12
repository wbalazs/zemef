<?php
// app/Http/Controllers/CashTransactionController.php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\CashTransaction;
use App\Models\Denomination;
use App\Services\CashRegister\SingleCashVoucherPDF;
use App\Models\DataTransferObjects\CashTransactionData;
use App\Services\CashRegister\CreateCashTransaction;
use App\Services\CashRegister\AttachFileToTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashTransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Bejelentkezett felhasználó pénztárai (employee_id alapján)
        $cashRegisters = CashRegister::with('currency')
            ->whereIn('business_id', Auth::user()->employee?->businesses?->pluck('business_id')->toArray() ?? [])
            ->where('is_active', true)
            
            ->get();
            
        $selectedRegisterId = $request->get('cash_register_id', $cashRegisters->first()?->cash_register_id);
        
        $transactions = collect();
        $cashRegister = null;
        $previousDayBalance = 0;
        $denominations = collect();
        
        if ($selectedRegisterId) {
            $cashRegister = CashRegister::with('currency')->find($selectedRegisterId);
            
            $transactions = CashTransaction::with('createdBy')
                ->where('cash_register_id', $selectedRegisterId)
                ->orderBy('transaction_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
                
            // Előző napi záró egyenleg
            $yesterday = Carbon::yesterday();
            $previousDayBalance = $cashRegister->getBalanceForDate($yesterday);
            
            // Címletek a pénztár devizájához
            $denominations = Denomination::where('currency_id', $cashRegister->currency_id)
                ->where('is_active', true)
                ->orderBy('value', 'desc')
                ->get();
        }
        
        return view('cash-transactions.index', compact(
            'cashRegisters',
            'selectedRegisterId',
            'cashRegister',
            'transactions',
            'previousDayBalance',
            'denominations'
        ));
    }
    
    public function store(Request $request)
    {
        $cashRegister = CashRegister::findOrFail($request->cash_register_id);
        
        $rules = [
            'cash_register_id' => 'required|exists:cash_registers,cash_register_id',
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'payer_payee' => 'required|string|max:255',
            'reason' => 'required|string|max:255',
            'reason_custom' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric|min:0',
            'denominations' => 'nullable|array',
            'print_voucher' => 'nullable|boolean' // Új mező a nyomtatáshoz
        ];
        
        // Deviza specifikus validáció
        if ($cashRegister->currency && $cashRegister->currency->code === 'HUF') {
            $rules['amount'] = 'required|integer|min:0';
        }
        
        $validated = $request->validate($rules);
        
        // DTO létrehozása
        $data = CashTransactionData::fromArray(array_merge($validated, [
            'document_number' => '', // Generálva lesz
            'created_by' => Auth::id(),
            'balance_before' => 0, // Számolva lesz
            'balance_after' => 0 // Számolva lesz
        ]));
        
        try {
            $transaction = CreateCashTransaction::from($data)->handle();
            
            // Mellékletek feltöltése
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    (new AttachFileToTransaction($transaction, $file))->handle();
                }
            }
            
            // Ha a felhasználó nyomtatni szeretne, generáljuk a PDF-et
        if ($request->has('print_voucher') && $request->print_voucher) {
            $pdfGenerator = new SingleCashVoucherPDF($transaction);
            $pdf = $pdfGenerator->generate();
            
            // PDF megjelenítése letöltés helyett
            return $pdf->stream($transaction->document_number . '.pdf');
        }

            return redirect()->route('cash-transactions.index', ['cash_register_id' => $cashRegister->cash_register_id])
                ->with('success', 'Tranzakció sikeresen rögzítve! Bizonylatszám: ' . $transaction->document_number);
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Hiba történt: ' . $e->getMessage());
        }
    }
    
    public function show(CashTransaction $cashTransaction)
    {
        $cashTransaction->load(['cashRegister.currency', 'createdBy', 'attachments']);
        
        return view('cash-transactions.show', compact('cashTransaction'));
    }
    
    public function getDenominations(Request $request)
    {
        $currencyId = $request->get('currency_id');
        
        $denominations = Denomination::where('currency_id', $currencyId)
            ->where('is_active', true)
            ->orderBy('value', 'desc')
            ->get();
            
        return response()->json($denominations);
    }
    public function printVoucher(CashTransaction $cashTransaction)
    {
        $pdfGenerator = new CashVoucherPDF($cashTransaction);
        $pdf = $pdfGenerator->generate();
        
        return $pdf->stream($cashTransaction->document_number . '.pdf');
    }
}