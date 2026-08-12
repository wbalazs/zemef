<?php
// app/Http/Controllers/DenominationController.php

namespace App\Http\Controllers;

use App\Models\Denomination;
use App\Models\DataTransferObjects\DenominationData;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Models\CashRegister;

class DenominationController extends Controller
{
    public function index(Request $request)
    {
        $currencyId = $request->get('currency_id');
        
        $denominations = Denomination::with('currency')
            ->when($currencyId, function ($query) use ($currencyId) {
                return $query->where('currency_id', $currencyId);
            })
            ->get();
            
        $currencies = Currency::all();
        
        return view('denominations.index', compact('denominations', 'currencies', 'currencyId'));
    }
    
    public function create(Request $request)
    {
        $currencies = Currency::all();
        $selectedCurrency = $request->get('currency_id');
        
        return view('denominations.create', compact('currencies', 'selectedCurrency'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'currency_id' => 'required|exists:master_currencies,currency_id',
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:coin,banknote',
            'is_active' => 'boolean'
        ]);
        
        $data = DenominationData::fromArray($validated);
        
        Denomination::create($data->toArray());
        
        return redirect()->route('denominations.index', ['currency_id' => $data->currency_id])
            ->with('success', 'Címlet sikeresen létrehozva!');
    }
    
    public function edit(Denomination $denomination)
    {
        $currencies = Currency::all();
        
        return view('denominations.edit', compact('denomination', 'currencies'));
    }
    
    public function update(Request $request, Denomination $denomination)
    {
        $validated = $request->validate([
            'currency_id' => 'required|exists:master_currencies,currency_id',
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'type' => 'required|in:coin,banknote',
            'is_active' => 'boolean'
        ]);
        
        $data = DenominationData::fromArray($validated);
        $denomination->update($data->toArray());
        
        return redirect()->route('denominations.index', ['currency_id' => $data->currency_id])
            ->with('success', 'Címlet sikeresen módosítva!');
    }
    
    public function destroy(Denomination $denomination)
    {
        $currencyId = $denomination->currency_id;
        $denomination->delete();
        
        return redirect()->route('denominations.index', ['currency_id' => $currencyId])
            ->with('success', 'Címlet sikeresen törölve!');
    }
}