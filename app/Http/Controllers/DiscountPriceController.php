<?php
// app/Http/Controllers/DiscountPriceController.php

namespace App\Http\Controllers;

use App\Models\Package\DiscountPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountPriceController extends Controller
{
    public function index()
    {
        $discountPrices = DiscountPrice::orderBy('created_at', 'desc')->get();
        
        return view('discount-price.index', compact('discountPrices'));
    }

    public function create()
    {
        return view('discount-price.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:discount_prices,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        DiscountPrice::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'usage_limit' => $request->usage_limit,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('discount-price.index')
            ->with('success', 'Kedvezmény sikeresen létrehozva!');
    }

    public function show(DiscountPrice $discountPrice)
    {
        $discountPrice->load('packages.order');
        
        return view('discount-price.show', compact('discountPrice'));
    }

    public function edit(DiscountPrice $discountPrice)
    {
        return view('discount-price.edit', compact('discountPrice'));
    }

    public function update(Request $request, DiscountPrice $discountPrice)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:discount_prices,code,' . $discountPrice->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $discountPrice->update([
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'min_order_amount' => $request->min_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'valid_from' => $request->valid_from,
            'valid_to' => $request->valid_to,
            'usage_limit' => $request->usage_limit,
            'description' => $request->description,
            'status' => $request->status ?? $discountPrice->status,
        ]);

        return redirect()->route('discount-price.index')
            ->with('success', 'Kedvezmény sikeresen frissítve!');
    }

    public function destroy(DiscountPrice $discountPrice)
    {
        // Check if discount price is used in packages
        if ($discountPrice->packages()->exists()) {
            return redirect()->route('discount-price.index')
                ->with('error', 'Nem törölhető, mert használatban van csomagokban!');
        }

        $discountPrice->delete();

        return redirect()->route('discount-price.index')
            ->with('success', 'Kedvezmény sikeresen törölve!');
    }

    public function toggleStatus(DiscountPrice $discountPrice)
    {
        $discountPrice->update(['status' => !$discountPrice->status]);

        $status = $discountPrice->status ? 'aktiválva' : 'deaktiválva';
        return redirect()->route('discount-price.index')
            ->with('success', "Kedvezmény sikeresen {$status}!");
    }

    public function resetUsage(DiscountPrice $discountPrice)
    {
        $discountPrice->update(['used_count' => 0]);

        return redirect()->route('discount-price.index')
            ->with('success', 'Felhasználási számláló sikeresen visszaállítva!');
    }

    public function duplicate(DiscountPrice $discountPrice)
    {
        $newDiscount = $discountPrice->replicate();
        $newDiscount->code = $discountPrice->code . '-COPY-' . time();
        $newDiscount->name = $discountPrice->name . ' (másolat)';
        $newDiscount->used_count = 0;
        $newDiscount->save();

        return redirect()->route('discount-price.index')
            ->with('success', 'Kedvezmény sikeresen másolva!');
    }
}