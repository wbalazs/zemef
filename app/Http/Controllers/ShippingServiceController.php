<?php

namespace App\Http\Controllers;

use App\Models\Package\ShippingService;
use Illuminate\Http\Request;

class ShippingServiceController extends Controller
{
    public function index()
    {
        $shippingServices = ShippingService::orderBy('sort_order')->orderBy('name')->get();

        return view('shipping-service.index', compact('shippingServices'));
    }

    public function create()
    {
        return view('shipping-service.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_services,code',
            'base_price' => 'required|numeric|min:0',
            'supports_cod' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $data['supports_cod'] = $request->boolean('supports_cod');
        $data['status'] = $request->boolean('status', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        ShippingService::create($data);

        return redirect()->route('shipping-service.index')
            ->with('success', 'Szállítási szolgáltató sikeresen létrehozva!');
    }

    public function edit(ShippingService $shippingService)
    {
        return view('shipping-service.edit', compact('shippingService'));
    }

    public function update(Request $request, ShippingService $shippingService)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_services,code,' . $shippingService->id,
            'base_price' => 'required|numeric|min:0',
            'supports_cod' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $data['supports_cod'] = $request->boolean('supports_cod');
        $data['status'] = $request->boolean('status');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $shippingService->update($data);

        return redirect()->route('shipping-service.index')
            ->with('success', 'Szállítási szolgáltató sikeresen frissítve!');
    }

    public function destroy(ShippingService $shippingService)
    {
        if ($shippingService->packages()->exists()) {
            return redirect()->route('shipping-service.index')
                ->with('error', 'Nem törölhető, mert már van hozzá kapcsolt csomag!');
        }

        $shippingService->delete();

        return redirect()->route('shipping-service.index')
            ->with('success', 'Szállítási szolgáltató sikeresen törölve!');
    }

    public function toggleStatus(ShippingService $shippingService)
    {
        $shippingService->update(['status' => !$shippingService->status]);

        $status = $shippingService->status ? 'aktiválva' : 'deaktiválva';

        return redirect()->route('shipping-service.index')
            ->with('success', "Szállítási szolgáltató sikeresen {$status}!");
    }
}
