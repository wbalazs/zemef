<?php
// app/Http/Controllers/CashRegisterController.php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\DataTransferObjects\CashRegisterData;
use App\Models\Currency;
use App\Models\Business;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CashRegister\CashVoucherPDF;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index()
    {
        $cashRegisters = CashRegister::with(['currency', 'business', 'employee'])
            //->whereIn('business_id', auth()->user()->employee?->businesses?->pluck('business_id')->toArray())
            ->get();
            
        return view('cash-register.index', compact('cashRegisters'));
    }
    
    public function create()
    {
        $currencies = Currency::all();
        $businesses = Business::all();
        $employees = Employee::all();
        
        return view('cash-register.create', compact('currencies', 'businesses', 'employees'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:10|unique:cash_registers',
            'currency_id' => 'required|exists:master_currencies,currency_id',
            'opening_balance' => 'required|numeric|min:0',
            'opening_balance_date' => 'required|date',
            'business_id' => 'required|exists:businesses,business_id',
            'employee_id' => 'nullable|exists:employees,employee_id',
            'is_active' => 'boolean'
        ]);
        
        $data = CashRegisterData::fromArray($validated);
        
        CashRegister::create($data->toArray());
        
        return redirect()->route('cash-registers.index')
            ->with('success', 'Pénztár sikeresen létrehozva!');
    }
    
    public function edit(CashRegister $cashRegister)
    {
        $currencies = Currency::all();
        $businesses = Business::all();
        $employees = Employee::all();
        
        return view('cash-register.edit', compact('cashRegister', 'currencies', 'businesses', 'employees'));
    }
    
    public function update(Request $request, CashRegister $cashRegister)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:10|unique:cash_registers,prefix,' . $cashRegister->cash_register_id . ',cash_register_id',
            'currency_id' => 'required|exists:master_currencies,currency_id',
            'opening_balance' => 'required|numeric|min:0',
            'opening_balance_date' => 'required|date',
            'business_id' => 'required|exists:businesses,business_id',
            'employee_id' => 'nullable|exists:employees,employee_id',
            'is_active' => 'boolean'
        ]);
        
        $cashRegister->update($validated);
        
        return redirect()->route('cash-registers.index')
            ->with('success', 'Pénztár sikeresen módosítva!');
    }
    
    public function destroy(CashRegister $cashRegister)
    {
        if ($cashRegister->transactions()->exists()) {
            return back()->with('error', 'Nem törölhető, mert már vannak tranzakciói!');
        }
        
        $cashRegister->delete();
        
        return redirect()->route('cash-registers.index')
            ->with('success', 'Pénztár sikeresen törölve!');
    }

    public function printVoucherOld(CashRegister $cashRegister, Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense,both',
            'date' => 'nullable|date'
        ]);
        
        $date = $request->get('date', now());
        
        $pdfGenerator = new CashVoucherPDF($cashRegister, $request->type, $date);
        $pdf = $pdfGenerator->generate();
        
        $typeName = $request->type;
        $dateFormatted = Carbon::parse($date)->format('Ymd');
        
        return $pdf->stream(
            $cashRegister->prefix . '_' . $typeName . '_' . $dateFormatted . '.pdf'
        );
    }
    public function printVoucher(CashRegister $cashRegister, Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense,both,period',
            'date' => 'required_if:type,income,expense,both|nullable|date',
            'start_date' => 'required_if:type,period|nullable|date',
            'end_date' => 'required_if:type,period|nullable|date|after_or_equal:start_date',
        ]);
        
        $pdfGenerator = new CashVoucherPDF($cashRegister, $request->type, $request->date);
        
        if ($request->type === 'period') {
            // Alapértelmezett egy hónapos intervallum
            $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
            $endDate = $request->end_date ?? now()->format('Y-m-d');
            
            $pdf = $pdfGenerator->generatePeriodReport($startDate, $endDate);
            
            $fileName = $cashRegister->prefix . '_period_' . 
                        Carbon::parse($startDate)->format('Ymd') . '_' . 
                        Carbon::parse($endDate)->format('Ymd') . '.pdf';
        } else {
            $pdf = $pdfGenerator->generate();
            $date = $request->get('date', now());
            $fileName = $cashRegister->prefix . '_' . $request->type . '_' . 
                        Carbon::parse($date)->format('Ymd') . '.pdf';
        }
        
        return $pdf->stream($fileName);
    }
}