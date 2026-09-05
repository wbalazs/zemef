<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\ConsignmentReceipt;
use App\Models\ConsignmentReceiptItem;
use App\Models\ZImage;
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
use App\Enums\ConsignmentReceiptStatus;
use App\Enums\InventoryStatus;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Category;
use App\Tag;
use App\User;
use App\Http\Controllers\CategoryController;
use Carbon\Carbon;
use App\Http\Requests\ConsignmentReceiptRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use PDF;
use File;


class ConsignmentReceiptController extends Controller
{

    public function __construct()
    {
        ////$this->authorizeResource(ConsignmentReceipt::class);
        $this->categoryController = new CategoryController();
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(ConsignmentReceipt $model)
    {
        ConsignmentReceipt::where('ticket_number', '--empty_row--')->delete();
        return view('consignment_receipts.index', 
        [
            'consignment_receipts' => $model->orderBy('created_at', 'DESC')->get()
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

        $consignment_receipt = ConsignmentReceipt::create(['ticket_number' => '--empty_row--', 'status' => 1]);
        $consignment_receipt->ticket_number = '';

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
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $products = Product::orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('consignment_receipts.edit', [
            'consignment_receipt' => $consignment_receipt,
            'customers' => $customers,
            'businesses' => $businesses,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_signs' => $product_signs,
            'product_types' => $product_types,
            'products' => $products,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'karats' => $karats,
            'category2s' => $category2s,
            'consignment_receipt_statuses' => ConsignmentReceiptStatus::options(),
            'inventoryItems' => collect(),
            'currencies' => Currency::where('status', 1)->orderBy('currency_id', 'ASC')->get()
        ]);
    }
    
    public function import()
    {
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('consignment_receipts.import', ['customers' => $customers]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\ConsignmentReceiptRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ConsignmentReceiptRequest $request, ConsignmentReceipt $model)
    {
        $consignment_receipt = $model->create($request->merge([
        ])->all());

        return redirect()->route('consignment_receipt.index')->withStatus('A bizományosi átvétel sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $consignment_receipt
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(ConsignmentReceipt $consignment_receipt)
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

        $storageId = $consignment_receipt->storage_id;
        $warehouse = Warehouse::find($storageId);

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();
        $products = Product::orderBy('name', 'ASC')->get();

        $inventoryItems = Inventory::where('source_type', 'consignment')
            ->where('source_header_id', $consignment_receipt->consignment_receipt_id)
            ->get()
            ->map(function ($item) use ($consignment_receipt) {
                $itemData = $item->getAttribute('item_data');
                if (is_string($itemData)) {
                    $itemData = json_decode($itemData, true);
                }
                if (!is_array($itemData)) {
                    $itemData = [];
                }

                // Header fallback, hogy a régi tételek is kompatibilisek maradjanak.
                $headerData = is_array($consignment_receipt->header_data) ? $consignment_receipt->header_data : [];
                $fallbackKeys = ['termek_tipus_id', 'category_id', 'beszerzesi_ar', 'eladasi_ar', 'vat_type_id', 'karat_id', 'label_type', 'category2_id', 'size'];
                foreach ($fallbackKeys as $key) {
                    if (!array_key_exists($key, $itemData) || $itemData[$key] === null || $itemData[$key] === '') {
                        if (array_key_exists($key, $headerData)) {
                            $itemData[$key] = $headerData[$key];
                        }
                    }
                }

                if (!array_key_exists('hallmarks', $itemData)) {
                    $itemData['hallmarks'] = $headerData['hallmarks'] ?? 0;
                }
                if (!array_key_exists('statuses', $itemData)) {
                    $itemData['statuses'] = isset($headerData['product_status_id']) && $headerData['product_status_id']
                        ? [(int) $headerData['product_status_id']]
                        : [];
                }
                if (!array_key_exists('colors', $itemData)) {
                    $itemData['colors'] = [];
                }

                foreach (['statuses', 'colors'] as $field) {
                    if (isset($itemData[$field]) && !is_array($itemData[$field])) {
                        $itemData[$field] = $itemData[$field] !== '' && $itemData[$field] !== null
                            ? [(int) $itemData[$field]]
                            : [];
                    }
                }

                $item->setAttribute('item_data', $itemData);
                return $item;
            });


        return view('consignment_receipts.edit', [
            'consignment_receipt' => $consignment_receipt,
            'customers' => $customers,
            'businesses' => $businesses,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_signs' => $product_signs,
            'product_types' => $product_types,
            'warehouse' => $warehouse,
            'currencies' => Currency::where('status', 1)->orderBy('currency_id', 'ASC')->get(),
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'karats' => $karats,
            'category2s' => $category2s,
            'consignment_receipt_statuses' => ConsignmentReceiptStatus::options(),
            'products' => $products,
            'inventoryItems' => $inventoryItems,
            'show' => false
        ]);
    }


    private function getNextSerial()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'consignment_receipt')
            ->where('year', date('Y'))
            ->first();

        $serial = $record ? $record->serial_number : null;

        if (!$serial) {
            DB::table('serial_numbers')
                ->insert(['module' => 'consignment_receipt', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }

        $next = ($serial+1);

        DB::table('serial_numbers')
            ->where('module', 'consignment_receipt')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);

        return $serial;
    }

    public function get_storages_by_business_id_employee(Request $request)
    {
        $business_id = $request->business_id;
        
        $employee = Auth::user()->employee;
        
        // Lekérjük azokat a warehouse ID-kat, amelyek kapcsolódnak a megadott business ID-hoz
        $warehouseIds = DB::table('business_warehouse')
            ->where('business_business_id', $business_id)
            ->pluck('warehouse_warehouse_id');

        // Lekérjük azokat a warehouse elemeket, amelyek ID-je szerepel a fent lekért ID-k között,
        // és az aktuális employee is hozzá van rendelve.
        $employeeWarehouses = Warehouse::whereIn('warehouse_id', $warehouseIds)
            ->where('status', 1)  // Itt szűrjük a státuszra
            //->where('is_consignment', 1) // Itt szűrjük a bizományosira
            ->whereExists(function ($query) use ($employee) {
                $query->select(DB::raw(1))
                    ->from('employee_warehouse')
                    ->whereRaw('employee_warehouse.warehouse_warehouse_id = warehouses.warehouse_id')
                    ->where('employee_employee_id', $employee->employee_id);
            })
            ->select('warehouse_id', 'name')
            ->get();

        return response()->json($employeeWarehouses);
    }


    public function saveConsignmentReceipt(Request $request)
    {
        $request->validate([
            'consignment_status' => ['required', 'integer', 'in:' . implode(',', array_keys(ConsignmentReceiptStatus::options()))],
        ]);

        $consignment_receipt = ConsignmentReceipt::find($request->consignment_receipt_id);
        if (!$consignment_receipt) {
            return response()->json(['success' => false, 'message' => 'A bizonylat nem található.'], 404);
        }

        $storage = Warehouse::find($request->storage_id);
        $storage_id = $storage ? $storage->warehouse_id : null;
        $businessId = $request->business_id;
        if (empty($businessId) && $storage && $storage->businesses->first()) {
            $businessId = $storage->businesses->first()->business_id;
        }

        if(!$consignment_receipt->ticket_number || $consignment_receipt->ticket_number == '--empty_row--'){

            $serial = $this->getNextSerial();
            // Formátum: XX/EEEE/00001 - ahol XX - bolti azonosító
            $consignment_receipt->ticket_number = 'BATV/'. $businessId . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        }

        $customer_id = $request->customer_id;
        $customerIsCompany = filter_var($request->input('customer_is_company', 0), FILTER_VALIDATE_BOOLEAN);

        $taxIdentifier = $request->input('tax_identifier');
        if ($taxIdentifier === null || $taxIdentifier === '') {
            $taxIdentifier = $request->input('tax_number', $request->input('identity_card_number'));
        }
        $taxIdentity = !$customerIsCompany ? $taxIdentifier : null;
        $taxNumber = $customerIsCompany ? $taxIdentifier : null;

        if(!is_numeric($customer_id)){
            $zip = Zip::where('id', $request->zip)->first();
            $city = City::where('id', $request->city)->first();

            $customer = new Customer();
            $customer->name = $customer_id;
            $customer->is_company = $customerIsCompany ? 1 : 0;
            $customer->tax_identity = $taxIdentity;
            $customer->tax_number = $taxNumber;
            $customer->identity_card_number = null;
            $customer->status = 1;
            $customer->save();

            $zipName = $zip ? $zip->name : null;
            $zipDbId = $zip ? $zip->id : null;
            $cityName = $city ? $city->name : null;
            $cityDbId = $city ? $city->id : null;

            $customer_address = new CustomerAddress();
            $customer_address->address_type = "Lakcím";
            $customer_address->zip = $zipName;
            $customer_address->zip_id = $zipDbId;
            $customer_address->city = $cityName;
            $customer_address->city_id = $cityDbId;
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

                if ($zip || $city || $request->address) {
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
            }

            $customer->is_company = $customerIsCompany ? 1 : 0;
            $customer->tax_identity = !$customerIsCompany ? $taxIdentifier : null;
            $customer->tax_number = $customerIsCompany ? $taxIdentifier : null;
            $customer->identity_card_number = null;
            $customer->save();
        }

        $headerTermekTipusId = $request->input(
            'termek_tipus_id',
            $request->input('new_termek_tipus_id', $consignment_receipt->header_data['termek_tipus_id'] ?? null)
        );

        $headerData = [
            'customer_is_company' => $customerIsCompany ? 1 : 0,
            'tax_identifier_label' => $customerIsCompany ? 'Adószám' : 'Adóazonosító',
            'tax_identifier' => $taxIdentifier,
            'filenumber' => $request->input('filenumber'),
            'consignment_status' => (int) $request->input('consignment_status'),
            'termek_tipus_id' => $headerTermekTipusId,
            'category_id' => $request->category_id,
            'beszerzesi_ar' => $request->beszerzesi_ar,
            'eladasi_ar' => $request->eladasi_ar,
            'vat_type_id' => $request->vat_type,
            'hallmarks' => filter_var($request->input('hallmarks', 0), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'karat_id' => $request->karat_id,
            'label_type' => $request->label_type_default,
            'category2_id' => $request->category2_id,
            'size' => $request->size,
        ];

        $consignment_receipt->update(
            [
                'employee_id' => Auth::user()->employee?->employee_id,
                'customer_id' => $customer_id,
                'currency_id' => $request->currency_id,
                'business_id' => $businessId,
                'currency_value' => $request->currency_value,
                'is_hallmark_requested' => $request->is_hallmark_requested,
                'expire_date' => $request->expire_date,
                'storage_id' => $storage_id,
            ]
        );

        $consignment_receipt->header_data = $headerData;
        $consignment_receipt->save();

        

        return response()->json([
            'success' => true,
            'consignment_receipt_id' => $consignment_receipt->consignment_receipt_id,
        ]);
    }

    public function saveConsignmentReceiptItem(Request $request)
    {
        $request->validate([
            'new_consignment_status' => ['required', 'integer', 'in:' . implode(',', array_keys(ConsignmentReceiptStatus::options()))],
        ]);

        $consignment_receipt = ConsignmentReceipt::findOrFail($request->consignment_receipt_id);

        $rawTermekTipusId = $request->input('new_termek_tipus_id', $request->input('termek_tipus_id'));
        if ($rawTermekTipusId === null || $rawTermekTipusId === '') {
            $rawTermekTipusId = $consignment_receipt->header_data['termek_tipus_id'] ?? null;
        }
        $termekTipusId = ($rawTermekTipusId === null || $rawTermekTipusId === '') ? null : (int) $rawTermekTipusId;
        $inventoryName = $termekTipusId ? (ProductType::find($termekTipusId)?->name ?? null) : null;

        $selectedColors = $request->input('new_colors', $request->input('colors', []));
        if (!is_array($selectedColors)) {
            $selectedColors = $selectedColors !== null && $selectedColors !== '' ? [(int) $selectedColors] : [];
        }

        $itemData = [
            'termek_tipus_id' => $termekTipusId,
            'category_id' => $request->input('new_category_id', $request->input('category_id')),
            'beszerzesi_ar' => $request->input('new_beszerzesi_ar', $request->cri_oneprice),
            'eladasi_ar' => $request->input('new_eladasi_ar', $request->cri_price),
            'vat_type_id' => $request->input('new_vat_type', $request->input('vat_type_id')),
            'hallmarks' => filter_var($request->input('new_hallmarks', $request->input('hallmarks', 0)), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'sell_elsewhere' => filter_var($request->input('new_sell_elsewhere', 0), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'webshop' => filter_var($request->input('new_webshop', 0), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'consignment_status' => (int) $request->input('new_consignment_status'),
            'karat_id' => $request->input('new_karat_id', $request->input('karat_id')),
            'colors' => $selectedColors,
            'size' => $request->input('new_size', $request->input('size')),
            'label_type' => $request->input('new_label_type', $request->input('label_type', $consignment_receipt->header_data['label_type'] ?? null)),
            'category2_id' => $request->input('new_category2_id', $request->input('category2_id')),
        ];

        // Meglévő tétel módosítása
        $editInventoryId = $request->input('inventory_id', $request->input('cri_id'));
        if ($editInventoryId && is_numeric($editInventoryId)) {
            $inventory = Inventory::where('source_type', 'consignment')
                                ->where('source_header_id', $request->consignment_receipt_id)
                                ->where('inventory_id', $editInventoryId)
                                ->firstOrFail();

            $inventory->description = $request->cri_description;
            $inventory->quantity = $request->cri_quantity;
            $inventory->weight = $request->cri_weight;
            $inventory->factory_number = $request->cri_factory_number;
            $inventory->oneprice = $request->cri_oneprice;
            $inventory->price = $request->cri_oneprice * $request->cri_quantity;
            $inventory->termek_tipus_id = $termekTipusId;
            $inventory->item_data = array_merge(is_array($inventory->item_data) ? $inventory->item_data : [], $itemData);
            $inventory->save();

            // A kapcsolódó terméktípus frissítése
            $product = $inventory->product;
            if ($product) {
                $product->short_description = $request->cri_description;
                $product->weight = $request->cri_weight ?: 0;
                $product->real_price = $request->cri_oneprice;
                if (!empty($inventoryName)) {
                    $product->name = $inventoryName;
                }
                $product->save();
            }

            $inventory_id = $inventory->inventory_id;
        } 
        else {
            // Új tétel(ek) létrehozása
            $is_unique = filter_var($request->cri_is_unique, FILTER_VALIDATE_BOOLEAN);
            $quantity = (int) $request->cri_quantity;
            $oneprice = (int) $request->cri_oneprice;
            $weight = $request->cri_weight ?: 0;

            // Megnevezés opcionális: ha nincs, stock_entry mintára fix product_id-t használunk.
            $defaultProductId = 1;
            $productId = $defaultProductId;
            $productName = $inventoryName ?: ($request->cri_name ?: 'Tétel');

            if (is_numeric($request->cri_name)) {
                $product = Product::find($request->cri_name);
                if ($product) {
                    $productId = $product->product_id;
                    $productName = $product->name ?: $productName;
                }
            } elseif (!empty($request->cri_name)) {
                $product = new Product();
                $product->name = $productName;
                $product->short_description = $request->cri_description;
                $product->status = 3; // Feldolgozás alatt
                $product->weight = 0;
                $product->real_price = $oneprice;
                $product->save();

                // Alapértelmezett fémjelzések, színek, státuszok, jelek hozzáadása
                $hallmarkIds = Hallmark::where('status', 1)?->pluck('hallmark_id');
                $product->hallmarks()->syncWithoutDetaching($hallmarkIds);

                $productColorIds = ProductColor::where('status', 1)?->pluck('product_color_id');
                $product->product_colors()->syncWithoutDetaching($productColorIds);

                $productStatusIds = ProductStatus::where('status', 1)?->pluck('product_status_id');
                $product->product_statuses()->syncWithoutDetaching($productStatusIds);

                $productSignIds = ProductSign::where('status', 1)?->pluck('product_sign_id');
                $product->product_signs()->syncWithoutDetaching($productSignIds);

                $productId = $product->product_id;
            }

            // Inventory tételek létrehozása
            $inventoryItems = [];
            if ($is_unique) {
                // Egyedi tételek: minden darabhoz külön inventory rekord
                for ($i = 0; $i < $quantity; $i++) {
                    $inventory = new Inventory();
                    $inventory->product_id = $productId;
                    $inventory->code = $inventory->generateInventoryCode($termekTipusId ?: $productId); // új
                    $inventory->name = $productName;
                    $inventory->description = $request->cri_description;
                    $inventory->factory_number = $request->cri_factory_number;
                    $inventory->weight = $weight;
                    $inventory->quantity = 1;
                    $inventory->oneprice = $oneprice;
                    $inventory->price = $oneprice;
                    $inventory->comment = $request->cri_comment;
                    $inventory->status = 2; // függőben (még nem fizikailag átvéve)
                    $inventory->storage_id = $request->storage_id;
                    $inventory->source_type = 'consignment';
                    $inventory->source_header_id = $consignment_receipt->consignment_receipt_id;
                    $inventory->warehouse_to = $request->storage_id;
                    $inventory->termek_tipus_id = $termekTipusId;
                    $inventory->item_data = $itemData;
                    $inventory->save();

                    $inventoryItems[] = $inventory;
                }
            } else {
                // Nem egyedi: egy inventory rekord a teljes mennyiséggel
                $inventory = new Inventory();
                $inventory->product_id = $productId;
                $inventory->code = $inventory->generateInventoryCode($termekTipusId ?: $productId); // új
                $inventory->name = $productName;
                $inventory->description = $request->cri_description;
                $inventory->factory_number = $request->cri_factory_number;
                $inventory->weight = $weight;
                $inventory->quantity = $quantity;
                $inventory->oneprice = $oneprice;
                $inventory->price = $oneprice * $quantity;
                $inventory->comment = $request->cri_comment;
                $inventory->status = 2; // függőben
                $inventory->storage_id = $request->storage_id;
                $inventory->source_type = 'consignment';
                $inventory->source_header_id = $consignment_receipt->consignment_receipt_id;
                $inventory->warehouse_to = $request->storage_id;
                $inventory->termek_tipus_id = $termekTipusId;
                $inventory->item_data = $itemData;
                $inventory->save();

                $inventoryItems[] = $inventory;
            }

            $inventory_id = $inventoryItems[0]->inventory_id;
        }

        // Nyomtatás kezelése
        if ($request->print) {
            $consignment_receipt->printed_at = now();
            $consignment_receipt->save();
            session()->flash('print', $consignment_receipt->consignment_receipt_id);
        }

        return $inventory_id;
    }
    
    public function saveConsignmentReceiptItem_old(Request $request)
    {

        if($request->cri_id){
            $consignment_receipt_item = ConsignmentReceiptItem::find($request->cri_id);
            $product = Product::find($consignment_receipt_item->product_id);
        } else {
            $consignment_receipt_item = new ConsignmentReceiptItem();

            $product = new Product();
        }

        //termék törzsbe mentés

        $product->name = $request->cri_name;
        $product->short_description = $request->cri_description;
        $product->status = 3; //Feldolgozás alatt
        $product->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
        $product->weight = $request->cri_weight ? $request->cri_weight : 0;
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

        $consignment_receipt_item->consignment_receipt_id = $request->consignment_receipt_id;
        $consignment_receipt_item->name = $request->cri_name;
        $consignment_receipt_item->description = $request->cri_description;
        $consignment_receipt_item->quantity = $request->cri_quantity;
        $consignment_receipt_item->weight = $request->cri_weight;
        $consignment_receipt_item->factory_number = $request->cri_factory_number;
        $consignment_receipt_item->oneprice = $request->cri_oneprice;
        $consignment_receipt_item->price = $request->cri_oneprice*$request->cri_quantity;
        $consignment_receipt_item->comment = $request->cri_comment;
        $consignment_receipt_item->product_id = $product->product_id;
        $consignment_receipt_item->warehouse_to = $product->storage_id;


        if(!$consignment_receipt_item->status){
            $consignment_receipt_item->status = 1;
        }

        $consignment_receipt_item->save();

        return $consignment_receipt_item->consignment_receipt_item_id;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $consignment_receipt
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ConsignmentReceiptRequest $request, ConsignmentReceipt $consignment_receipt)
    {
        $consignment_receipt->update(
            array_merge($request->all(),
                []
            )
        );

        return redirect()->route('consignment_receipt.index')->withStatus('A bizományosi átvétel adatai sikeresen mentve.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $consignment_receipt
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ConsignmentReceipt $consignment_receipt)
    {
        try {
            $consignment_receipt->delete();
            return redirect()->route('consignment_receipt.index')->withStatus('A bizományosi átvétel sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('consignment_receipt.index')->withErrors('Nem lehet törölni a bizományosi átvételt, mert kapcsolódó adatok vannak.');
        }
    }


    public function printConsignmentReceipt(Request $request)
    {
        //clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/consignment_receipts/*'));

        $file_name = 'bizomanyosi_atvetel.pdf';

        $pdf = \App::make('dompdf.wrapper');

        $data = array();

        $consignment_receipt = ConsignmentReceipt::find($request->consignment_receipt_id);

        $consignment_receipt->printed_at = date('Y-m-d H:i:s');
        $consignment_receipt->save();

        $data['consignment_receipt'] = $consignment_receipt;
        $data['business'] = $consignment_receipt->business;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.consignment_receipt', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save(public_path() . '/pdf/consignment_receipts/' . $file_name);

        return $file_name;
    }

    public function update_expire_date(Request $request)
    {
        $consignment_receipt = ConsignmentReceipt::Find($request->consignment_receipt_id);
        $consignment_receipt->expire_date = $request->date;
        $consignment_receipt->save();
        return response()->json(['success' => true,'message' => 'OK'], 200);
    }

    public function saveAndUploadcriImage(Request $request)
    {
        try {
            // Fájl feltöltése a fájlrendszerbe
            $file = $request->file('cri_image');
            $path = public_path('/document_storage/consignment_receipt_images/' . $request->inventory_id . '/');

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $image_resize = Image::make($file->getRealPath());
            $image_resize->save($path . $file->getClientOriginalName());

            $relativeImagePath = $request->inventory_id . '/' . $file->getClientOriginalName();

            // Kép mentése az adatbázisba
            $image = new ZImage();
            $image->resource_id = $request->inventory_id;
            $image->image_path = $relativeImagePath;
            $image->created_at = date('Y-m-d H:i:s');
            $image->module = (isset($request->module) && $request->module) ? $request->module : 'cri';
            $image->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Kép sikeresen feltöltve!',
                'image_path' => $relativeImagePath
            ]);

        } catch (\Exception $e) {
            \Log::error('Hiba a kép feltöltése közben: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Hiba a kép feltöltése közben!'
            ], 500);
        }
    }

    
    public function autoSavecriImage(Request $request)
    {
        $image = new ZImage();
        $image->resource_id = $request->inventory_id;
        $image->image_path = $request->image;
        $image->created_at = date('Y-m-d H:i:s');
        $image->module = (isset($request->module) && $request->module) ? $request->module : 'cri';
        $image->save();
    }


    public function uploadcriImage(Request $request)
    {
        $file = $request->file('cri_image');
        $path = public_path('/document_storage/consignment_receipt_images/' . $request->inventory_id . '/');

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $image_resize = Image::make($file->getRealPath());
        $image_resize->save($path . $file->getClientOriginalName());
        return $request->inventory_id . '/' . $file->getClientOriginalName();
    }


    public function loadcriGallery(Request $request)
    {
        $html = '';

        $images_key = 'consignment_receipt_images' . $request->mod;

        $cri = Inventory::find($request->inventory_id);
        if ($cri->$images_key) {
            $html .= '<div class="delete_buttons">';

            foreach($cri->$images_key as $img)
            {
                $html .= '<div class="hidden_image_wrapper" data-img_id="' . $img->image_id . '">
                            <button data-mod="'.$request->mod.'" type="button" data-cri_id="' . $cri->consignment_receipt_item_id . '" data-img_id="'. $img->image_id . '" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed"><i data-img_id="'.$img->image_id.'" class="fa fa-times"></i></button>
                            </div>';
            }
        
            $html .= '</div>';

            $html .= '<div data-lightGallery="1" id="lightgallery_cri_images'.$request->mod.'_' . $request->inventory_id . '" class="">';

            foreach($cri->$images_key as $img)
            {
                    $html .= '<div data-sub-html="" data-img_id="' . $img->image_id . '" class="cri_img_wrapper" data-src="' . env('PUBLIC_URL') . '/document_storage/consignment_receipt_images/' . $img->image_path . '"><img data-img_id="' . $img->image_id . '" class="img-responsive cri_image_img" src="' . env('PUBLIC_URL') . '/document_storage/consignment_receipt_images/' . $img->image_path . '" alt=""><span class="image_description"></span></div>';
            }
            $html .= '</div>';
        } else {
            $html .= '<div class="alert alert-info">Nincsenek feltöltött képek.</div>';
        }

        return $html;
    }


    public function deleteImg(Request $request)
    {
        $image = ZImage::find($request->img_id);

        if(file_exists(public_path() . '/document_storage/consignment_receipt_images/' . $image->image_path))
        {
            unlink(public_path() . '/document_storage/consignment_receipt_images/' . $image->image_path);
        }

        $image->delete();
    }


    //get_consignment_receipt_images ajax
    public function get_consignment_receipt_images(Request $request)
    {
        $consignment_receipt_id = $request->consignment_receipt_id;
        $images = DB::table('images')
            ->where('module', 'consignment_receipt')
            ->where('resource_id', $consignment_receipt_id)
            ->get();

        return response()->json(['success' => 'success', 'images' => $images]);
    }
    
    public function copyConsignmentReceipt(Request $request)
    {
        DB::beginTransaction();

        $original = ConsignmentReceipt::findOrFail($request->consignment_receipt_id);

        // Új bizományosi átvétel létrehozása
        $serial = $this->getNextSerial();
        $copy = new ConsignmentReceipt();
        $copy->ticket_number = 'BATV/' . $original->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        $copy->customer_id = $original->customer_id;
        $copy->business_id = $original->business_id;
        $copy->storage_id = $original->storage_id;
        $copy->employee_id = $original->employee_id;
        $copy->currency_id = $original->currency_id;
        $copy->currency_value = $original->currency_value;
        $copy->header_data = $original->header_data;
        $copy->save();

        // Eredeti inventory tételek másolása
        foreach ($original->inventoryItems as $oldItem) {
            // Új inventory tétel létrehozása (ugyanaz a product_id, de új source_header_id)
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
            $newItem->status = 2; // függőben
            $newItem->storage_id = $copy->storage_id;
            $newItem->source_type = 'consignment';
            $newItem->source_header_id = $copy->consignment_receipt_id;
            $newItem->warehouse_to = $copy->storage_id;
            $newItem->termek_tipus_id = $oldItem->termek_tipus_id;
            $newItem->item_data = $oldItem->item_data;
            $newItem->save();

            // Képek másolása (feltételezzük, hogy a ZImage táblában van module='consignment_receipt' és resource_id = inventory_id)
            $path = public_path('/document_storage/consignment_receipt_images/');
            foreach ($oldItem->consignment_receipt_images as $image) {
                // Új fájlnév: a régi elérési útban az inventory_id-t cseréljük az újra
                $newImagePath = preg_replace('/\d+(?=\/)/', $newItem->inventory_id, $image->image_path, 1);

                // Fájl másolása
                if (!File::exists($path . $newItem->inventory_id . '/')) {
                    File::makeDirectory($path . $newItem->inventory_id . '/', 0777, true);
                }
                File::copy($path . $image->image_path, $path . $newImagePath);

                // Új kép rekord létrehozása
                ZImage::create([
                    'resource_id' => $newItem->inventory_id,
                    'image_path' => $newImagePath,
                    'module' => 'consignment_receipt', // vagy 'inventory' – a rendszertől függ
                    'created_at' => now(),
                ]);
            }
        }

        DB::commit();
        return $copy->consignment_receipt_id;
    }

    public function cancelConsignmentReceipt(Request $request)
    {
        try {
            DB::beginTransaction();

            $consignment_receipt = ConsignmentReceipt::find($request->consignment_receipt_id);
            $serial = $this->getNextSerial();

            $storno_cr = new ConsignmentReceipt();
            $storno_cr->ticket_number = 'BATV/'.$consignment_receipt->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
            $storno_cr->customer_id = $consignment_receipt->customer_id;
            $storno_cr->business_id = $consignment_receipt->business_id;
            $storno_cr->storage_id = $consignment_receipt->storage_id;
            $storno_cr->employee_id = $consignment_receipt->employee_id;
            $storno_cr->currency_id = $consignment_receipt->currency_id;
            $storno_cr->currency_value = $consignment_receipt->currency_value;
            $storno_cr->storno_reason = $request->storno_reason;
            $storno_cr->is_storno = 1;
            //Sztornó
            $storno_cr->status = 3;
            $storno_cr->save();

            $consignment_receipt->cancelled_at = date('Y-m-d H:i:s');
            $consignment_receipt->storno_cr_id = $storno_cr->consignment_receipt_id;
            //Sztornózva
            $consignment_receipt->status = 4;
            $consignment_receipt->save();

            foreach ($consignment_receipt->consignment_receipt_items as $item) {

                $cancel_cri = new ConsignmentReceiptItem();
                $cancel_cri->consignment_receipt_id = $storno_cr->consignment_receipt_id;
                $cancel_cri->name = $item->name;
                $cancel_cri->description = $item->description;
                $cancel_cri->quantity = $item->quantity*(-1);
                $cancel_cri->weight = $item->weight;
                $cancel_cri->factory_number = $item->factory_number;
                $cancel_cri->oneprice = $item->oneprice;
                $cancel_cri->price = $item->price;
                $cancel_cri->product_id = $product->product_id;
                $cancel_cri->status = 1;
                $cancel_cri->warehouse_to = $consignment_receipt->storage_id;
                $cancel_cri->save();
            }

            DB::commit();

            return response()->json(['data' => ['success' => 'success','message' => 'OK','consignment_receipt_id' =>  $storno_se->consignment_receipt_id]], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' => ['success' => 'error','message' => 'Hiba történt a sztornózás közben: ' . $e->getMessage()]], 200);
        }
    }

    public function savecriProduct(Request $request)
    {
        $cri = Inventory::find($request->inventory_id);
        $storage_id = $request->storage_id;
        $cri->storage_id = $storage_id;

        $form_data = $request->form_data;
        $decoded_form_data = urldecode($form_data);
        parse_str($decoded_form_data, $data_array);

        $prod = Product::find($cri->product_id);

        $prod->name = $data_array['name'];
        $prod->short_description = $data_array['short_description'];
        $prod->long_description = $data_array['long_description'];
        $prod->brand = $data_array['brand'];
        $prod->status = $data_array['status'];
    
        $prod->real_price = $data_array['real_price'];
        if(isset($data_array['price_type'])){
            $unitprice = UnitPriceType::find($data_array['price_type']);
            if ($unitprice) {
                $prod->price_type = $unitprice->unit_price_type_id;
                $prod->real_price = $unitprice->price;
            }
        }if(isset($data_array['vat_type'])){
            $vat = VatType::find($data_array['vat_type']);
            $prod->vat_type = $vat->vat_type_id;
        }if(isset($data_array['discount_allowed'])){
            $prod->discount_allowed = 1;
        }


        if(isset($data_array['category_id']) && $data_array['category_id']){
            $prod->category_id = $data_array['category_id'];
        } else {
            $prod->category_id = null;
        }



        if(isset($data_array['hallmarks'])){
            $prod->hallmarks()->sync($data_array['hallmarks']);
        }
        if(isset($data_array['product_colors'])){
            $prod->product_colors()->sync($data_array['product_colors']);
        }
        if(isset($data_array['product_statuses'])){
            $prod->product_statuses()->sync($data_array['product_statuses']);
        }
        if(isset($data_array['product_signs'])){
            $prod->product_signs()->sync($data_array['product_signs']);
        }


        foreach ($data_array as $attr => $param) {
            if(substr($attr, 0, 9) == 'hallmark_'){
                DB::table('hallmark_product')
                    ->where('hallmark_hallmark_id', substr($attr, 9))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['value' => $param]);
            }
            elseif(substr($attr, 0, 14) == 'product_color_'){
                DB::table('product_product_color')
                    ->where('product_color_product_color_id', substr($attr, 14))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['value' => $param]);
            }
            elseif(substr($attr, 0, 15) == 'product_status_'){
                DB::table('product_product_status')
                    ->where('product_status_product_status_id', substr($attr, 15))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['comment' => $param]);
            }
            elseif(substr($attr, 0, 13) == 'product_sign_'){
                DB::table('product_product_sign')
                    ->where('product_sign_product_sign_id', substr($attr, 13))
                    ->where('product_product_id', $prod->product_id)
                    ->update(['comment' => $param]);
            }
        }


        $prod->save();

        //cri VIZSGÁLAT
        $cri->investigation = 1;
        $cri->investigated_at = date('Y-m-d H:i:s');
        $cri->investigation_user_id = Auth::user()->id;
        $cri->save();
    }


    public function editcri(Request $request)
    {
        $cri = Inventory::find($request->inventory_id);

        $tags = Tag::orderBy('name', 'ASC')->get();

        return view('consignment_receipts.edit_cri', [
            'cri' => $cri,
            'tags' => $tags
        ]);
    }

}
