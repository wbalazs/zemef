<?php

namespace App\Http\Controllers;

use App\Models\Package\Package;
use App\Models\Package\CashOnDelivery;
use App\Models\Package\PackageSize;
use App\Models\Package\DiscountPrice;
use App\Models\Package\ShippingService;
use App\Models\Inventory;
use App\Models\WebshopOrder;
use App\Models\WebshopOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with(['order', 'items', 'packageSize', 'discountPrice', 'cashOnDelivery', 'shippingService'])
            ->latest()
            ->paginate(20);

        return view('package.index', compact('packages'));
    }

    public function create()
    {
        // Get orders that need packaging (based on your shipping condition)
        $orders = WebshopOrder::where('status', 'processing')
        ->with(['items'])
        ->get();
        
        $packageSizes = PackageSize::where('status', true)->get();
        $discountPrices = DiscountPrice::where('status', true)->get();
        $shippingServices = ShippingService::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('package.create', compact('orders', 'packageSizes', 'discountPrices', 'shippingServices'));
    }

    public function show(Request $request)
    {
    }

    public function store(Request $request)
    {
    
        $request->validate([
            'order_id' => 'required|exists:webshop_orders,id',
            'shipping_service_id' => 'required|exists:shipping_services,id',
            'shipping_price' => 'nullable|numeric|min:0',
            'tracking_number' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'package_size_id' => 'nullable|exists:package_sizes,id',
            'discount_price_id' => 'nullable|exists:discount_prices,id',
            'status' => 'required|in:created,shipped,delivered,cancelled',
            'items' => 'required|array',
            'items.*.order_item_id' => 'nullable|integer|exists:webshop_order_items,id',
            'items.*.itemable_type' => 'nullable|string|in:webshop_order_item,inventory,' . WebshopOrderItem::class . ',' . Inventory::class,
            'items.*.itemable_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
            // Cash on delivery validation is checked dynamically by service capabilities.
            'cash_on_delivery_amount' => 'nullable|numeric|min:0',
            'cash_on_delivery_bank_account' => 'nullable|string|max:255',
            'cash_on_delivery_recipient' => 'nullable|string|max:255',
            'cash_on_delivery_bank_name' => 'nullable|string|max:255',
            'cash_on_delivery_notes' => 'nullable|string',
        ]);

        $shippingService = ShippingService::query()->findOrFail((int) $request->shipping_service_id);
        $shippingPrice = $request->filled('shipping_price') ? (float) $request->shipping_price : (float) $shippingService->base_price;

        $this->validateCashOnDelivery($request, $shippingService);

        DB::transaction(function() use ($request, $shippingService, $shippingPrice) {
            $package = Package::create([
                'order_id' => $request->order_id,
                'shipping_service_id' => $shippingService->id,
                // Keep legacy string column for backwards compatibility.
                'shipping_service' => $shippingService->code,
                'shipping_price' => $shippingPrice,
                'tracking_number' => $request->tracking_number,
                'weight' => $request->weight,
                'notes' => $request->notes,
                'package_size_id' => $request->package_size_id,
                'discount_price_id' => $request->discount_price_id,
                'status' => $request->status,
            ]);

            foreach ($request->items as $item) {
                [$itemableType, $itemableId] = $this->resolvePackageItemable($item);

                $package->items()->create([
                    'itemable_type' => $itemableType,
                    'itemable_id' => $itemableId,
                    // Keep legacy column in sync while old code paths still use it.
                    'order_item_id' => $itemableType === WebshopOrderItem::class ? $itemableId : null,
                    'quantity' => $item['quantity'],
                ]);
            }

            // Create cash on delivery record if selected service supports COD
            if ($shippingService->supports_cod && $request->filled('cash_on_delivery_amount')) {
                CashOnDelivery::create([
                    'package_id' => $package->id,
                    'amount' => $request->cash_on_delivery_amount,
                    'bank_account_number' => $request->cash_on_delivery_bank_account,
                    'recipient_name' => $request->cash_on_delivery_recipient,
                    'bank_name' => $request->cash_on_delivery_bank_name,
                    'notes' => $request->cash_on_delivery_notes,
                    'status' => 'pending',
                ]);
            }

            // Increment discount usage
            if ($request->discount_price_id) {
                DiscountPrice::find($request->discount_price_id)->incrementUsage();
            }
        });

        return redirect()->route('package.index')
            ->with('success', 'Csomag sikeresen létrehozva!');
    }

    public function edit(Package $package)
    {
        $package->load(['order.items', 'items.itemable', 'packageSize', 'discountPrice', 'cashOnDelivery', 'shippingService']);

        $packageSizes = PackageSize::active()->get();
        
        $discountPrices = DiscountPrice::active()->get();

        $shippingServices = ShippingService::active()->get();

        return view('package.edit', compact('package', 'packageSizes', 'discountPrices', 'shippingServices'));
    }
public function update(Request $request, Package $package)
{
    $request->validate([
        'shipping_service_id' => 'required|exists:shipping_services,id',
        'shipping_price' => 'nullable|numeric|min:0',
        'tracking_number' => 'nullable|string|max:100',
        'weight' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string',
        'package_size_id' => 'nullable|exists:package_sizes,id',
        'discount_price_id' => 'nullable|exists:discount_prices,id',
        'status' => 'required|in:created,shipped,delivered,cancelled',
        // Cash on delivery validation
        'cash_on_delivery_amount' => 'nullable|numeric|min:0',
        'cash_on_delivery_bank_account' => 'nullable|string|max:255',
        'cash_on_delivery_recipient' => 'nullable|string|max:255',
        'cash_on_delivery_bank_name' => 'nullable|string|max:255',
        'cash_on_delivery_notes' => 'nullable|string',
    ]);

    $shippingService = ShippingService::query()->findOrFail((int) $request->shipping_service_id);
    $shippingPrice = $request->filled('shipping_price') ? (float) $request->shipping_price : (float) $shippingService->base_price;

    $this->validateCashOnDelivery($request, $shippingService);

    DB::transaction(function() use ($request, $package, $shippingService, $shippingPrice) {
        $oldDiscountId = $package->discount_price_id;
        
        $package->update([
            'shipping_service_id' => $shippingService->id,
            'shipping_service' => $shippingService->code,
            'shipping_price' => $shippingPrice,
            'tracking_number' => $request->tracking_number,
            'weight' => $request->weight,
            'notes' => $request->notes,
            'package_size_id' => $request->package_size_id,
            'discount_price_id' => $request->discount_price_id,
            'status' => $request->status,
        ]);

        // Handle cash on delivery
        if ($shippingService->supports_cod && $request->filled('cash_on_delivery_amount')) {
            if ($package->cashOnDelivery) {
                $package->cashOnDelivery->update([
                    'amount' => $request->cash_on_delivery_amount,
                    'bank_account_number' => $request->cash_on_delivery_bank_account,
                    'recipient_name' => $request->cash_on_delivery_recipient,
                    'bank_name' => $request->cash_on_delivery_bank_name,
                    'notes' => $request->cash_on_delivery_notes,
                ]);
            } else {
                CashOnDelivery::create([
                    'package_id' => $package->id,
                    'amount' => $request->cash_on_delivery_amount,
                    'bank_account_number' => $request->cash_on_delivery_bank_account,
                    'recipient_name' => $request->cash_on_delivery_recipient,
                    'bank_name' => $request->cash_on_delivery_bank_name,
                    'notes' => $request->cash_on_delivery_notes,
                    'status' => 'pending',
                ]);
            }
        } elseif (!$shippingService->supports_cod && $package->cashOnDelivery) {
            // Remove cash on delivery if selected shipping service does not support COD
            $package->cashOnDelivery->delete();
        }

        // Handle discount price usage
        if ($oldDiscountId != $request->discount_price_id) {
            if ($oldDiscountId) {
                DiscountPrice::where('id', $oldDiscountId)->decrement('used_count');
            }
            if ($request->discount_price_id) {
                DiscountPrice::where('id', $request->discount_price_id)->increment('used_count');
            }
        }

        // Handle status changes with timestamps
        if ($request->status === 'shipped' && !$package->shipped_at) {
            $package->update(['shipped_at' => now()]);
        } elseif ($request->status === 'delivered' && !$package->delivered_at) {
            $package->update(['delivered_at' => now()]);
        }
    });

    return redirect()->route('package.index')
        ->with('success', 'Csomag sikeresen frissítve!');
}

public function markAsShipped(Package $package)
{
    $package->update([
        'status' => 'shipped',
        'shipped_at' => now(),
    ]);

    return redirect()->route('package.edit', $package)
        ->with('success', 'Csomag státusza "Kiszállítva"-ra módosítva!');
}

public function markAsDelivered(Package $package)
{
    $package->update([
        'status' => 'delivered',
        'delivered_at' => now(),
    ]);

    return redirect()->route('package.edit', $package)
        ->with('success', 'Csomag státusza "Kézbesítve"-re módosítva!');
}
    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('package.index')
            ->with('success', 'Csomag sikeresen törölve!');
    }

    
    

    public function getOrderItems(WebshopOrder $order)
    {
        $order->load('items');
        
        return response()->json($order->items);
    }

    protected function resolvePackageItemable(array $item): array
    {
        if (!empty($item['order_item_id'])) {
            $id = (int) $item['order_item_id'];

            if (!WebshopOrderItem::query()->whereKey($id)->exists()) {
                abort(422, 'A kiválasztott webshop rendeléstétel nem található.');
            }

            return [WebshopOrderItem::class, $id];
        }

        if (empty($item['itemable_type']) || empty($item['itemable_id'])) {
            abort(422, 'Minden csomagtételhez meg kell adni a forrás tételt.');
        }

        $normalizedType = $this->normalizeItemableType($item['itemable_type']);
        $id = (int) $item['itemable_id'];

        if ($normalizedType === WebshopOrderItem::class && !WebshopOrderItem::query()->whereKey($id)->exists()) {
            abort(422, 'A kiválasztott webshop rendeléstétel nem található.');
        }

        if ($normalizedType === Inventory::class && !Inventory::query()->where('inventory_id', $id)->exists()) {
            abort(422, 'A kiválasztott inventory tétel nem található.');
        }

        return [$normalizedType, $id];
    }

    protected function normalizeItemableType(string $itemableType): string
    {
        if ($itemableType === 'webshop_order_item') {
            return WebshopOrderItem::class;
        }

        if ($itemableType === 'inventory') {
            return Inventory::class;
        }

        return $itemableType;
    }

    protected function validateCashOnDelivery(Request $request, ShippingService $shippingService): void
    {
        if (!$shippingService->supports_cod) {
            return;
        }

        $request->validate([
            'cash_on_delivery_amount' => 'required|numeric|min:0',
            'cash_on_delivery_bank_account' => 'required|string|max:255',
            'cash_on_delivery_recipient' => 'required|string|max:255',
            'cash_on_delivery_bank_name' => 'nullable|string|max:255',
            'cash_on_delivery_notes' => 'nullable|string',
        ]);
    }
}