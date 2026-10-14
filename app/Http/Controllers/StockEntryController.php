<?php

namespace App\Http\Controllers;

use App\Enums\InventoryStatus;
use App\Enums\ItemListStatus;
use App\Models\Customer;
use App\Models\CustomerMarking;
use App\Models\CustomerAddress;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Employee;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\ZImage;
use App\Models\Inventory;
use App\Models\InventoryPicture;
use App\Models\Hallmark;
use App\Models\ProductColor;
use App\Models\ProductSign;
use App\Models\ProductStatus;
use App\Models\ProductType;
use App\Models\UnitPriceType;
use App\Models\VatType;
use App\Models\Currency;
use App\Models\Karat;
use App\Models\Category2;
use App\Models\ItemList\ItemList;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Services\StockEntry\SaveStockEntryItem;
use App\Category;
use App\Tag;
use App\User;
use App\Http\Controllers\CategoryController;
use Carbon\Carbon;
use App\Http\Requests\StockEntryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use App\Services\CashRegister\CreateAutoCashTransaction;
use App\Services\CashRegister\DataTransferObjects\AutoCashTransactionData;
use DB;
use PDF;
use File;
use Illuminate\Support\Facades\Log;

class StockEntryController extends Controller
{

    public function __construct()
    {
        ////$this->authorizeResource(StockEntry::class);
        $this->categoryController = new CategoryController();
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(StockEntry $model)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        StockEntry::where('ticket_number', '--empty_row--')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        return view('stock_entries.index', 
        [
            'stock_entries' => $model->orderBy('created_at', 'DESC')->get()
        ]);
    }

    /**
     * Show the form for creating a new item
     *
     * @param  \App\Tag $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();

        $stock_entry = StockEntry::create(['ticket_number' => '--empty_row--', 'status' => 0]);
        $stock_entry->ticket_number = '';

        $businessIds = auth()->user()->employee?->businesses?->pluck('business_id')->toArray() ?? [];

        $businesses = Business::where('status', 1)
            ->whereIn('business_id', $businessIds)
            ->orderBy('name', 'ASC')
            ->get();

        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $products = Product::orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();
        $customer_markings = collect();

        return view('stock_entries.edit', [
            'stock_entry' => $stock_entry,
            'customers' => $customers,
            'businesses' => $businesses,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_types' => $product_types,
            'product_signs' => $product_signs,
            'products' => $products,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'karats' => $karats,
            'category2s' => $category2s,
            'customer_markings' => $customer_markings,
            'inventoryItems' => [],
            'currencies' => Currency::where('status', 1)->orderBy('currency_id', 'ASC')->get()
        ]);
    }

    public function import(Request $request)
    {
        
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();

        $stock_entry = StockEntry::create(['ticket_number' => '--empty_row--']);
        $stock_entry->ticket_number = '';
        
        
        if ($request->file('file')) {
            if (($handle = fopen($request->file('file'), 'r')) !== FALSE) {
                
                while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                
                    if (empty(array_filter($data))) continue;  // Skip empty rows
                
                    $fields = explode('$', $data[0]);
                                        
                    $product = Product::firstOrCreate([
                        'name' => trim(mb_convert_encoding(trim($fields[12]), 'UTF-8', 'ISO-8859-1') ?? '')
                    ]);
                    
                    $fields[10] = str_replace(',', '.', trim($fields[10])); // Replace comma with dot: becomes "21.30"

                    StockEntryItem::create([
                        'stock_entry_id' => $stock_entry->stock_entry_id, 
                        'product_id' => $product->product_id, 
                        'main_product_id' => 0, 
                        'name' => trim(mb_convert_encoding(trim($fields[12]), 'UTF-8', 'ISO-8859-1') ?? ''), 
                        'description' => '', 
                        'quantity' => intval($fields[13] ?? ''), 
                        'oneprice' => ( intval($fields[1] ?? '') + intval($fields[2] ?? '') + intval($fields[3] ?? '') ),  
                        'price' => ( intval($fields[1] ?? '') + intval($fields[2] ?? '') + intval($fields[3] ?? '') ),  
                        'factory_number' => trim($fields[0] ?? ''),  
                        'weight' => $fields[10] ?? '', 
                    ]);
                }
            }
        }
        
        $stock_entry->load('stock_entry_items','stock_entry_items.product');

        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $products = Product::orderBy('name', 'ASC')->get();
        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();
        $customer_markings = collect();

        $businesses = Business::where('status', 1)
                                ->whereIn('business_id', auth()->user()->employee?->businesses?->pluck('business_id')->toArray())
                                    ->orderBy('name', 'ASC')
                                    ->get();

        return view('stock_entries.edit', [
            'stock_entry' => $stock_entry,
            'categories' => $categories,
            'customers' => $customers,
            'businesses' => $businesses,
            'products' => $products,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_types' => $product_types,
            'product_signs' => $product_signs,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'karats' => $karats,
            'category2s' => $category2s,
            'customer_markings' => $customer_markings,
            'show' => false,
            'currencies' => Currency::where('status', 1)->orderBy('currency_id', 'ASC')->get()
        ]);
    }

    public function createFromImport(Request $request)
    {
        SaveStockEntryItem::From($request);
        
        $item_list = ItemList::find($request->get('item_list_id'));
        
        $item_list->incImportCount($request->get('sei_factory_number'));

        return response()->json(['success' => true], 200);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\StockEntryRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StockEntryRequest $request, StockEntry $model)
    {
        $stock_entry = $model->create($request->merge([
        ])->all());

        return redirect()->route('stock_entry.index')->withStatus('Az ékszer bevételezési bizonylat sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $stock_entry
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(StockEntry $stock_entry)
    {
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();
        $businesses = Business::where('status', 1)
                                ->whereIn('business_id', auth()->user()->employee?->businesses?->pluck('business_id')->toArray())
                                ->orderBy('name', 'ASC')
                                ->get();

        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $storageId = $stock_entry->storage_id;
        $warehouse = Warehouse::find($storageId);

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();
        $customer_markings = collect();
        if (!empty($stock_entry->customer_id)) {
            $customer_markings = CustomerMarking::with('productStatus')
                ->where('customer_id', $stock_entry->customer_id)
                ->where('status', 1)
                ->orderByDesc('default')
                ->orderBy('name', 'ASC')
                ->get();
        }
        //$products = Product::orderBy('name', 'ASC')->get();

        // Az inventory tételek lekérése (source_type = 'stock_entry')
        $inventoryItems = Inventory::where('source_type', 'stock_entry')
                                ->where('source_header_id', $stock_entry->stock_entry_id)
                                ->get();

        // Az item_data-t kiegészítjük az stock_entry header_data-jával, ha üres
        $inventoryItems = $inventoryItems->map(function ($item) use ($stock_entry) {
            $itemData = $item->getAttribute('item_data') ?? [];
            
            foreach ($itemData as $key => $value) {
                if (!isset($itemData[$key]) || empty($itemData[$key])) {
                    $itemData[$key] = $value;
                }
            }
            
            // Az item_data-t beállítjuk az objektumon
            $item->setAttribute('item_data', $itemData);
            return $item;
        });
        $inventoryItems = $inventoryItems->map(function ($item) use ($stock_entry) {
            $itemData = $item->getAttribute('item_data');
            if (is_string($itemData)) $itemData = json_decode($itemData, true);
            if (!is_array($itemData)) $itemData = [];
            
            // Normalizáljuk a tömb mezőket
            $arrayFields = ['statuses', 'colors'];
            foreach ($arrayFields as $field) {
                if (isset($itemData[$field]) && !is_array($itemData[$field])) {
                    $itemData[$field] = $itemData[$field] ? [$itemData[$field]] : [];
                }
            }
            
            $item->setAttribute('item_data', $itemData);
            return $item;
        });
        $groupedInventoryItems = $inventoryItems->groupBy('inventory_id');

        return view('stock_entries.edit', [
            'stock_entry' => $stock_entry,
            'customers' => $customers,
            'businesses' => $businesses,
            'categories' => $categories,
            //'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_types' => $product_types,
            'product_signs' => $product_signs,
            'warehouse' => $warehouse,
            'currencies' => Currency::where('status', 1)->orderBy('currency_id', 'ASC')->get(),
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'karats' => $karats,
            'category2s' => $category2s,
            'customer_markings' => $customer_markings,
            //'products' => $products,
            'inventoryItems' => $inventoryItems,  // új
            'groupedInventoryItems' => $groupedInventoryItems,  // új
            'show' => false
        ]);
    }


    private function getNextSerial()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'stock_entry')
            ->where('year', date('Y'))
            ->first();

        $serial = $record ? $record->serial_number : null;

        if (!$serial) {
            DB::table('serial_numbers')
                ->insert(['module' => 'stock_entry', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }

        $next = ($serial+1);

        DB::table('serial_numbers')
            ->where('module', 'stock_entry')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);

        return $serial;
    }


    public function saveStockEntry(Request $request)
    {
        $stock_entry = StockEntry::find($request->stock_entry_id);
        $closeEntry = $request->boolean('close_entry');

        if ((int) $stock_entry->status === 2) {
            return response()->json(['message' => 'A bizonylat lezárt, nem módosítható.'], 423);
        }
        $brokenGoldInventoryIds = collect();
        $brokenGoldWarehouseId = null;
        if ($closeEntry) {
            $brokenGoldInventoryIds = $stock_entry->inventoryItems()
                ->get(['inventory_id', 'item_data'])
                ->filter(fn ($inventory) => filter_var($inventory->item_data['is_broken_gold'] ?? false, FILTER_VALIDATE_BOOLEAN))
                ->pluck('inventory_id');

            if ($brokenGoldInventoryIds->isNotEmpty()) {
                $brokenGoldWarehouseId = Warehouse::where('is_broken_gold', 1)->value('warehouse_id');
                if (!$brokenGoldWarehouseId) {
                    return response()->json(['message' => 'Nincs törtarany raktár beállítva.'], 422);
                }
            }
        }
        //$storage = Warehouse::where('is_central', '1')->first();
        //$storage = Business::find(auth()->user()->employee?->active_business_id)->warehouses()->first();
        $storage_id = $request->storage_id;
        //$request->business_id = $request->business_id;

        if(!$stock_entry->ticket_number || $stock_entry->ticket_number == '--empty_row--'){

            $serial = $this->getNextSerial();
            // Formátum: XX/EEEE/00001 - ahol XX - bolti azonosító
            $stock_entry->ticket_number = 'EBEV/'. $request->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
            $stock_entry->status = 1; // Aktívvá válik a bizonylat, megjelenik a listában
        }
       
        $customer_id = $request->customer_id;

        if(!is_numeric($customer_id)){
            $zip = Zip::where('id', $request->zip)->first();
            $city = City::where('id', $request->city)->first();

            $customer = new Customer();
            $customer->name = $customer_id;
            $customer->identity_card_number = $request->identity_card_number;
            $customer->tax_number = $request->tax_number;
            $customer->status = 1;
            $customer->save();

            $customer_address = new CustomerAddress();
            $customer_address->address_type = "Lakcím";
            $customer_address->zip = $zip->name;
            $customer_address->zip_id = $zip->id;
            $customer_address->city = $city->name;
            $customer_address->city_id = $city->id;
            $customer_address->address = $request->address;
            $customer_address->customer_id = $customer->customer_id;
            $customer_address->save();

            $customer_id = $customer->customer_id;
        }else {
            $customer = Customer::find($customer_id);
            
            $customer_address = $customer->addresses->where('address_type', 'Lakcím')->first();
            if (!$customer_address) {
                $zip = Zip::where('id', $request->zip)->first();
                $city = City::where('id', $request->city)->first();
                
                $customer_address = new CustomerAddress();
                $customer_address->address_type = "Lakcím";
                $customer_address->zip = $zip ? $zip->name : null;
                $customer_address->zip_id = $zip ? $zip->id : null;
                $customer_address->city = $city ? $city->name : null;
                $customer_address->city_id = $city ? $city->id : null;
                $customer_address->address = $request->address;
                $customer_address->customer_id = $customer->customer_id;
                $customer_address->save();
            }
            $customer->save();
        }
        $selectedCustomerMarking = null;
        if ($request->filled('customer_marking_id') && is_numeric($request->customer_marking_id) && is_numeric($customer_id)) {
            $selectedCustomerMarking = CustomerMarking::where('id', $request->customer_marking_id)
                ->where('customer_id', $customer_id)
                ->first();
        }

        $headerTermekTipusId = $request->input(
            'termek_tipus_id',
            $request->input('new_termek_tipus_id', $stock_entry->header_data['termek_tipus_id'] ?? null)
        );

        $headerData = [
            'customer_marking_id' => $selectedCustomerMarking?->id,
            'vzj' => $selectedCustomerMarking?->name,
            'product_status_id' => $selectedCustomerMarking?->product_status_id,
            'termek_tipus_id' => $headerTermekTipusId,
            'category_id' => $request->category_id,
            'beszerzesi_ar' => $request->beszerzesi_ar,
            'eladasi_ar' => $request->eladasi_ar,
            'vat_type_id' => $request->vat_type,
            'hallmarks' => $request->has('hallmarks') ? 1 : 0,
            'karat_id' => $request->karat_id,
            'label_type' => $request->label_type_default,
        ];

        $stock_entry->update(
            array_merge(
                $request->all(),
                [
                    'employee_id' => Auth::user()->employee?->employee_id,
                    'business_id' => auth()->user()->employee?->active_business_id,
                    'storage_id' => $storage_id,
                    'customer_id' => $customer_id,
                    'currency_id' => $request->currency_id,
                    'business_id' => $request->business_id,
                    'currency_value' => $request->currency_value
                ]
            )
        );

        $stock_entry->header_data = $headerData;

        if ($closeEntry) {
            $stock_entry->status = 2;
            if (empty($stock_entry->printed_at)) {
                $stock_entry->printed_at = now();
            }
        } elseif ((int) $stock_entry->status === 0) {
            $stock_entry->status = 1;
        }

        DB::transaction(function () use ($stock_entry, $brokenGoldInventoryIds, $brokenGoldWarehouseId) {
            if ($brokenGoldInventoryIds->isNotEmpty()) {
                $stock_entry->inventoryItems()
                    ->whereIn('inventory_id', $brokenGoldInventoryIds)
                    ->update([
                        'storage_id' => $brokenGoldWarehouseId,
                        'warehouse_to' => $brokenGoldWarehouseId,
                    ]);
            }

            $stock_entry->save();
        });

        if ($closeEntry) {
            try {
                $expenseAmount = (float) Inventory::where('source_type', 'stock_entry')
                    ->where('source_header_id', $stock_entry->stock_entry_id)
                    ->sum('price');

                $payerPayee = (string) ($customer->name ?? ('Partner #' . $customer_id));

                $cashTransactionData = new AutoCashTransactionData(
                    type: 'expense',
                    amount: $expenseAmount,
                    payer_payee: $payerPayee,
                    reason: 'purchase',
                    description: 'Automatikus penztar kiadas - StockEntry #' . $stock_entry->stock_entry_id
                );

                CreateAutoCashTransaction::from($cashTransactionData)->handle();
            } catch (\Throwable $e) {
                Log::error('Auto cash expense failed for stock entry close', [
                    'stock_entry_id' => $stock_entry->stock_entry_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return true;
    }

   public function saveStockEntryItem(Request $request)
    {
        $request->validate([
            'new_hallmarks' => 'nullable|in:0,1',
        ]);

        $stock_entry = StockEntry::findOrFail($request->stock_entry_id);
        Log::info('Saving stock entry item', ['stock_entry_id' => $stock_entry->stock_entry_id, 'request' => $request->all()]);
        if ((int) $stock_entry->status === 2) {
            return response()->json(['message' => 'A bizonylat lezárt, új tétel nem rögzíthető.'], 423);
        }

        $rawTermekTipusId = $request->input('new_termek_tipus_id', $request->input('new_termek_tipus'));
        if ($rawTermekTipusId === null || $rawTermekTipusId === '') {
            $rawTermekTipusId = $stock_entry->header_data['termek_tipus_id'] ?? null;
        }
        $termekTipusId = ($rawTermekTipusId === null || $rawTermekTipusId === '') ? null : (int) $rawTermekTipusId;
        $inventoryName = $termekTipusId ? (ProductType::find($termekTipusId)?->name ?? null) : null;

        //$storage = Business::find(auth()->user()->employee?->active_business_id)->warehouses()->first();
        $storage_id = $stock_entry->storage_id;

        $selectedCustomerMarking = null;
        if ($request->filled('new_customer_marking_id') && is_numeric($request->new_customer_marking_id)) {
            $selectedCustomerMarking = CustomerMarking::where('id', $request->new_customer_marking_id)
                ->where('customer_id', $stock_entry->customer_id)
                ->first();
        }
    
        // Új tétel(ek) létrehozása
        $is_unique = filter_var($request->new_sei_is_unique, FILTER_VALIDATE_BOOLEAN);
        $quantity = (int) $request->new_quantity;
        $oneprice = (int) $request->new_oneprice;
        $weight = $request->new_weight ?: 0;
        $isBrokenGold = $request->boolean('new_is_broken_gold');
        $hallmarksValue = $request->input('new_hallmarks');
        $hallmarks = $hallmarksValue === null ? null : (int) $hallmarksValue;

        if ($isBrokenGold) {
            $brokenGoldWarehouseId = Warehouse::where('is_broken_gold', 1)->value('warehouse_id');
            if (!$brokenGoldWarehouseId) {
                return response()->json(['message' => 'Nincs törtarany raktár beállítva.'], 422);
            }

            $storage_id = $brokenGoldWarehouseId;
        } elseif ($hallmarks === 0) {
            $hallmarkWarehouseId = Warehouse::where('is_hallmark', 1)->value('warehouse_id');
            if (!$hallmarkWarehouseId) {
                return response()->json(['message' => 'Nincs fémjelzés raktár beállítva.'], 422);
            }

            $storage_id = $hallmarkWarehouseId;
        }

        // Inventory tételek létrehozása
        $inventoryItems = [];
        if ($is_unique) {
            // Egyedi tételek: minden darabhoz külön inventory rekord
            for ($i = 0; $i < $quantity; $i++) {
                $sorszam = $alszam = null;
                $parentInventory = Inventory::find($request->parent_inventory_id);
                //csakakkor kérek új sorszámot, ha nincs parent inventory, 
                // vagy ha van, akkor a sorszámot a parent inventory alapján kérem, hogy megmaradjon a hierarchia
                //az alszám pedig mindig a parent inventory alapján jön, hogy a hierarchiában egymás alá kerüljenek a tételek
                if ($parentInventory) {
                    $sorszam = $parentInventory->sorszam;
                    $alszam = Inventory::getNextAlszam($stock_entry->stock_entry_id, $sorszam);
                } else{
                    $sorszam = Inventory::getNextSorszam($stock_entry->stock_entry_id);
                }
                
                $inventory = new Inventory();
                $inventory->product_id = 1;
                $inventory->sorszam = $sorszam; // új mező a sorszámnak
                $inventory->alszam = $alszam; // új mező a sorszámnak
                $inventory->code = $sorszam . '/' . $alszam; // új
                $inventory->name = $inventoryName;
                $inventory->description = $request->new_description;
                $inventory->factory_number = $request->new_factory_number;
                $inventory->weight = $weight;
                $inventory->quantity = 1;
                $inventory->oneprice = $oneprice;
                $inventory->price = $oneprice;
                $inventory->comment = $request->new_comment;
                $inventory->status = InventoryStatus::IN_STOCK->value; // raktaron
                $inventory->storage_id = $storage_id;
                $inventory->source_type = 'stock_entry';
                $inventory->source_header_id = $stock_entry->stock_entry_id;
                $inventory->warehouse_to = $storage_id;
                $inventory->termek_tipus_id = $termekTipusId;

                $itemData = [
                    'parent_inventory_id' => $request->parent_inventory_id,
                    'customer_marking_id' => $selectedCustomerMarking?->id,
                    'vzj' => $selectedCustomerMarking?->name,
                    'termek_tipus_id' => $termekTipusId,
                    'category_id' => $request->new_category_id,
                    'beszerzesi_ar' => $request->new_beszerzesi_ar,
                    'eladasi_ar' => $request->new_eladasi_ar,
                    'vat_type_id' => $request->new_vat_type,
                    'hallmarks' => $hallmarks,
                    'is_broken_gold' => $isBrokenGold ? 1 : 0,
                    'statuses' => $request->new_statuses ?? [],
                    'karat_id' => $request->new_karat_id,
                    'colors' => $request->new_colors ?? [],
                    'size' => $request->new_size,
                    'label_type' => $request->label_type,
                    'category2_id' => $request->category2_id,
                ];
                $inventory->item_data = $itemData;
                
                $inventory->save();

                $inventoryItems[] = $inventory;
            }

        } else {
            // Nem egyedi: egy inventory rekord a teljes mennyiséggel
            $sorszam = $alszam = null;
            $parentInventory = Inventory::find($request->parent_inventory_id);
            //csak akkor kérek új sorszámot, ha nincs parent inventory, 
            // vagy ha van, akkor a sorszámot a parent inventory alapján kérem, hogy megmaradjon a hierarchia
            //az alszám pedig mindig a parent inventory alapján jön, hogy a hierarchiában egymás alá kerüljenek a tételek
            if ($parentInventory) {
                $sorszam = $parentInventory->sorszam;
                $alszam = Inventory::getNextAlszam($stock_entry->stock_entry_id, $sorszam);
            } else{
                $sorszam = Inventory::getNextSorszam($stock_entry->stock_entry_id);
            }
            $inventory = new Inventory();
            $inventory->product_id = 1;
            $inventory->sorszam = $sorszam; // új mező a sorszámnak
            $inventory->alszam = $alszam; // új mező az alszámnak
            $inventory->code = $sorszam . '/' . $alszam; // új
            $inventory->name = $inventoryName;
            $inventory->description = $request->new_description;
            $inventory->factory_number = $request->new_factory_number;
            $inventory->weight = $weight;
            $inventory->quantity = $quantity;
            $inventory->oneprice = $oneprice;
            $inventory->price = $oneprice * $quantity;
            $inventory->comment = $request->new_comment;
            $inventory->status = InventoryStatus::IN_STOCK->value; // raktaron
            $inventory->storage_id = $storage_id;
            $inventory->source_type = 'stock_entry';
            $inventory->source_header_id = $stock_entry->stock_entry_id;
            $inventory->warehouse_to = $storage_id;
            $inventory->termek_tipus_id = $termekTipusId;

            $itemData = [
                'parent_inventory_id' => $request->parent_inventory_id,
                'customer_marking_id' => $selectedCustomerMarking?->id,
                'vzj' => $selectedCustomerMarking?->name,
                'termek_tipus_id' => $termekTipusId,
                'category_id' => $request->new_category_id,
                'beszerzesi_ar' => $request->new_beszerzesi_ar,
                'eladasi_ar' => $request->new_eladasi_ar,
                'vat_type_id' => $request->new_vat_type,
                'hallmarks' => $hallmarks,
                'is_broken_gold' => $isBrokenGold ? 1 : 0,
                'statuses' => $request->new_statuses ?? [],
                'karat_id' => $request->new_karat_id,
                'colors' => $request->new_colors ?? [],
                'size' => $request->new_size,
                'label_type' => $request->label_type,
                'category2_id' => $request->category2_id,
            ];
            $inventory->item_data = $itemData;
            
            $inventory->save();

            $inventoryItems[] = $inventory;
        }

         

        // Nyomtatás kezelése
        if ($request->boolean('print')) {
            $stock_entry->printed_at = now();
            $stock_entry->save();
            session()->flash('print', $stock_entry->stock_entry_id);
        }

        // Ha ez a tétel egy kényszerlista sorból lett átemelve, a sort most jelöljük "termékké alakítva" státuszra.
        if ($request->filled('item_list_id') && $request->filled('item_list_sor')) {
            $itemList = ItemList::find($request->item_list_id);
            if ($itemList) {
                $rows = $itemList->json['tetelek'] ?? [];
                foreach ($rows as $index => $row) {
                    if ((int) ($row['sor'] ?? -1) === (int) $request->item_list_sor) {
                        $rows[$index]['status'] = ItemListStatus::PRODUCT->value;
                        $rows[$index]['stock_entry_id'] = $stock_entry->stock_entry_id;
                        break;
                    }
                }
                $json = $itemList->json ?? [];
                $json['tetelek'] = $rows;
                $itemList->json = $json;
                $itemList->save();
            }
        }

        return response()->json([
            'success' => true,
            'inventory_ids' => collect($inventoryItems)->pluck('inventory_id')->values(),
            'inventory_id' => optional($inventoryItems[0] ?? null)->inventory_id,
        ]);
    }

    public function copyStockEntryItem(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|integer|exists:inventory,inventory_id',
        ]);

        $original = Inventory::findOrFail($request->inventory_id);

        if ($original->source_type !== 'stock_entry' || empty($original->source_header_id)) {
            return response()->json(['message' => 'Csak ékszer vásárlásból származó tétel másolható.'], 422);
        }

        $stockEntry = StockEntry::findOrFail($original->source_header_id);
        if ((int) $stockEntry->status === 2) {
            return response()->json(['message' => 'A bizonylat lezárt, tétel nem másolható.'], 423);
        }

        DB::beginTransaction();
        try {
            $sorszam = $original->sorszam ?: Inventory::getNextSorszam($stockEntry->stock_entry_id);
            $alszam = Inventory::getNextAlszam($stockEntry->stock_entry_id, $sorszam);

            $newItem = new Inventory();
            $newItem->product_id = $original->product_id;
            $newItem->sorszam = $sorszam;
            $newItem->alszam = $alszam;
            $newItem->code = $sorszam . '/' . $alszam;
            $newItem->name = $original->name;
            $newItem->description = $original->description;
            $newItem->factory_number = $original->factory_number;
            $newItem->weight = $original->weight;
            $newItem->quantity = $original->quantity;
            $newItem->oneprice = $original->oneprice;
            $newItem->price = $original->price;
            $newItem->comment = $original->comment;
            $newItem->status = $original->status;
            $newItem->storage_id = $original->storage_id;
            $newItem->source_type = 'stock_entry';
            $newItem->source_header_id = $stockEntry->stock_entry_id;
            $newItem->warehouse_from = $original->warehouse_from;
            $newItem->warehouse_to = $original->warehouse_to;
            $newItem->warehouse_to = $original->warehouse_to;
            $newItem->termek_tipus_id = $original->termek_tipus_id;
            // SKU must be unique; generate it after insert when inventory_id is available.
            $newItem->sku = null;

            $itemData = $original->item_data;
            if (is_string($itemData)) {
                $itemData = json_decode($itemData, true) ?: [];
            }
            if (!is_array($itemData)) {
                $itemData = [];
            }
            $itemData['parent_inventory_id'] = $original->inventory_id;
            $newItem->item_data = $itemData;
            $newItem->save();

            $productCode = $newItem->product_id ? (optional(Product::find($newItem->product_id))->code ?? 'INV') : 'INV';
            $newItem->sku = $productCode . '-' . $newItem->inventory_id;
            $newItem->save();

            DB::commit();
            return response()->json(['success' => true, 'inventory_id' => $newItem->inventory_id]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('copyStockEntryItem error: ' . $e->getMessage());
            return response()->json(['message' => 'A másolás sikertelen.'], 500);
        }
    }

    public function deleteStockEntryItem(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|integer|exists:inventory,inventory_id',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);

        if ($inventory->source_type !== 'stock_entry' || empty($inventory->source_header_id)) {
            return response()->json(['message' => 'Csak ékszer vásárlásból származó tétel törölhető.'], 422);
        }

        $stockEntry = StockEntry::findOrFail($inventory->source_header_id);
        if ((int) $stockEntry->status === 2) {
            return response()->json(['message' => 'A bizonylat lezárt, tétel nem törölhető.'], 423);
        }

        DB::beginTransaction();
        try {
            InventoryPicture::where('inventory_id', $inventory->inventory_id)->delete();

            $imagePath = public_path('/document_storage/inventory_images/' . $inventory->inventory_id);
            if (File::isDirectory($imagePath)) {
                File::deleteDirectory($imagePath);
            }

            $inventory->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('deleteStockEntryItem error: ' . $e->getMessage());
            return response()->json(['message' => 'A törlés sikertelen.'], 500);
        }
    }

    /**
     * Inline szerkesztésből érkező inventory tétel mentése.
     */
    public function saveInventoryItemInline(Request $request)
    {
        try {
            $validated = $request->validate([
                'inventory_id' => 'required|integer|exists:inventory,inventory_id',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:1',
                'oneprice' => 'nullable|numeric|min:0',
                'price' => 'nullable|numeric|min:0',
                'factory_number' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'vzj' => 'nullable|string|max:255',
                'customer_marking_id' => 'nullable|integer|exists:customer_markings,id',
                'category_id' => 'nullable|integer',
                'beszerzesi_ar' => 'nullable|numeric',
                'eladasi_ar' => 'nullable|numeric',
                'vat_type_id' => 'nullable|integer',
                'hallmarks' => 'nullable|integer|in:0,1',
                'statuses' => 'nullable|array',
                'colors' => 'nullable|array',
                'category2_id' => 'nullable|integer',
                'karat_id' => 'nullable|integer',
                'size' => 'nullable|string|max:100',
                'label_type' => 'nullable|string|max:50',
                'termek_tipus_id' => 'nullable|integer',
            ]);
            
            $inventory = Inventory::findOrFail($validated['inventory_id']);

            $stockEntry = StockEntry::find($inventory->source_header_id);
            if ($stockEntry && (int) $stockEntry->status === 2) {
                return response()->json(['message' => 'A bizonylat lezárt, a tétel nem módosítható.'], 423);
            }

            // Alap inventory mezők frissítése
            $inventory->name = $validated['name'] ?? $inventory->name;
            $inventory->description = $validated['description'] ?? $inventory->description;
            $inventory->quantity = $validated['quantity'];
            $inventory->oneprice = $validated['oneprice'] ?? $inventory->oneprice;
            $inventory->price = $validated['price'] ?? $inventory->price;
            $inventory->factory_number = $validated['factory_number'] ?? $inventory->factory_number;
            $inventory->weight = $validated['weight'] ?? $inventory->weight;
            $inventory->termek_tipus_id = $validated['termek_tipus_id'] ?? $inventory->termek_tipus_id;
            $resolvedInventoryName = $inventory->termek_tipus_id
                ? (ProductType::find((int) $inventory->termek_tipus_id)?->name ?? null)
                : null;
            $inventory->name = $resolvedInventoryName;
            // JSON (item_data) összeállítása
            $itemData = $inventory->item_data ?? [];
            if (!is_array($itemData)) {
                $itemData = json_decode($itemData, true) ?: [];
            }

            // Frissítjük a JSON mezőket
            if (!empty($validated['customer_marking_id'])) {
                $selectedCustomerMarking = CustomerMarking::where('id', $validated['customer_marking_id'])
                    ->where('customer_id', optional($stockEntry)->customer_id)
                    ->first();
                if ($selectedCustomerMarking) {
                    $itemData['customer_marking_id'] = $selectedCustomerMarking->id;
                    $itemData['vzj'] = $selectedCustomerMarking->name;
                }
            } else {
                $itemData['customer_marking_id'] = null;
                $itemData['vzj'] = $validated['vzj'] ?? ($itemData['vzj'] ?? null);
            }
            $itemData['category_id'] = $validated['category_id'] ?? ($itemData['category_id'] ?? null);
            $itemData['beszerzesi_ar'] = $validated['beszerzesi_ar'] ?? ($itemData['beszerzesi_ar'] ?? null);
            $itemData['eladasi_ar'] = $validated['eladasi_ar'] ?? ($itemData['eladasi_ar'] ?? null);
            $itemData['vat_type_id'] = $validated['vat_type_id'] ?? ($itemData['vat_type_id'] ?? null);
            $itemData['hallmarks'] = $validated['hallmarks'] ??  0;
            $itemData['statuses'] = $validated['statuses'] ?? ($itemData['statuses'] ?? []);
            $itemData['colors'] = $validated['colors'] ?? ($itemData['colors'] ?? []);
            $itemData['category2_id'] = $validated['category2_id'] ?? ($itemData['category2_id'] ?? null);
            $itemData['size'] = $validated['size'] ?? ($itemData['size'] ?? null);
            $itemData['label_type'] = $validated['label_type'] ?? ($itemData['label_type'] ?? null);
            $itemData['karat_id'] = $validated['karat_id'] ?? ($itemData['karat_id'] ?? null);
            $itemData['termek_tipus_id'] = $inventory->termek_tipus_id;

            $inventory->item_data = $itemData;

            $inventory->save();

            // Opcionális: a kapcsolódó product törzsadatainak frissítése (ha a felhasználó a termék nevét is szerkeszti)
            // Itt most nem frissítjük, mert a terméknév valószínűleg közös több tételnél.
            // Ha mégis szükséges, a $inventory->product->update(...) használható.

            return response()->json([
                'success' => true,
                'message' => 'A tétel sikeresen frissítve.',
                'inventory' => $inventory
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a mentés közben: ' . $e->getMessage()
            ], 500);
        }
    }
    public function settings(StockEntry $stock_entry, Request $request)
    {
        $inventoryItems = $stock_entry->inventoryItems()->with('product')->get();
        if ($request->has('inventory_id')) {
            $inventoryItems = $inventoryItems->where('inventory_id', $request->inventory_id);
        } elseif ($request->has('product_id')) {
            $inventoryItems = $inventoryItems->where('product_id', $request->product_id);
        }
        $products = Product::orderBy('name', 'ASC')->get();
        $statuses = StockController::STATUSES;

        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();

        return view('stock_entries.settings', [
            'stock_entry' => $stock_entry,
            'inventoryItems' => $inventoryItems,
            'products' => $products,
            'statuses' => $statuses,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'vat_types' => $vat_types,
        ]);
    }

    public function saveInventorySettings(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,product_id',
            'status' => 'required|integer',
            'weight' => 'nullable|numeric|min:0',
            'oneprice' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'factory_number' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'comment' => 'nullable|string|max:1000',
            'inv_comment' => 'nullable|string|max:1000',
            'investigation' => 'nullable|boolean',
            'repair' => 'nullable|boolean',
            'hallmark' => 'nullable|boolean',
            'is_fake' => 'nullable|boolean',
            'vzj' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'beszerzesi_ar' => 'nullable|numeric|min:0',
            'eladasi_ar' => 'nullable|numeric|min:0',
            'vat_type' => 'nullable|integer|exists:master_vat_types,vat_type_id',
            'hallmarks' => 'nullable|array',
            'hallmarks.*' => 'integer|exists:master_hallmarks,hallmark_id',
            'karat_id' => 'nullable|integer',
            'colors' => 'nullable|array',
            'colors.*' => 'integer|exists:master_product_colors,product_color_id',
            'size' => 'nullable|string|max:255',
            'category2_id' => 'nullable|integer',
            'label_type' => 'nullable|integer',
            'statuses' => 'nullable|array',
            'statuses.*' => 'integer|exists:master_product_statuses,product_status_id',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $inventory->product_id = $product->product_id;
        $inventory->name = $validated['name'] ?? $product->name;
        $inventory->description = $validated['description'] ?? $inventory->description;
        $inventory->factory_number = $validated['factory_number'] ?? $inventory->factory_number;
        $inventory->weight = $validated['weight'] ?? $inventory->weight;
        $inventory->oneprice = $validated['oneprice'] ?? $inventory->oneprice;
        $inventory->price = $validated['price'] ?? $inventory->price;
        $inventory->comment = $validated['comment'] ?? $inventory->comment;
        $inventory->inv_comment = $validated['inv_comment'] ?? $inventory->inv_comment;
        $inventory->status = $validated['status'];
        $inventory->investigation = $request->has('investigation') ? 1 : 0;
        $inventory->repair = $request->has('repair') ? 1 : 0;
        $inventory->hallmark = $request->has('hallmark') ? 1 : 0;
        $inventory->is_fake = $request->has('is_fake') ? 1 : 0;
        $inventory->termek_tipus_id = $validated['termek_tipus_id'] ?? $inventory->termek_tipus_id;

        // item_data frissítése
        $itemData = $inventory->item_data ?? [];
        if (is_string($itemData)) $itemData = json_decode($itemData, true);
        if (!is_array($itemData)) $itemData = [];

        $itemData['vzj'] = $validated['vzj'] ?? null;
        $itemData['category_id'] = $validated['category_id'] ?? null;
        $itemData['beszerzesi_ar'] = $validated['beszerzesi_ar'] ?? null;
        $itemData['eladasi_ar'] = $validated['eladasi_ar'] ?? null;
        $itemData['vat_type_id'] = $validated['vat_type'] ?? null;
        $itemData['hallmarks'] = $validated['hallmarks'] ?? [];
        $itemData['karat_id'] = $validated['karat_id'] ?? null;
        $itemData['colors'] = $validated['colors'] ?? [];
        $itemData['size'] = $validated['size'] ?? null;
        $itemData['category2_id'] = $validated['category2_id'] ?? null;
        $itemData['label_type'] = $validated['label_type'] ?? null;
        $itemData['statuses'] = $validated['statuses'] ?? [];

        $inventory->item_data = $itemData;

        $inventory->save();

        return redirect()->route('stock_entry.settings', $inventory->source_header_id)
            ->withStatus('Inventory tétel sikeresen mentve.');
    }
    public function saveStockEntryItem_old(Request $request)
    {

        if($request->sei_id){
            $stock_entry_item = StockEntryItem::find($request->sei_id);
            $product = Product::find($stock_entry_item->product_id);
        } else {
            $stock_entry_item = new StockEntryItem();

            $product = new Product();
        }

        //termék törzsbe mentés

        $product->name = $request->sei_name;
        $product->short_description = $request->sei_description;
        $product->status = 3; //Feldolgozás alatt
        $product->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
        $product->weight = $request->sei_weight ? $request->sei_weight : 0;
        $product->save();

        // Fémjelzések hozzáadása
        $hallmarkIds = Hallmark::where('status', 1)?->pluck('hallmark_id');
        $product->hallmarks()->syncWithoutDetaching($hallmarkIds);

        // Színek hozzáadása
        $productColorIds = ProductColor::where('status', 1)?->pluck('product_color_id');
        $product->product_colors()->syncWithoutDetaching($productColorIds);

        // Státuszok hozzáadása
        $productStatusIds = ProductStatus::where('status', 1)?->pluck('product_status_id');
        $product->product_statuses()->syncWithoutDetaching($productStatusIds);

        // Jelek hozzáadása
        $productSignIds = ProductSign::where('status', 1)?->pluck('product_sign_id');
        $product->product_signs()->syncWithoutDetaching($productSignIds);

        $stock_entry_item->stock_entry_id = $request->stock_entry_id;
        $stock_entry_item->name = $request->sei_name;
        $stock_entry_item->description = $request->sei_description;
        $stock_entry_item->quantity = $request->sei_quantity;
        $stock_entry_item->weight = $request->sei_weight;
        $stock_entry_item->factory_number = $request->sei_factory_number;
        $stock_entry_item->oneprice = $request->sei_oneprice;
        $stock_entry_item->price = $request->sei_oneprice*$request->sei_quantity;
        $stock_entry_item->comment = $request->sei_comment;
        $stock_entry_item->product_id = $product->product_id;
        $stock_entry_item->warehouse_to = $product->storage_id;


        if(!$stock_entry_item->status){
            $stock_entry_item->status = 1;
        }

        $stock_entry_item->save();

        return $stock_entry_item->stock_entry_item_id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $stock_entry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StockEntryRequest $request, StockEntry $stock_entry)
    {
        $stock_entry->update(
            array_merge($request->all(),
                []
            )
        );

        return redirect()->route('stock_entry.index')->withStatus('Az ékszer bevételezés adatai sikeresen mentve.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $stock_entry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(StockEntry $stock_entry)
    {
        try {
            $stock_entry->delete();
            return redirect()->route('stock_entry.index')->withStatus('Az ékszer bevételezés sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('stock_entry.index')->withErrors('Nem lehet törölni az ékszer bevételezést, mert kapcsolódó adatok vannak.');
        }
    }


    public function printStockEntry(Request $request)
    {
        // Clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/stock_entries/*'));

        $stock_entry_id = $request->stock_entry_id;
        $file_name = 'stock_entry_' . $stock_entry_id . '_' . time() . '.pdf';
        $save_path = public_path() . '/pdf/stock_entries/' . $file_name;

        $pdf = \App::make('dompdf.wrapper');

        $stock_entry = StockEntry::with('inventoryItems')->find($stock_entry_id); // Eager loading
        if ($stock_entry->printed_at == null) {
            $stock_entry->printed_at = now();
            $stock_entry->save();
        }

        $data['stock_entry'] = $stock_entry;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $pdf->loadView('pdf.stock_entry', ['data' => $data]);
        $pdf->save($save_path);
        return $file_name;
    }

    public function update_fulfillment_date(Request $request)
    {
        $stock_entry = StockEntry::Find($request->stock_entry_id);

        if ((int) $stock_entry->status === 2) {
            return response()->json(['success' => false, 'message' => 'A bizonylat lezárt, nem módosítható.'], 423);
        }

        $stock_entry->fulfillment_date = $request->date;
        $stock_entry->save();
        return response()->json(['success' => true,'message' => 'OK'], 200);
    }

    public function saveAndUploadSEIImage(Request $request)
    {
        try {
            // Fájl feltöltése a fájlrendszerbe
            $file = $request->file('sei_image');
            $path = public_path('/document_storage/stock_entry_images/' . $request->sei_id . '/');

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $image_resize = Image::make($file->getRealPath());
            $image_resize->save($path . $file->getClientOriginalName());

            $relativeImagePath = $request->sei_id . '/' . $file->getClientOriginalName();

            // Kép mentése az adatbázisba
            $image = new ZImage();
            $image->resource_id = $request->sei_id;
            $image->image_path = $relativeImagePath;
            $image->created_at = date('Y-m-d H:i:s');
            $image->module = (isset($request->module) && $request->module) ? $request->module : 'sei';
            $image->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Kép sikeresen feltöltve!',
                'image_path' => $relativeImagePath
            ]);

        } catch (\Exception $e) {
            Log::error('Hiba a kép feltöltése közben: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Hiba a kép feltöltése közben!'
            ], 500);
        }
    }

    
    public function autoSaveSEIImage(Request $request)
    {
        $image = new ZImage();
        $image->resource_id = $request->sei_id;
        $image->image_path = $request->image;
        $image->created_at = date('Y-m-d H:i:s');
        $image->module = (isset($request->module) && $request->module) ? $request->module : 'sei';
        $image->save();
    }


    public function uploadSEIImage(Request $request)
    {
        $file = $request->file('sei_image');
        $path = public_path('/document_storage/stock_entry_images/' . $request->sei_id . '/');

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $image_resize = Image::make($file->getRealPath());
        $image_resize->save($path . $file->getClientOriginalName());
        return $request->sei_id . '/' . $file->getClientOriginalName();
    }


    public function loadSEIGallery(Request $request)
    {
        $html = '';

        $images_key = 'stock_entry_images' . $request->mod;

        $sei = StockEntryItem::find($request->sei_id);
        
            $html .= '<div class="delete_buttons">';

            foreach($sei->$images_key as $img)
            {
                $html .= '<div class="hidden_image_wrapper" data-img_id="' . $img->image_id . '">
                            <button data-mod="'.$request->mod.'" type="button" data-sei_id="' . $sei->stock_entry_item_id . '" data-img_id="'. $img->image_id . '" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed"><i data-img_id="'.$img->image_id.'" class="fa fa-times"></i></button>
                            </div>';
            }
        
            $html .= '</div>';
        

        $html .= '<div data-lightGallery="1" id="lightgallery_sei_images'.$request->mod.'_' . $request->sei_id . '" class="">';

        foreach($sei->$images_key as $img)
        {
                $html .= '<div data-sub-html="" data-img_id="' . $img->image_id . '" class="sei_img_wrapper" data-src="' . env('PUBLIC_URL') . '/document_storage/stock_entry_images/' . $img->image_path . '"><img data-img_id="' . $img->image_id . '" class="img-responsive sei_image_img" src="' . env('PUBLIC_URL') . '/document_storage/stock_entry_images/' . $img->image_path . '" alt=""><span class="image_description"></span></div>';
        }
        $html .= '</div>';
            
        return $html;
    }
    public function loadInventoryGallery(Request $request)
{
    $inventoryId = $request->inventory_id;
   
    $inventory = Inventory::find($inventoryId);

    $images = collect();

    // Inventory képek
    if ($inventory) {
        $inventoryImages = ZImage::where('resource_id', $inventoryId)
            ->where('module', 'inventory')
            ->get();
        $images = $images->merge($inventoryImages);
    }
    
    // HTML generálás (egyszerű példa)
    $html = '<div class="row">';
    foreach ($images as $img) {
        $url = $img->image_path;
        $html .= '<div class="col-md-3 mb-3">
                    <img src="' . $url . '" class="img-fluid img-thumbnail">
                    <button class="btn btn-sm btn-danger mt-1 delete-gallery-image" data-image-id="' . $img->image_id . '">Törlés</button>
                  </div>';
    }
    $html .= '</div>';
    return $html;
}
public function uploadInventoryImage(Request $request)
{
    try {
        $inventoryId = $request->inventory_id;
        
        $files = $request->file('images');

        if ($inventoryId) {
            $resourceId = $inventoryId;
            $module = 'inventory';
            $pathBase = 'inventory_images';
        } else {
            throw new \Exception('Nincs megadva inventory_id vagy product_id');
        }

        $dir = public_path("/document_storage/{$pathBase}/{$resourceId}/");
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0777, true, true);
        }

        foreach ($files as $file) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($dir, $filename);
            ZImage::create([
                'resource_id' => $resourceId,
                'image_path' => $resourceId . '/' . $filename,
                'created_at' => now(),
                'module' => $module,
            ]);
        }

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
    public function deleteImg(Request $request)
    {
        $image = ZImage::find($request->img_id);

        if(file_exists(public_path() . '/document_storage/stock_entry_images/' . $image->image_path))
        {
            unlink(public_path() . '/document_storage/stock_entry_images/' . $image->image_path);
        }

        $image->delete();
    }


    //get_stock_entry_images ajax
    public function get_stock_entry_images(Request $request)
    {
        $stock_entry_id = $request->stock_entry_id;
        $images = DB::table('images')
            ->where('module', 'stock_entry')
            ->where('resource_id', $stock_entry_id)
            ->get();

        return response()->json(['success' => 'success', 'images' => $images]);
    }

    public function copyStockEntry(Request $request)
    {
        DB::beginTransaction();

        $original = StockEntry::findOrFail($request->stock_entry_id);

        $serial = $this->getNextSerial();
        $copy = new StockEntry();
        $copy->ticket_number = 'EBEV/' . $original->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        $copy->customer_id = $original->customer_id;
        $copy->business_id = $original->business_id;
        $copy->storage_id = $original->storage_id;
        $copy->employee_id = $original->employee_id;
        $copy->currency_id = $original->currency_id;
        $copy->currency_value = $original->currency_value;
        $copy->fulfillment_date = $original->fulfillment_date;
        $copy->invoice_number = $original->invoice_number;
        $copy->has_contract = $original->has_contract;
        $copy->contract_number = $original->contract_number;
        $copy->save();

        foreach ($original->inventoryItems as $oldItem) {
            $newItem = new Inventory();
            $newItem->product_id = $oldItem->product_id;
            $newItem->name = $oldItem->name;
            $newItem->description = $oldItem->description;
            $newItem->factory_number = $oldItem->factory_number;
            $newItem->weight = $oldItem->weight;
            $newItem->quantity = $oldItem->quantity;
            $newItem->oneprice = $oldItem->oneprice;
            $newItem->price = $oldItem->price;
            $newItem->comment = $oldItem->comment;
            $newItem->status = InventoryStatus::PENDING->value;
            $newItem->storage_id = $copy->storage_id;
            $newItem->source_type = 'stock_entry';
            $newItem->source_header_id = $copy->stock_entry_id;
            $newItem->warehouse_to = $copy->storage_id;
            $newItem->save();

            // Képek másolása (ha vannak)
            $path = public_path('/document_storage/stock_entry_images/');
            foreach ($oldItem->stock_entry_images as $image) {
                $newImagePath = preg_replace('/\d+(?=\/)/', $newItem->inventory_id, $image->image_path, 1);
                if (!File::exists($path . $newItem->inventory_id . '/')) {
                    File::makeDirectory($path . $newItem->inventory_id . '/', 0777, true);
                }
                File::copy($path . $image->image_path, $path . $newImagePath);

                ZImage::create([
                    'resource_id' => $newItem->inventory_id,
                    'image_path' => $newImagePath,
                    'module' => 'stock_entry',
                    'created_at' => now(),
                ]);
            }
        }

        DB::commit();
        return $copy->stock_entry_id;
    }

    public function cancelStockEntry(Request $request)
    {
        try {
            DB::beginTransaction();

            $original = StockEntry::findOrFail($request->stock_entry_id);

            $serial = $this->getNextSerial();
            $storno = new StockEntry();
            $storno->ticket_number = 'EBEV/' . $original->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
            $storno->customer_id = $original->customer_id;
            $storno->business_id = $original->business_id;
            $storno->storage_id = $original->storage_id;
            $storno->employee_id = $original->employee_id;
            $storno->currency_id = $original->currency_id;
            $storno->currency_value = $original->currency_value;
            $storno->fulfillment_date = $original->fulfillment_date;
            $storno->invoice_number = $original->invoice_number;
            $storno->has_contract = $original->has_contract;
            $storno->contract_number = $original->contract_number;
            $storno->storno_reason = $request->storno_reason;
            $storno->is_storno = 1;
            $storno->status = 3; // Sztornó
            $storno->save();

            $original->cancelled_at = now();
            $original->storno_se_id = $storno->stock_entry_id;
            $original->status = 4; // Sztornózva
            $original->save();

            foreach ($original->inventoryItems as $item) {
                $stornoItem = new Inventory();
                $stornoItem->product_id = $item->product_id;
                $stornoItem->name = $item->name;
                $stornoItem->description = $item->description;
                $stornoItem->factory_number = $item->factory_number;
                $stornoItem->weight = $item->weight;
                $stornoItem->quantity = $item->quantity * (-1);
                $stornoItem->oneprice = $item->oneprice;
                $stornoItem->price = $item->price * (-1);
                $stornoItem->comment = 'Sztornó: ' . ($request->storno_reason ?? '');
                $stornoItem->status = InventoryStatus::SOLD_OUT->value;
                $stornoItem->storage_id = $original->storage_id;
                $stornoItem->source_type = 'stock_entry';
                $stornoItem->source_header_id = $storno->stock_entry_id;
                $stornoItem->warehouse_to = $original->storage_id;
                $stornoItem->save();
            }

            DB::commit();

            return response()->json([
                'data' => [
                    'success' => 'success',
                    'message' => 'Sztornó sikeres',
                    'stock_entry_id' => $storno->stock_entry_id
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'data' => [
                    'success' => 'error',
                    'message' => 'Hiba történt a sztornózás közben: ' . $e->getMessage()
                ]
            ], 200);
        }
    }
    public function saveSEIProduct(Request $request)
    {
       $inventory = Inventory::findOrFail($request->inventory_id);
        $storage_id = $request->storage_id;

        // A storage_id-t az inventory rekordhoz rendeljük
        $inventory->storage_id = $storage_id;

        $form_data = $request->form_data;
        $decoded_form_data = urldecode($form_data);
        parse_str($decoded_form_data, $data_array);

        $prod = Product::find($inventory->product_id);

        $prod->name = $data_array['name'];
        $prod->short_description = $data_array['short_description'];
        $prod->long_description = $data_array['long_description'];
        $prod->brand = $data_array['brand'];
        $prod->status = $data_array['status'];
        // TÖRÖLVE: $prod->storage_id = $storage_id;

        $prod->real_price = $data_array['real_price'];
        if (isset($data_array['price_type'])) {
            $unitprice = UnitPriceType::find($data_array['price_type']);
            if ($unitprice) {
                $prod->price_type = $unitprice->unit_price_type_id;
                $prod->real_price = $unitprice->price;
            }
        }
        if (isset($data_array['vat_type'])) {
            $vat = VatType::find($data_array['vat_type']);
            $prod->vat_type = $vat->vat_type_id;
        }
        if (isset($data_array['discount_allowed'])) {
            $prod->discount_allowed = 1;
        }

        if (isset($data_array['category_id']) && $data_array['category_id']) {
            $prod->category_id = $data_array['category_id'];
        } else {
            $prod->category_id = null;
        }

        // Több-értékű kapcsolatok szinkronizálása
        if (isset($data_array['hallmarks'])) {
            $prod->hallmarks()->sync($data_array['hallmarks']);
        }
        if (isset($data_array['product_colors'])) {
            $prod->product_colors()->sync($data_array['product_colors']);
        }
        if (isset($data_array['product_statuses'])) {
            $prod->product_statuses()->sync($data_array['product_statuses']);
        }
        if (isset($data_array['product_signs'])) {
            $prod->product_signs()->sync($data_array['product_signs']);
        }

        // Extra attribútumok frissítése
        foreach ($data_array as $attr => $param) {
            if (substr($attr, 0, 9) == 'hallmark_') {
                DB::table('hallmark_product')
                    ->where('hallmark_hallmark_id', substr($attr, 9))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['value' => $param]);
            } elseif (substr($attr, 0, 14) == 'product_color_') {
                DB::table('product_product_color')
                    ->where('product_color_product_color_id', substr($attr, 14))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['value' => $param]);
            } elseif (substr($attr, 0, 15) == 'product_status_') {
                DB::table('product_product_status')
                    ->where('product_status_product_status_id', substr($attr, 15))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['comment' => $param]);
            } elseif (substr($attr, 0, 13) == 'product_sign_') {
                DB::table('product_product_sign')
                    ->where('product_sign_product_sign_id', substr($attr, 13))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['comment' => $param]);
            }
        }

        $prod->save();

        // Vizsgálat rögzítése az inventory rekordon
        $inventory->investigation = 1;
        $inventory->investigated_at = now();
        $inventory->investigation_user_id = Auth::user()->id;
        $inventory->save();
    }


    public function editSEI(Request $request)
    {
        $sei = StockEntryItem::find($request->sei_id);

        $tags = Tag::orderBy('name', 'ASC')->get();

        return view('stock_entries.edit_sei', [
            'sei' => $sei,
            'tags' => $tags
        ]);
    }

}
