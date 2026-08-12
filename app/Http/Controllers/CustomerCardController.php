<?php
// app/Http/Controllers/CustomerCardController.php

namespace App\Http\Controllers;

use App\Models\CustomerCard\CustomerCard;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerCardController extends Controller
{
    /**
     * Display a listing of the customer cards
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $customerCards = CustomerCard::with(['businesses', 'customers'])
            ->latest()
            ->get();

        return view('customercard.index', ['customerCards' => $customerCards]);
    }

    /**
     * Show the form for creating a new customer card
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $businesses = Business::all();
        $customers = Customer::all();
        $products = Inventory::all();
        $productTypes = [];
        $productGroups = [];

        return view('customercard.create', compact(
            'businesses', 
            'customers', 
            'products', 
            'productTypes', 
            'productGroups'
        ));
    }

    /**
     * Store a newly created customer card
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:customer_cards,code',
            'type' => 'required|in:percent,price,product_item',
            'value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'businesses' => 'nullable|array',
            'customers' => 'nullable|array',
            'products' => 'nullable|array',
            'product_type_rules' => 'nullable|array',
            'product_group_rules' => 'nullable|array',
        ]);

        // Create the customer card
        $customerCard = CustomerCard::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Attach related models
        if (isset($validated['businesses'])) {
            $customerCard->businesses()->attach($validated['businesses']);
        }

        if (isset($validated['customers'])) {
            $customerCard->customers()->attach($validated['customers']);
        }

        if (isset($validated['products'])) {
            $customerCard->products()->attach($validated['products']);
        }

        // Create product type rules
        if (isset($validated['product_type_rules'])) {
            foreach ($validated['product_type_rules'] as $productTypeId => $percent) {
                if ($percent) {
                    $customerCard->productTypeRules()->create([
                        'product_type_id' => $productTypeId,
                        'percent' => $percent,
                    ]);
                }
            }
        }

        // Create product group rules
        if (isset($validated['product_group_rules'])) {
            foreach ($validated['product_group_rules'] as $productGroupId => $percent) {
                if ($percent) {
                    $customerCard->productGroupRules()->create([
                        'product_group_id' => $productGroupId,
                        'percent' => $percent,
                    ]);
                }
            }
        }

        return redirect()->route('customercard.index')
            ->with('success', 'Kártya sikeresen létrehozva!');
    }

    /**
     * Display the specified customer card
     *
     * @param  \App\Models\CustomerCard  $customerCard
     * @return \Illuminate\View\View
     */
    public function show(CustomerCard $customercard): View
    {
        $customercard->load(['businesses', 'customers', 'products', 'productTypeRules.productType', 'productGroupRules.productGroup']);
        
        return view('customercard.show', compact('customercard'));
    }

    /**
     * Show the form for editing the specified customer card
     *
     * @param  \App\Models\CustomerCard  $customerCard
     * @return \Illuminate\View\View
     */
    public function edit(CustomerCard $customercard): View
    {
        $customercard->load(['businesses', 'customers', 'products', 'productTypeRules', 'productGroupRules']);
        
        $businesses = Business::all();
        $customers = Customer::all();
        $products = Inventory::all();
        $productTypes = [];
        $productGroups = [];

        return view('customercard.edit', compact(
            'customercard',
            'businesses', 
            'customers', 
            'products', 
            'productTypes', 
            'productGroups'
        ));
    }

    /**
     * Update the specified customer card
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerCard  $customerCard
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $customerCard = CustomerCard::find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'type' => 'required|in:percent,price,product_item',
            'value' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'businesses' => 'nullable|array',
            'customers' => 'nullable|array',
            'products' => 'nullable|array',
            'product_type_rules' => 'nullable|array',
            'product_group_rules' => 'nullable|array',
        ]);

        // Update the customer card
        $customerCard->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Sync related models
        $customerCard->businesses()->sync($validated['businesses'] ?? []);
        $customerCard->customers()->sync($validated['customers'] ?? []);
        $customerCard->products()->sync($validated['products'] ?? []);

        // Update product type rules
        $customerCard->productTypeRules()->delete();
        if (isset($validated['product_type_rules'])) {
            foreach ($validated['product_type_rules'] as $productTypeId => $percent) {
                if ($percent) {
                    $customerCard->productTypeRules()->create([
                        'product_type_id' => $productTypeId,
                        'percent' => $percent,
                    ]);
                }
            }
        }

        // Update product group rules
        $customerCard->productGroupRules()->delete();
        if (isset($validated['product_group_rules'])) {
            foreach ($validated['product_group_rules'] as $productGroupId => $percent) {
                if ($percent) {
                    $customerCard->productGroupRules()->create([
                        'product_group_id' => $productGroupId,
                        'percent' => $percent,
                    ]);
                }
            }
        }

        return redirect()->route('customercard.index')
            ->with('success', 'Kártya sikeresen frissítve!');
    }

    /**
     * Remove the specified customer card
     *
     * @param  \App\Models\CustomerCard  $customerCard
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CustomerCard $customercard)
    {
        $customercard->delete();

        return redirect()->route('customercard.index')
            ->with('success', 'Kártya sikeresen törölve!');
    }

    /**
     * Get customer cards for a specific customer and business
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getValidCards(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'business_id' => 'required|exists:businesses,id',
        ]);

        $validCards = CustomerCard::active()
            ->validForDate()
            ->validForBusiness($request->business_id)
            ->validForCustomer($request->customer_id)
            ->get();

        return response()->json($validCards);
    }

    /**
     * Calculate discount for a product
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateDiscount(Request $request)
    {
        $request->validate([
            'customer_card_id' => 'required|exists:customer_cards,id',
            'product_id' => 'required|exists:inventory,inventory_id',
            'quantity' => 'required|integer|min:1',
            'business_id' => 'nullable|exists:businesses,id',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $customerCard = CustomerCard::find($request->customer_card_id);
        $product = Inventory::find($request->product_id);

        if (!$customerCard->isValidFor($request->business_id, $request->customer_id)) {
            return response()->json([
                'discount' => 0,
                'message' => 'A kártya nem érvényes a megadott feltételek mellett.'
            ]);
        }

        $discount = $customerCard->calculateDiscount($product, $request->quantity);

        return response()->json([
            'discount' => $discount,
            'formatted_discount' => number_format($discount, 0, ',', ' ') . ' HUF',
            'type' => $customerCard->type
        ]);
    }
}