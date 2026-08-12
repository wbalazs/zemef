<?php

namespace App\Http\Controllers;

use App\Models\PurchaseTicket;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Employee;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\ZImage;
use App\Models\Hallmark;
use App\Models\ProductColor;
use App\Models\Category2;
use App\Models\ProductSign;
use App\Models\ProductStatus;
use App\Models\ProductType;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\UnitPriceType;
use App\Models\VatType;
use App\Models\Inventory;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Category;
use App\Tag;
use App\User;
use App\Http\Controllers\CategoryController;
use Carbon\Carbon;
use App\Http\Requests\PurchaseTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Services\CashRegister\CreateAutoCashTransaction;
use App\Services\CashRegister\DataTransferObjects\AutoCashTransactionData;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use File;
use App\Models\Karat;

class PurchaseTicketController extends Controller
{
    protected $categoryController;
    protected $stockController;

    public function __construct()
    {
        ////$this->authorizeResource(PurchaseTicket::class);
        $this->categoryController = new CategoryController();
        $this->stockController = new StockController();
    }

    public function index(PurchaseTicket $model)
    {
        PurchaseTicket::where('ticket_number', '--empty_row--')->delete();
        return view('purchase_tickets.index', [
            'purchase_tickets' => $model->orderBy('created_at', 'DESC')->get()
        ]);
    }

    public function create()
    {
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();
        $purchase_ticket = PurchaseTicket::create(['ticket_number' => '--empty_row--', 'wait_to_transfer' => 1]);
        $purchase_ticket->ticket_number = '';
        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $businessIds = auth()->user()->employee?->businesses?->pluck('business_id')->toArray() ?? [];
        
        $businesses = Business::where('status', 1)
            ->whereIn('business_id', $businessIds)
            ->orderBy('name', 'ASC')
            ->get();

        $inventoryItems = Inventory::where('source_type', 'purchase')
            ->where('source_header_id', $purchase_ticket->purchase_ticket_id)
            ->get();
        
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('purchase_tickets.edit', [
            'purchase_ticket' => $purchase_ticket,
            'customers' => $customers,
            'businesses' => $businesses,
            'inventoryItems' => $inventoryItems,
            'currencies' => Currency::where('status', 1)->orderBy('currency_id', 'ASC')->get(),
            'payments' => Payment::where('status', 1)->orderBy('name', 'ASC')->get(),
            'categories' => $categories,
            'karats' => $karats,
            'product_types' => $product_types,
        ]);
    }

    public function store(PurchaseTicketRequest $request, PurchaseTicket $model)
    {
        $purchase_ticket = $model->create($request->all());
        return redirect()->route('purchase_ticket.index')->withStatus('A vételi jegy sikeresen létrehozva.');
    }

    public function edit(PurchaseTicket $purchase_ticket)
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

        $storageId = $purchase_ticket->storage_id;
        $warehouse = Warehouse::find($storageId);

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $products = Product::orderBy('name', 'ASC')->get();
        $goldsmithes = User::where('is_goldsmith', '1')->orderBy('name', 'ASC')->get();

        $inventoryItems = Inventory::where('source_type', 'purchase')
            ->where('source_header_id', $purchase_ticket->purchase_ticket_id)
            ->get();

        $unaccepted_delivery_note = DB::table('delivery_note_items')
            ->join('inventory', 'delivery_note_items.inventory_item_id', '=', 'inventory.inventory_id')
            ->where('inventory.source_type', 'purchase')
            ->where('inventory.source_header_id', $purchase_ticket->purchase_ticket_id)
            ->whereNull('delivery_note_items.accepted_at')
            ->count();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('purchase_tickets.edit', [
            'purchase_ticket' => $purchase_ticket,
            'customers' => $customers,
            'businesses' => $businesses,
            'payments' => Payment::where('status', 1)->orderBy('name', 'ASC')->get(),
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'products' => $products,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_types' => $product_types,
            'product_signs' => $product_signs,
            'goldsmithes' => $goldsmithes,
            'warehouse' => $warehouse,
            'inventoryItems' => $inventoryItems,
            'unaccepted_delivery_note' => $unaccepted_delivery_note,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'currencies' => Currency::where('status', 1)->orderBy('currency_id', 'ASC')->get(),
            'show' => false,
            'karats' => $karats,
        ]);
    }

    public function update(PurchaseTicketRequest $request, PurchaseTicket $purchase_ticket)
    {
        $purchase_ticket->update($request->all());
        return redirect()->route('purchase_ticket.index')->withStatus('A vételi jegy adatai sikeresen mentve.');
    }

    public function destroy(PurchaseTicket $purchase_ticket)
    {
        try {
            $purchase_ticket->delete();
            return redirect()->route('purchase_ticket.index')->withStatus('A vételi jegy sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('purchase_ticket.index')->withErrors('Nem lehet törölni a vételi jegyet, mert kapcsolódó adatok vannak.');
        }
    }

    // ------------------- Segédmetódusok sorszámhoz -------------------
    private function getNextSerial()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'veteli_jegy')
            ->where('year', date('Y'))
            ->first();
        $serial = $record ? $record->serial_number : null;
        if (!$serial) {
            DB::table('serial_numbers')->insert(['module' => 'veteli_jegy', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }
        $next = $serial + 1;
        DB::table('serial_numbers')
            ->where('module', 'veteli_jegy')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);
        return $serial;
    }

    private function getNextSerialDeliveryNote()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'delivery_note')
            ->where('year', date('Y'))
            ->first();
        $serial = $record ? $record->serial_number : null;
        if (!$serial) {
            DB::table('serial_numbers')->insert(['module' => 'delivery_note', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }
        $next = $serial + 1;
        DB::table('serial_numbers')
            ->where('module', 'delivery_note')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);
        return $serial;
    }

    private function getNextSerialWarehouseTransfer()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'warehouse_transfer')
            ->where('year', date('Y'))
            ->first();
        $serial = $record ? $record->serial_number : null;
        if (!$serial) {
            DB::table('serial_numbers')->insert(['module' => 'warehouse_transfer', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }
        $next = $serial + 1;
        DB::table('serial_numbers')
            ->where('module', 'warehouse_transfer')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);
        return $serial;
    }

    // ------------------- AJAX mentések -------------------
    public function savePurchaseTicket(Request $request)
    {
        $purchase_ticket = PurchaseTicket::findOrFail($request->purchase_ticket_id);
        $closeTicket = filter_var($request->input('close', false), FILTER_VALIDATE_BOOLEAN);
        $wasClosed = (int) $purchase_ticket->status === 2;

        if (!$purchase_ticket->ticket_number || $purchase_ticket->ticket_number == '--empty_row--') {
            $serial = $this->getNextSerial();
            $purchase_ticket->ticket_number = 'VJEGY/' . $request->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        }

        $customer_id = $request->customer_id;
        if (!is_numeric($customer_id)) {
            $zip = Zip::where('id', $request->zip)->first();
            $city = City::where('id', $request->city)->first();
            $customer = Customer::create([
                'name' => $customer_id,
                'identity_card_number' => $request->identity_card_number,
                'tax_number' => $request->tax_number,
                'status' => 1,
            ]);
            CustomerAddress::create([
                'address_type' => 'Lakcím',
                'zip' => $zip->name,
                'zip_id' => $zip->id,
                'city' => $city->name,
                'city_id' => $city->id,
                'address' => $request->address,
                'customer_id' => $customer->customer_id,
            ]);
            $customer_id = $customer->customer_id;
        } else {
            $customer = Customer::find($customer_id);
            $customer_address = $customer->addresses->where('address_type', 'Lakcím')->first();
            if (!$customer_address) {
                $zip = Zip::where('id', $request->zip)->first();
                $city = City::where('id', $request->city)->first();
                CustomerAddress::create([
                    'address_type' => 'Lakcím',
                    'zip' => $zip->name,
                    'zip_id' => $zip->id,
                    'city' => $city->name,
                    'city_id' => $city->id,
                    'address' => $request->address,
                    'customer_id' => $customer->customer_id,
                ]);
            }
        }

        $updateData = [
            'employee_id' => Auth::user()->employee?->employee_id,
            'customer_id' => $customer_id,
            'currency_id' => $request->currency_id,
            'currency_value' => $request->currency_value,
            'business_id' => $request->business_id,
            'storage_id' => $request->storage_id,
            'payment_id' => $request->payment_id,
        ];

        if (!Schema::hasColumn('purchase_tickets', 'payment_id')) {
            unset($updateData['payment_id']);
        }

        $expenseAmount = null;
        if ($closeTicket && !$wasClosed) {
            $expenseAmount = (float) Inventory::where('source_type', 'purchase')
                ->where('source_header_id', $purchase_ticket->purchase_ticket_id)
                ->sum('price');

            if ($expenseAmount <= 0) {
                return response()->json([
                    'message' => 'Lezaras nem lehetseges, mert a veteli jegyhez nincs mentett tetel.'
                ], 422);
            }

            $updateData['status'] = 2;
        }

        $purchase_ticket->update($updateData);

        if ($closeTicket && !$wasClosed) {
            try {
                if ((int) ($updateData['payment_id'] ?? 0) === 7 && (float) $expenseAmount > 0) {
                    $customerName = (string) (Customer::find($customer_id)?->name ?? ('Partner #' . $customer_id));

                    $cashTransactionData = new AutoCashTransactionData(
                        type: 'expense',
                        amount: (float) $expenseAmount,
                        payer_payee: $customerName,
                        reason: 'purchase',
                        description: 'Automatikus penztar kiadas - Veteli jegy #' . $purchase_ticket->ticket_number
                    );

                    CreateAutoCashTransaction::from($cashTransactionData)->handle();
                }
            } catch (\Throwable $e) {
                \Log::error('Auto cash expense failed for purchase ticket close', [
                    'purchase_ticket_id' => $purchase_ticket->purchase_ticket_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        return true;
    }

    public function savePurchaseTicketItem(Request $request)
    {
        $purchase_ticket = PurchaseTicket::findOrFail($request->purchase_ticket_id);
        $termekTipusNames = [
            1 => 'Nyaklánc',
            2 => 'Fülbevaló',
            3 => 'Gyűrű',
            4 => 'Karkötő',
        ];
        $requestedTermekTipusId = $request->filled('termek_tipus_id') ? (int) $request->termek_tipus_id : null;
        $termekTipusId = $requestedTermekTipusId;
        $termekTipusName = $termekTipusNames[$termekTipusId] ?? 'Ismeretlen';
        $categoryId = $request->filled('new_category_id') ? (int) $request->new_category_id : null;
        $karatId = $request->filled('new_karat_id') ? (int) $request->new_karat_id : null;

        $applyItemData = function ($inventory) use ($categoryId, $karatId) {
            $itemData = $inventory->item_data;
            if (is_string($itemData)) {
                $itemData = json_decode($itemData, true);
            }
            if (!is_array($itemData)) {
                $itemData = [];
            }
            $itemData['category_id'] = $categoryId;
            $itemData['karat_id'] = $karatId;
            $inventory->item_data = $itemData;
        };

        // Módosítás
        if ($request->pti_id && is_numeric($request->pti_id)) {
            $inventory = Inventory::where('source_type', 'purchase')
                ->where('source_header_id', $request->purchase_ticket_id)
                ->where('inventory_id', $request->pti_id)
                ->firstOrFail();

            if (!$termekTipusId) {
                $termekTipusId = (int) $inventory->termek_tipus_id;
                $termekTipusName = $termekTipusNames[$termekTipusId] ?? ($inventory->name ?: 'Ismeretlen');
            }

            $inventory->product_id = null;
            $inventory->termek_tipus_id = $termekTipusId;
            //$inventory->code = Inventory::generateInventoryCode($termekTipusId);
            $inventory->name = $termekTipusName;
            $inventory->description = $request->pti_description;
            $inventory->quantity = $request->pti_quantity;
            $inventory->weight = $request->pti_weight;
            $inventory->factory_number = $request->pti_factory_number;
            $inventory->oneprice = $request->pti_oneprice;
            $inventory->price = $request->pti_oneprice * $request->pti_quantity;
            $applyItemData($inventory);
            $inventory->save();

            $inventory_id = $inventory->inventory_id;
        } else {
            if (!$termekTipusId) {
                return response()->json(['message' => 'A termék típus kötelező.'], 422);
            }

            // Új tétel
            $is_unique = filter_var($request->pti_is_unique, FILTER_VALIDATE_BOOLEAN);
            $quantity = (int) $request->pti_quantity;
            $oneprice = (int) $request->pti_oneprice;
            $weight = $request->pti_weight ?: 0;

            $inventoryItems = [];
            if ($is_unique) {
                for ($i = 0; $i < $quantity; $i++) {
                    $inventory = new Inventory();
                    $inventory->product_id = null;
                    $inventory->termek_tipus_id = $termekTipusId;
                    $inventory->code = $inventory->generateInventoryCode($termekTipusId);
                    $inventory->name = $termekTipusName;
                    $inventory->description = $request->pti_description;
                    $inventory->factory_number = $request->pti_factory_number;
                    $inventory->weight = $weight;
                    $inventory->quantity = 1;
                    $inventory->oneprice = $oneprice;
                    $inventory->price = $oneprice;
                    $inventory->comment = $request->pti_comment;
                    $inventory->status = 1;
                    $inventory->storage_id = $request->storage_id;
                    $inventory->source_type = 'purchase';
                    $inventory->source_header_id = $purchase_ticket->purchase_ticket_id;
                    $inventory->warehouse_to = $request->storage_id;
                    $applyItemData($inventory);
                    $inventory->save();
                    $inventoryItems[] = $inventory;
                }
            } else {
                $inventory = new Inventory();
                $inventory->product_id = null;
                $inventory->termek_tipus_id = $termekTipusId;
                $inventory->code = Inventory::generateInventoryCode($termekTipusId);
                $inventory->name = $termekTipusName;
                $inventory->description = $request->pti_description;
                $inventory->factory_number = $request->pti_factory_number;
                $inventory->weight = $weight;
                $inventory->quantity = $quantity;
                $inventory->oneprice = $oneprice;
                $inventory->price = $oneprice * $quantity;
                $inventory->comment = $request->pti_comment;
                $inventory->status = 1;
                $inventory->storage_id = $request->storage_id;
                $inventory->source_type = 'purchase';
                $inventory->source_header_id = $purchase_ticket->purchase_ticket_id;
                $inventory->warehouse_to = $request->storage_id;
                $applyItemData($inventory);
                $inventory->save();
                $inventoryItems[] = $inventory;
            }
            $inventory_id = $inventoryItems[0]->inventory_id;
        }

        $shouldPrint = filter_var($request->input('print', false), FILTER_VALIDATE_BOOLEAN);
        if ($shouldPrint) {
            $purchase_ticket->printed_at = now();
            $purchase_ticket->save();
            session()->flash('print', $purchase_ticket->purchase_ticket_id);
        } else {
            session()->forget('print');
        }

        return $inventory_id;
    }

    // ------------------- Nyomtatás -------------------
    public function printPurchaseTicket(Request $request)
    {
        array_map('unlink', glob(public_path() . '/pdf/purchase_tickets/*'));
        $file_name = 'veteli_jegy.pdf';
        $pdf = \App::make('dompdf.wrapper');
        $purchase_ticket = PurchaseTicket::findOrFail($request->purchase_ticket_id);
        $purchase_ticket->printed_at = now();
        $purchase_ticket->save();

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $view = count($purchase_ticket->inventoryItems) > 2 ? 'pdf.purchase_ticket_2' : 'pdf.purchase_ticket';
        $pdf->loadView($view, ['data' => ['purchase_ticket' => $purchase_ticket]]);
        //return view('pdf.purchase_ticket', ['data' => ['purchase_ticket' => $purchase_ticket]]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save(public_path() . '/pdf/purchase_tickets/' . $file_name);
        return $file_name;
    }

    // ------------------- Képkezelés (Inventory‑hez) -------------------
    public function saveAndUploadPTIImage(Request $request)
    {
        try {
            $file = $request->file('pti_image');
            $path = public_path('/document_storage/inventory_images/' . $request->pti_id . '/');
            if (!File::isDirectory($path)) File::makeDirectory($path, 0777, true, true);

            $image_resize = Image::make($file->getRealPath());
            $image_resize->save($path . $file->getClientOriginalName());

            $relativePath = $request->pti_id . '/' . $file->getClientOriginalName();
            ZImage::create([
                'resource_id' => $request->pti_id,
                'image_path' => $relativePath,
                'created_at' => now(),
                'module' => $request->module ?? 'inventory',
            ]);
            return response()->json(['status' => 'success', 'message' => 'Kép sikeresen feltöltve!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Hiba a kép feltöltése közben!'], 500);
        }
    }

    public function autoSavePTIImage(Request $request)
    {
        ZImage::create([
            'resource_id' => $request->pti_id,
            'image_path' => $request->image,
            'created_at' => now(),
            'module' => $request->module ?? 'inventory',
        ]);
    }

    public function loadPTIGallery(Request $request)
    {
        $images = ZImage::where('resource_id', $request->pti_id)
            ->where('module', $request->mod == '2' ? 'inventory2' : 'inventory')
            ->get();
        $html = '<div class="delete_buttons">';
        foreach ($images as $img) {
            $html .= '<div class="hidden_image_wrapper" data-img_id="' . $img->image_id . '">
                        <button data-mod="'.$request->mod.'" type="button" data-pti_id="' . $request->pti_id . '" data-img_id="'. $img->image_id . '" class="btn delete_img btn-danger btn-sm btn-icon"><i class="fa fa-times"></i></button>
                      </div>';
        }
        $html .= '</div><div data-lightGallery="1" id="lightgallery_pti_images'.$request->mod.'_' . $request->pti_id . '">';
        foreach ($images as $img) {
            $url = env('PUBLIC_URL') . '/document_storage/inventory_images/' . $img->image_path;
            $html .= '<div data-img_id="' . $img->image_id . '" class="inventory_img_wrapper" data-src="' . $url . '">
                        <img data-img_id="' . $img->image_id . '" class="img-responsive" src="' . $url . '" alt="">
                      </div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function deleteImg(Request $request)
    {
        $image = ZImage::findOrFail($request->img_id);
        $path = public_path('/document_storage/inventory_images/' . $image->image_path);
        if (file_exists($path)) unlink($path);
        $image->delete();
    }

    // ------------------- Másolás és sztornó -------------------
    public function copyPurchaseTicket(Request $request)
    {
        DB::beginTransaction();
        $original = PurchaseTicket::findOrFail($request->purchase_ticket_id);
        $serial = $this->getNextSerial();
        $copy = PurchaseTicket::create([
            'ticket_number' => 'VJEGY/' . $original->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial),
            'customer_id' => $original->customer_id,
            'business_id' => $original->business_id,
            'storage_id' => $original->storage_id,
            'employee_id' => $original->employee_id,
            'currency_id' => $original->currency_id,
            'currency_value' => $original->currency_value,
        ]);

        foreach ($original->inventoryItems as $item) {
            $newInventory = $item->replicate();
            $newInventory->source_header_id = $copy->purchase_ticket_id;
            $newInventory->code =  $newInventory->generateInventoryCode($original->termek_tipus_id);
            $newInventory->save();

            // Képek másolása
            $oldImages = ZImage::where('resource_id', $item->inventory_id)
                ->where('module', 'inventory')->get();
            foreach ($oldImages as $img) {
                $oldPath = public_path('/document_storage/inventory_images/' . $img->image_path);
                $newImagePath = $newInventory->inventory_id . '/' . basename($img->image_path);
                $newFullPath = public_path('/document_storage/inventory_images/' . $newImagePath);
                if (file_exists($oldPath)) {
                    File::copyDirectory(dirname($oldPath), dirname($newFullPath));
                    copy($oldPath, $newFullPath);
                }
                ZImage::create([
                    'resource_id' => $newInventory->inventory_id,
                    'image_path' => $newImagePath,
                    'created_at' => now(),
                    'module' => 'inventory',
                ]);
            }
        }
        DB::commit();
        return $copy->purchase_ticket_id;
    }

    public function cancelPurchaseTicket(Request $request)
    {
        try {
            DB::beginTransaction();
            $original = PurchaseTicket::findOrFail($request->purchase_ticket_id);
            if ($original->wait_to_transfer == 0) {
                return response()->json(['data' => ['success' => 'error', 'message' => 'A vételi jegyet nem lehet sztornózni, mert már át lett adva.']], 200);
            }

            $serial = $this->getNextSerial();
            $storno = PurchaseTicket::create([
                'ticket_number' => 'VJEGY/' . $original->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial),
                'customer_id' => $original->customer_id,
                'business_id' => $original->business_id,
                'storage_id' => $original->storage_id,
                'employee_id' => $original->employee_id,
                'currency_id' => $original->currency_id,
                'currency_value' => $original->currency_value,
                'storno_reason' => $request->storno_reason,
                'is_storno' => 1,
                'status' => 3,
            ]);

            $original->cancelled_at = now();
            $original->storno_pt_id = $storno->purchase_ticket_id;
            $original->wait_to_transfer = 0;
            $original->status = 4;
            $original->save();

            foreach ($original->inventoryItems as $item) {
                $stornoItem = $item->replicate();
                $stornoItem->quantity = -$item->quantity;
                $stornoItem->price = -$item->price;
                $stornoItem->status = 0;
                $stornoItem->source_header_id = $storno->purchase_ticket_id;
                //$stornoItem->code = Inventory::generateInventoryCode($item->termek_tipus_id);
                $stornoItem->save();
            }
            DB::commit();
            return response()->json(['data' => ['success' => 'success', 'purchase_ticket_id' => $storno->purchase_ticket_id]], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' => ['success' => 'error', 'message' => 'Hiba: ' . $e->getMessage()]], 200);
        }
    }

    // ------------------- Vizsgálat (investigation) -------------------
    public function saveInvestigation(Request $request)
    {
        $inventory = Inventory::findOrFail($request->pti_id);
        $employee_id = Auth::user()->employee?->employee_id;
        $customer_id = $inventory->purchaseTicket->customer_id ?? null;
        $warehouse_from = Warehouse::where('is_central', 1)->first()->warehouse_id;
        $business_id = Warehouse::find($warehouse_from)->businesses->first()->business_id;

        $goldsmith_warehouse_id = null;
        if ($request->goldsmith_id) {
            $user = User::find($request->goldsmith_id);
            $goldsmith_warehouse_id = $user?->employee?->warehouses->where('is_goldsmith', 1)->first()?->warehouse_id;
            if (!$goldsmith_warehouse_id) {
                return response()->json(['data' => ['success' => 'error', 'message' => 'A felhasználóhoz nincs ötvös raktár rendelve.']], 200);
            }
        }

        // Készlet ellenőrzés (ha nem értékesíthető)
        if ($request->investigation > 1 && $request->investigation != 3) {
            $stock = collect($this->stockController->get_actual_stock($warehouse_from, true));
            $stock_item = $stock->where('product_id', $inventory->product_id)->first();
            if (!$stock_item || $stock_item['stock'] < 1) {
                return response()->json(['data' => ['success' => 'error', 'message' => 'Nincs elegendő mennyiség a központi raktárban!']], 200);
            }
        }

        // Úticél meghatározása
        $warehouse_to = null;
        switch ($request->investigation) {
            case 1: // Értékesíthető – nem csinálunk semmit
                break;
            case 2: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_repair', 1)->first()->warehouse_id; break;
            case 3: $warehouse_to = Warehouse::where('is_hallmark', 1)->first()->warehouse_id; break;
            case 4: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_broken_gold', 1)->first()->warehouse_id; break;
            case 5: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_broken_silver', 1)->first()->warehouse_id; break;
            case 6: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_fake', 1)->first()->warehouse_id; break;
            default: $warehouse_to = $goldsmith_warehouse_id; break;
        }

        if ($request->investigation > 1 && $request->investigation != 3 && $warehouse_to) {
            $wt_id = $this->create_warehouse_transfer($warehouse_from, $warehouse_to, $employee_id, $business_id);
            $this->create_warehouse_transfer_item($wt_id, $inventory->inventory_id, 1, $warehouse_from, $warehouse_to, $request->inv_comment);
        } elseif ($request->investigation == 3 && $warehouse_to) {
            $dn_id = $this->create_delivery_note($warehouse_from, $warehouse_to, $employee_id, $business_id, $customer_id);
            $this->create_delivery_note_item($dn_id, $inventory->inventory_id, 1, $warehouse_from, $warehouse_to);
        }

        $inventory->investigation = 1;
        $inventory->investigated_at = now();
        $inventory->investigation_user_id = Auth::id();
        $inventory->inv_comment = $request->inv_comment;
        $inventory->status = 2; // Feldolgozott
        $inventory->save();

        $this->checkTicketStatus($inventory);
        return response()->json(['data' => ['success' => 'success', 'message' => 'A vizsgálat sikeres volt!']], 200);
    }

    public function saveInvestigationMore(Request $request)
    {
        $inventory = Inventory::findOrFail($request->pti_id);
        $totalQuantity = $request->totalValue;
        if ($totalQuantity > $inventory->quantity) {
            return response()->json(['data' => ['success' => 'error', 'message' => 'A vizsgálati mennyiség nem lehet nagyobb, mint a vételi jegyen szereplő mennyiség!']], 200);
        }

        $employee_id = Auth::user()->employee?->employee_id;
        $customer_id = $inventory->purchaseTicket->customer_id ?? null;
        $warehouse_from = Warehouse::where('is_central', 1)->first()->warehouse_id;
        $business_id = Warehouse::find($warehouse_from)->businesses->first()->business_id;

        $goldsmith_warehouse_id = null;
        if ($request->goldsmith_id) {
            $user = User::find($request->goldsmith_id);
            $goldsmith_warehouse_id = $user?->employee?->warehouses->where('is_goldsmith', 1)->first()?->warehouse_id;
            if (!$goldsmith_warehouse_id) {
                return response()->json(['data' => ['success' => 'error', 'message' => 'A felhasználóhoz nincs ötvös raktár rendelve.']], 200);
            }
        }

        foreach ($request->checkboxData as $data) {
            $quantity = $data['value'];
            if ($quantity == 0) continue;
            if ($data['id'] > 1) {
                $stock = collect($this->stockController->get_actual_stock($warehouse_from, true));
                $stock_item = $stock->where('product_id', $inventory->product_id)->first();
                if (!$stock_item || $stock_item['stock'] < $quantity) {
                    return response()->json(['data' => ['success' => 'error', 'message' => "Nem áll rendelkezésre {$quantity} db!"]], 200);
                }
            }

            $warehouse_to = null;
            switch ($data['id']) {
                case 1: continue 2;
                case 2: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_repair', 1)->first()->warehouse_id; break;
                case 3: $warehouse_to = Warehouse::where('is_hallmark', 1)->first()->warehouse_id; break;
                case 4: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_broken_gold', 1)->first()->warehouse_id; break;
                case 5: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_broken_silver', 1)->first()->warehouse_id; break;
                case 6: $warehouse_to = Warehouse::where('is_central_connected', 1)->where('is_fake', 1)->first()->warehouse_id; break;
                default: $warehouse_to = $goldsmith_warehouse_id; break;
            }

            if ($data['id'] == 3) {
                $dn_id = $this->create_delivery_note($warehouse_from, $warehouse_to, $employee_id, $business_id, $customer_id);
                $this->create_delivery_note_item($dn_id, $inventory->inventory_id, $quantity, $warehouse_from, $warehouse_to);
            } elseif ($warehouse_to) {
                $wt_id = $this->create_warehouse_transfer($warehouse_from, $warehouse_to, $employee_id, $business_id);
                $this->create_warehouse_transfer_item($wt_id, $inventory->inventory_id, $quantity, $warehouse_from, $warehouse_to, $request->inv_comment);
            }
        }

        $inventory->investigation = 1;
        $inventory->investigated_at = now();
        $inventory->investigation_user_id = Auth::id();
        $inventory->inv_comment = $request->inv_comment;
        $inventory->status = 2;
        $inventory->save();

        $this->checkTicketStatus($inventory);
        return response()->json(['data' => ['success' => 'success', 'message' => 'A vizsgálat sikeres volt!']], 200);
    }

    private function checkTicketStatus(Inventory $inventory)
    {
        $ticket = $inventory->purchaseTicket;
        $allProcessed = $ticket->inventoryItems->every(fn($i) => $i->status == 2);
        if ($allProcessed) {
            $ticket->status = 2; // Feldolgozva
            $ticket->save();
        }
    }

    // ------------------- Raktármozgások (inventory‑alapú) -------------------
    private function create_warehouse_transfer($warehouse_from, $warehouse_to, $employee_id, $business_id)
    {
        $serial = $this->getNextSerialWarehouseTransfer();
        $transfer_number = 'RKOZ/' . $business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        $business = Business::find($business_id);
        $employee = Employee::find($employee_id);
        $warehouseTransfer = WarehouseTransfer::create([
            'warehouse_transfer_number' => $transfer_number,
            'employee_id' => $employee_id,
            'employee_name' => $employee->lastname . ' ' . $employee->firstname,
            'business_id' => $business_id,
            'business_name' => $business->name,
            'business_address' => $business->zip . ' ' . $business->city . ', ' . $business->address,
            'warehouse_from' => $warehouse_from,
            'warehouse_from_name' => Warehouse::find($warehouse_from)->name,
            'warehouse_to' => $warehouse_to,
            'warehouse_to_name' => Warehouse::find($warehouse_to)->name,
        ]);
        return $warehouseTransfer->warehouse_transfer_id;
    }

    private function create_warehouse_transfer_item($transfer_id, $inventory_item_id, $quantity, $warehouse_from, $warehouse_to, $note = null)
    {
        WarehouseTransferItem::create([
            'warehouse_transfer_id' => $transfer_id,
            'inventory_item_id' => $inventory_item_id,
            'quantity' => $quantity,
            'warehouse_from' => $warehouse_from,
            'warehouse_to' => $warehouse_to,
            'note' => $note,
        ]);
    }

    private function create_delivery_note($warehouse_from, $warehouse_to, $employee_id, $business_id, $customer_id)
    {
        $serial = $this->getNextSerialDeliveryNote();
        $note_number = 'SZALL/' . $business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        $business = Business::find($business_id);
        $employee = Employee::find($employee_id);
        $customer = Customer::find($customer_id);
        return DeliveryNote::create([
            'delivery_note_number' => $note_number,
            'employee_id' => $employee_id,
            'employee_name' => $employee->lastname . ' ' . $employee->firstname,
            'business_id' => $business_id,
            'business_name' => $business->name,
            'business_address' => $business->zip . ' ' . $business->city . ', ' . $business->address,
            'customer_id' => $customer_id,
            'customer_name' => $customer->name ?? null,
            'received_at' => now(),
            'receive_employee_id' => $employee_id,
            'receive_employee_name' => $employee->lastname . ' ' . $employee->firstname,
            'warehouse_from' => $warehouse_from,
            'warehouse_from_name' => Warehouse::find($warehouse_from)->name,
            'warehouse_to' => $warehouse_to,
            'warehouse_to_name' => Warehouse::find($warehouse_to)->name,
        ])->delivery_note_id;
    }

    private function create_delivery_note_item($delivery_note_id, $inventory_item_id, $quantity, $warehouse_from, $warehouse_to)
    {
        $inventory = Inventory::findOrFail($inventory_item_id);
        $product = $inventory->product;
        DeliveryNoteItem::create([
            'delivery_note_id' => $delivery_note_id,
            'inventory_item_id' => $inventory_item_id,
            'product_name' => $product->name,
            'product_description' => $inventory->description,
            'factory_number' => $inventory->factory_number,
            'weight' => $inventory->weight,
            'quantity' => $quantity,
            'price' => $inventory->price,
            'warehouse_from' => $warehouse_from,
            'warehouse_to' => $warehouse_to,
            'accepted_employee_id' => Auth::user()->employee?->employee_id,
            'accepted_at' => now(),
        ]);
    }

    // ------------------- Termék adatlap mentés -------------------
    public function savePTIProduct(Request $request)
    {
        $inventory = Inventory::findOrFail($request->pti_id);
        $storage_id = $request->storage_id;
        $form_data = urldecode($request->form_data);
        parse_str($form_data, $data);

        $product = $inventory->product;
        $product->name = $data['name'];
        $product->short_description = $data['short_description'];
        $product->long_description = $data['long_description'];
        $product->brand = $data['brand'];
        $product->status = $data['status'];
        if (!empty($data['price_type'])) {
            $unitprice = UnitPriceType::find($data['price_type']);
            $product->price_type = $unitprice->unit_price_type_id;
            $product->real_price = $unitprice->price;
        }
        if (!empty($data['vat_type'])) {
            $vat = VatType::find($data['vat_type']);
            $product->vat_type = $vat->vat_type_id;
        }
        $product->discount_allowed = isset($data['discount_allowed']) ? 1 : 0;
        $product->category_id = $data['category_id'] ?? null;

        if (isset($data['hallmarks'])) $product->hallmarks()->sync($data['hallmarks']);
        if (isset($data['product_colors'])) $product->product_colors()->sync($data['product_colors']);
        if (isset($data['product_statuses'])) $product->product_statuses()->sync($data['product_statuses']);
        if (isset($data['product_signs'])) $product->product_signs()->sync($data['product_signs']);

        foreach ($data as $attr => $param) {
            if (str_starts_with($attr, 'hallmark_')) {
                DB::table('hallmark_product')
                    ->where('hallmark_hallmark_id', substr($attr, 9))
                    ->where('product_product_id', $product->product_id)
                    ->update(['value' => $param]);
            } elseif (str_starts_with($attr, 'product_color_')) {
                DB::table('product_product_color')
                    ->where('product_color_product_color_id', substr($attr, 14))
                    ->where('product_product_id', $product->product_id)
                    ->update(['value' => $param]);
            } elseif (str_starts_with($attr, 'product_status_')) {
                DB::table('product_product_status')
                    ->where('product_status_product_status_id', substr($attr, 15))
                    ->where('product_product_id', $product->product_id)
                    ->update(['comment' => $param]);
            } elseif (str_starts_with($attr, 'product_sign_')) {
                DB::table('product_product_sign')
                    ->where('product_sign_product_sign_id', substr($attr, 13))
                    ->where('product_product_id', $product->product_id)
                    ->update(['comment' => $param]);
            }
        }
        $product->save();

        $inventory->investigation = 1;
        $inventory->investigated_at = now();
        $inventory->investigation_user_id = Auth::id();
        $inventory->save();
    }

    // ------------------- Egyéb, még használt metódusok -------------------
    public function get_storages_by_business_id_employee(Request $request)
    {
        $business_id = $request->business_id;
        $business_id =  auth()->user()->employee?->businesses->pluck('business_id');
        
        $employee = Auth::user()->employee;
        $warehouseIds = DB::table('business_warehouse')
            ->whereIn('business_business_id', $business_id)
            ->pluck('warehouse_warehouse_id');

        $employeeWarehouses = Warehouse::whereIn('warehouse_id', $warehouseIds)
            ->where('status', 1)
            ->where('is_receiving', 1)
            /*
            ->where(function ($query) {
                $query->where('is_receiving', 1)
                      ->orWhere('is_consignment', 1);
            })
                      */
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

    public function save_pti_comment(Request $request)
    {
        DB::table('warehouse_transfer_items')
            ->where('transfer_items_id', $request->transfer_items_id)
            ->update(['note' => $request->transfer_items_note]);
        return response()->json(['data' => ['success' => 'Megjegyzés sikeresen elmentve!']]);
    }

    public function save_pti_repair_price(Request $request)
    {
        DB::table('warehouse_transfer_items')
            ->where('transfer_items_id', $request->transfer_items_id)
            ->update(['repair_price' => $request->repair_price]);
        return response()->json(['data' => ['success' => 'A javítási ár sikeresen elmentve!']]);
    }

    public function displayRepairList(Request $request)
    {
        $employeeWarehouseIds = auth()->user()->employee?->warehouses->where('is_repair', 1)?->pluck('warehouse_id')->toArray();
        $allWarehouses = Warehouse::all()->keyBy('warehouse_id');
        $repairItems = DB::table('warehouse_transfer_items as wti')
            ->join('inventory as i', 'wti.inventory_item_id', '=', 'i.inventory_id')
            ->join('products as p', 'i.product_id', '=', 'p.product_id')
            ->leftJoin('warehouse_transfer as wt', 'wti.warehouse_transfer_id', '=', 'wt.warehouse_transfer_id')
            ->where('wti.is_repair', 1)
            ->select([
                'wti.transfer_items_id as item_id',
                'i.inventory_id',
                'p.name as product_name',
                'i.description',
                'wti.quantity',
                'wti.warehouse_from',
                'wti.warehouse_to',
                'wti.repair_price',
                'wti.accepted_at',
                'wti.back_to_storage_at',
                'wti.note as notes',
                'wt.warehouse_transfer_number',
                'wt.warehouse_transfer_id as id',
                DB::raw("'warehouse_transfer' as type")
            ])
            ->get();
        return view('purchase_tickets.repair', ['repair_list' => $repairItems, 'all_warehouse' => $allWarehouses]);
    }

    public function displayWaitedList(Request $request)
    {
        $waited_list = PurchaseTicket::with(['customer', 'warehouse', 'inventoryItems'])
            ->where('wait_to_transfer', 1)
            ->whereNull('is_storno')
            ->whereNotNull('printed_at')
            ->orderBy('ticket_number')
            ->get();

        $categoryMap = Category::where('status', 1)->pluck('name', 'id')->toArray();
        $karatMap = Karat::where('status', 1)->pluck('name', 'karat_id')->toArray();

        $customerOptions = $waited_list
            ->filter(fn($ticket) => $ticket->customer)
            ->map(fn($ticket) => [
                'customer_id' => $ticket->customer->customer_id,
                'name' => $ticket->customer->name,
            ])
            ->unique('customer_id')
            ->sortBy('name')
            ->values();

        $businessIds = $waited_list->pluck('business_id')->filter()->unique()->values();
        $businessOptions = Business::whereIn('business_id', $businessIds)
            ->orderBy('name', 'ASC')
            ->get(['business_id', 'name']);

        $storageIds = $waited_list->pluck('storage_id')->filter()->unique()->values();
        $storageOptions = Warehouse::whereIn('warehouse_id', $storageIds)
            ->orderBy('name', 'ASC')
            ->get(['warehouse_id', 'name']);
            
        return view('purchase_tickets.waited_list', [
            'waited_list' => $waited_list,
            'categoryMap' => $categoryMap,
            'karatMap' => $karatMap,
            'customerOptions' => $customerOptions,
            'businessOptions' => $businessOptions,
            'storageOptions' => $storageOptions,
        ]);
    }

    public function displayExtensionList(Request $request)
    {
        $rows = PurchaseTicket::query()
            ->from('purchase_tickets as pt')
            ->leftJoin('customers as c', 'pt.customer_id', '=', 'c.customer_id')
            ->leftJoin('businesses as b', 'pt.business_id', '=', 'b.business_id')
            ->leftJoin('employees as e', 'pt.employee_id', '=', 'e.employee_id')
            ->leftJoin('inventory as i', function ($join) {
                $join->on('i.source_header_id', '=', 'pt.purchase_ticket_id')
                    ->where('i.source_type', '=', 'purchase');
            })
            ->where('pt.wait_to_transfer', 2)
            ->whereNull('pt.is_storno')
            ->groupBy([
                'pt.purchase_ticket_id',
                'pt.ticket_number',
                'pt.created_at',
                'c.name',
                'b.name',
                'e.lastname',
                'e.firstname',
            ])
            ->select([
                'pt.purchase_ticket_id',
                'pt.ticket_number',
                'pt.created_at as purchase_ticket_date',
                'c.name as customer_name',
                'b.name as business_name',
                DB::raw('TRIM(CONCAT(COALESCE(e.lastname, ""), " ", COALESCE(e.firstname, ""))) as employee_name'),
                DB::raw('COALESCE(SUM(i.quantity), 0) as item_count'),
                DB::raw('COALESCE(SUM(i.weight), 0) as total_weight'),
                DB::raw('COALESCE(SUM(i.price), 0) as total_amount'),
                DB::raw('(SELECT dn.delivery_note_number
                    FROM delivery_note_items dni
                    JOIN delivery_note dn ON dn.delivery_note_id = dni.delivery_note_id
                    JOIN inventory inv ON inv.inventory_id = dni.inventory_item_id
                    WHERE inv.source_type = "purchase"
                      AND inv.source_header_id = pt.purchase_ticket_id
                    ORDER BY dn.delivery_note_id DESC
                    LIMIT 1) as delivery_note_number'),
                DB::raw('(SELECT dn.created_at
                    FROM delivery_note_items dni
                    JOIN delivery_note dn ON dn.delivery_note_id = dni.delivery_note_id
                    JOIN inventory inv ON inv.inventory_id = dni.inventory_item_id
                    WHERE inv.source_type = "purchase"
                      AND inv.source_header_id = pt.purchase_ticket_id
                    ORDER BY dn.delivery_note_id DESC
                    LIMIT 1) as delivery_note_date'),
            ])
            ->orderByDesc('pt.created_at')
            ->get();

        return view('purchase_tickets.extension_list', [
            'rows' => $rows,
        ]);
    }

    public function editExtension(PurchaseTicket $purchase_ticket)
    {
        $purchase_ticket->load(['customer', 'business']);

        $businessIds = auth()->user()->employee?->businesses?->pluck('business_id')->toArray() ?? [];
        $businesses = Business::where('status', 1)
            ->whereIn('business_id', $businessIds)
            ->orderBy('name', 'ASC')
            ->get();

        $headerData = [];
        if (is_array($purchase_ticket->options)) {
            $headerData = $purchase_ticket->options['extension'] ?? [];
        }

        $latestDeliveryData = DB::table('delivery_note_items as dni')
            ->join('delivery_note as dn', 'dn.delivery_note_id', '=', 'dni.delivery_note_id')
            ->join('inventory as inv', 'inv.inventory_id', '=', 'dni.inventory_item_id')
            ->where('inv.source_type', 'purchase')
            ->where('inv.source_header_id', $purchase_ticket->purchase_ticket_id)
            ->orderByDesc('dn.delivery_note_id')
            ->select('dn.delivery_note_number', 'dn.created_at')
            ->first();

        if (empty($headerData['delivery_note_number']) && !empty($latestDeliveryData?->delivery_note_number)) {
            $headerData['delivery_note_number'] = $latestDeliveryData->delivery_note_number;
        }

        if (empty($headerData['fulfillment_date']) && !empty($purchase_ticket->created_at)) {
            $headerData['fulfillment_date'] = Carbon::parse($purchase_ticket->created_at)->format('Y-m-d');
        }

        $businessMarkings = collect();
        if ($purchase_ticket->business) {
            $businessMarkings = $purchase_ticket->business->business_markings()
                ->with('productStatus.vatType')
                ->orderByDesc('default')
                ->orderBy('name', 'ASC')
                ->get();
        }

        $inventoryItems = Inventory::query()
            ->where('source_type', 'purchase')
            ->where('source_header_id', $purchase_ticket->purchase_ticket_id)
            ->orderBy('inventory_id', 'ASC')
            ->get();

        $groupedInventoryItems = $inventoryItems->groupBy('inventory_id');

        $productTypeMap = ProductType::where('status', 1)
            ->pluck('name', 'product_type_id')
            ->toArray();

        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();

        $brokenGoldWarehouseName = Warehouse::query()
            ->where('status', 1)
            ->where('is_broken_gold', 1)
            ->orderBy('name', 'ASC')
            ->value('name');

        return view('purchase_tickets.extension_edit', [
            'purchase_ticket' => $purchase_ticket,
            'headerData' => $headerData,
            'businessMarkings' => $businessMarkings,
            'businesses' => $businesses,
            'inventoryItems' => $inventoryItems,
            'groupedInventoryItems' => $groupedInventoryItems,
            'productTypeMap' => $productTypeMap,
            'brokenGoldWarehouseName' => $brokenGoldWarehouseName,
            'categories' => $categories,
            'vat_types' => $vat_types,
            'karats' => $karats,
            'category2s' => $category2s,
            'product_statuses' => $product_statuses,
            'product_colors' => $product_colors,
            'product_types' => $product_types,
        ]);
    }

    public function updateExtension(Request $request, PurchaseTicket $purchase_ticket)
    {
        $validated = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,business_id'],
            'business_marking_id' => ['nullable', 'integer', 'exists:business_markings,id'],
            'delivery_note_number' => ['nullable', 'string', 'max:255'],
            'fulfillment_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.business_marking_id' => ['nullable', 'integer', 'exists:business_markings,id'],
            'items.*.is_broken_gold' => ['nullable', 'boolean'],
        ]);

        $options = is_array($purchase_ticket->options) ? $purchase_ticket->options : [];

        $selectedBusiness = Business::where('business_id', $validated['business_id'])->first();
        $markingsById = $selectedBusiness
            ? $selectedBusiness->business_markings()->with('productStatus.vatType')->get()->keyBy('id')
            : collect();

        $marking = null;
        if (!empty($validated['business_marking_id'])) {
            $marking = $markingsById->get((int) $validated['business_marking_id']);
        }

        $options['extension'] = [
            'business_id' => $validated['business_id'],
            'business_marking_id' => $validated['business_marking_id'] ?? null,
            'business_marking_name' => optional($marking)->name,
            'delivery_note_number' => $validated['delivery_note_number'] ?? null,
            'fulfillment_date' => $validated['fulfillment_date'] ?? null,
            'purchase_price' => $validated['purchase_price'] ?? null,
            'sale_price' => optional(optional($marking)->productStatus)->price,
            'vat_type_id' => optional(optional($marking)->productStatus)->vat_type_id,
            'vat_name' => optional(optional(optional($marking)->productStatus)->vatType)->name,
        ];

        $purchase_ticket->business_id = (int) $validated['business_id'];
        $purchase_ticket->options = $options;
        $purchase_ticket->save();

        $headerMarkingId = !empty($validated['business_marking_id']) ? (int) $validated['business_marking_id'] : null;
        $brokenGoldWarehouseId = $this->resolveBrokenGoldWarehouseId((int) $validated['business_id']);
        $itemPayload = $validated['items'] ?? [];

        $inventoryItems = Inventory::query()
            ->where('source_type', 'purchase')
            ->where('source_header_id', $purchase_ticket->purchase_ticket_id)
            ->get();

        foreach ($inventoryItems as $inventoryItem) {
            $row = $itemPayload[$inventoryItem->inventory_id] ?? [];
            $rowMarkingId = !empty($row['business_marking_id']) ? (int) $row['business_marking_id'] : $headerMarkingId;
            $rowMarking = $rowMarkingId ? $markingsById->get($rowMarkingId) : null;
            $isBrokenGold = !empty($row['is_broken_gold']);

            $itemData = $inventoryItem->item_data;
            if (!is_array($itemData)) {
                $itemData = is_string($itemData) ? (json_decode($itemData, true) ?: []) : [];
            }

            $itemData['customer_marking_id'] = $rowMarkingId;
            $itemData['vzj'] = optional($rowMarking)->name;
            $itemData['is_broken_gold'] = $isBrokenGold ? 1 : 0;
            $inventoryItem->item_data = $itemData;

            if ($isBrokenGold && $brokenGoldWarehouseId) {
                $inventoryItem->storage_id = $brokenGoldWarehouseId;
            }

            $inventoryItem->save();
        }

        return redirect()
            ->route('purchase_tickets.extension_edit', ['purchase_ticket' => $purchase_ticket->purchase_ticket_id])
            ->withStatus('A kiegészítési adatok sikeresen mentve.');
    }

    private function resolveBrokenGoldWarehouseId(int $businessId): ?int
    {
        $businessWarehouseId = Warehouse::query()
            ->where('status', 1)
            ->where('is_broken_gold', 1)
            ->whereExists(function ($query) use ($businessId) {
                $query->select(DB::raw(1))
                    ->from('business_warehouse')
                    ->whereRaw('business_warehouse.warehouse_warehouse_id = warehouses.warehouse_id')
                    ->where('business_warehouse.business_business_id', $businessId);
            })
            ->orderBy('name', 'ASC')
            ->value('warehouse_id');

        if ($businessWarehouseId) {
            return (int) $businessWarehouseId;
        }

        $fallbackWarehouseId = Warehouse::query()
            ->where('status', 1)
            ->where('is_broken_gold', 1)
            ->orderBy('name', 'ASC')
            ->value('warehouse_id');

        return $fallbackWarehouseId ? (int) $fallbackWarehouseId : null;
    }

    public function saveExtensionItemInline(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|integer|exists:inventory,inventory_id',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'oneprice' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'factory_number' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'business_marking_id' => 'nullable|integer|exists:business_markings,id',
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
            'is_broken_gold' => 'nullable|boolean',
            'business_id' => 'required|integer|exists:businesses,business_id',
        ]);

        $inventory = Inventory::findOrFail($validated['inventory_id']);
        if ($inventory->source_type !== 'purchase') {
            return response()->json(['message' => 'Nem vételi jegy tétel.'], 422);
        }

        $purchaseTicket = PurchaseTicket::where('purchase_ticket_id', $inventory->source_header_id)->first();
        if (!$purchaseTicket) {
            return response()->json(['message' => 'Vételi jegy nem található.'], 404);
        }

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
        if (!empty($resolvedInventoryName)) {
            $inventory->name = $resolvedInventoryName;
        }

        $itemData = $inventory->item_data;
        if (!is_array($itemData)) {
            $itemData = is_string($itemData) ? (json_decode($itemData, true) ?: []) : [];
        }

        $selectedBusinessMarking = null;
        if (!empty($validated['business_marking_id'])) {
            $selectedBusinessMarking = Business::where('business_id', (int) $validated['business_id'])
                ->first()
                ?->business_markings()
                ->with('productStatus.vatType')
                ->where('id', (int) $validated['business_marking_id'])
                ->first();
        }

        $itemData['business_marking_id'] = $selectedBusinessMarking?->id;
        $itemData['customer_marking_id'] = $selectedBusinessMarking?->id;
        $itemData['vzj'] = $selectedBusinessMarking?->name;
        $itemData['category_id'] = $validated['category_id'] ?? ($itemData['category_id'] ?? null);
        $itemData['beszerzesi_ar'] = $validated['beszerzesi_ar'] ?? ($itemData['beszerzesi_ar'] ?? null);
        $itemData['eladasi_ar'] = $validated['eladasi_ar'] ?? ($itemData['eladasi_ar'] ?? null);
        $itemData['vat_type_id'] = $validated['vat_type_id'] ?? ($itemData['vat_type_id'] ?? null);
        $itemData['hallmarks'] = $validated['hallmarks'] ?? 0;
        $itemData['statuses'] = $validated['statuses'] ?? ($itemData['statuses'] ?? []);
        $itemData['colors'] = $validated['colors'] ?? ($itemData['colors'] ?? []);
        $itemData['category2_id'] = $validated['category2_id'] ?? ($itemData['category2_id'] ?? null);
        $itemData['size'] = $validated['size'] ?? ($itemData['size'] ?? null);
        $itemData['label_type'] = $validated['label_type'] ?? ($itemData['label_type'] ?? null);
        $itemData['karat_id'] = $validated['karat_id'] ?? ($itemData['karat_id'] ?? null);
        $itemData['termek_tipus_id'] = $inventory->termek_tipus_id;
        $itemData['is_broken_gold'] = !empty($validated['is_broken_gold']) ? 1 : 0;

        $inventory->item_data = $itemData;

        if (!empty($validated['is_broken_gold'])) {
            $brokenGoldWarehouseId = $this->resolveBrokenGoldWarehouseId((int) $validated['business_id']);
            if ($brokenGoldWarehouseId) {
                $inventory->storage_id = $brokenGoldWarehouseId;
            }
        }

        $inventory->save();

        return response()->json([
            'success' => true,
            'message' => 'A tétel sikeresen frissítve.',
            'inventory' => $inventory,
        ]);
    }

    public function updateWaitedTicketStorage(Request $request)
    {
        $request->validate([
            'purchase_ticket_id' => 'required|integer',
            'storage_id' => 'required|integer',
        ]);

        $purchaseTicket = PurchaseTicket::where('purchase_ticket_id', $request->purchase_ticket_id)
            ->where('wait_to_transfer', 1)
            ->whereNull('is_storno')
            ->firstOrFail();

        $employee = Auth::user()->employee;
        if (!$employee) {
            return response()->json(['data' => ['success' => 'error', 'message' => 'Nincs hozzárendelt dolgozó.']], 422);
        }
        /*        
        $warehouseIds = DB::table('business_warehouse')
            ->where('business_business_id', $purchaseTicket->business_id)
            ->pluck('warehouse_warehouse_id');
        */
        $isAllowedWarehouse = Warehouse::where('warehouse_id', $request->storage_id)
            ->where('status', 1)
            //->where('is_receiving', 1)
            ->whereExists(function ($query) use ($employee) {
                $query->select(DB::raw(1))
                    ->from('employee_warehouse')
                    ->whereRaw('employee_warehouse.warehouse_warehouse_id = warehouses.warehouse_id')
                    ->where('employee_employee_id', $employee->employee_id);
            })
            ->first();

        if (!$isAllowedWarehouse) {
            return response()->json(['data' => ['success' => 'error', 'message' => 'A kiválasztott célraktár nem engedélyezett.']], 422);
        }

        $purchaseTicket->storage_id = (int) $request->storage_id;
        $purchaseTicket->save();

        return response()->json([
            'data' => [
                'success' => 'success',
                'message' => 'A célraktár sikeresen módosítva.',
                'warehouse_name' => $isAllowedWarehouse->name,
            ]
        ]);
    }

    public function displayWaitToAcceptList(Request $request)
{
    return view('purchase_tickets.wait_to_accept_list');
}

    private function getWaitToAcceptItemsQuery(array $employeeWarehouseIds)
    {
        $deliveryNoteItems = DB::table('delivery_note_items as dni')
            ->leftJoin('delivery_note as dn', 'dni.delivery_note_id', '=', 'dn.delivery_note_id')
            ->leftJoin('warehouses as wf', 'dni.warehouse_from', '=', 'wf.warehouse_id')
            ->leftJoin('warehouses as wt', 'dni.warehouse_to', '=', 'wt.warehouse_id')
            ->whereIn('dni.warehouse_to', $employeeWarehouseIds)
            ->whereNull('dni.accepted_at')
            ->select([
                'dni.note_items_id as item_id',
                'dn.delivery_note_id as id',
                DB::raw("'delivery_note' as type"),
                DB::raw("'Szállítólevél' as type_text"),
                'dn.delivery_note_number as document_number',
                DB::raw("'' as foreign_receipt_name"),
                DB::raw("'' as shipping_label"),
                'dni.warehouse_from',
                'dni.warehouse_to',
                DB::raw('COALESCE(wf.name, "") as warehouse_from_name'),
                DB::raw('COALESCE(wt.name, "") as warehouse_to_name'),
                DB::raw('COALESCE(dni.product_name, "") as product_name'),
                DB::raw('COALESCE(dni.quantity, 0) as quantity'),
                DB::raw('COALESCE(dn.customer_name, "") as customer_name')
            ]);

        $warehouseTransferItems = DB::table('warehouse_transfer_items as wti')
            ->join('inventory as i', 'wti.inventory_item_id', '=', 'i.inventory_id')
            ->leftJoin('warehouse_transfer as wtr', 'wti.warehouse_transfer_id', '=', 'wtr.warehouse_transfer_id')
            ->leftJoin('warehouses as wf', 'wti.warehouse_from', '=', 'wf.warehouse_id')
            ->leftJoin('warehouses as wt', 'wti.warehouse_to', '=', 'wt.warehouse_id')
            ->whereIn('wti.warehouse_to', $employeeWarehouseIds)
            ->whereNull('wti.accepted_at')
            ->select([
                'wti.transfer_items_id as item_id',
                'wtr.warehouse_transfer_id as id',
                DB::raw("'warehouse_transfer' as type"),
                DB::raw("'Raktárközi' as type_text"),
                'wtr.warehouse_transfer_number as document_number',
                DB::raw("'' as foreign_receipt_name"),
                DB::raw("'' as shipping_label"),
                'wti.warehouse_from',
                'wti.warehouse_to',
                DB::raw('COALESCE(wf.name, "") as warehouse_from_name'),
                DB::raw('COALESCE(wt.name, "") as warehouse_to_name'),
                DB::raw('COALESCE(i.name, "") as product_name'),
                DB::raw('COALESCE(wti.quantity, 0) as quantity'),
                DB::raw("'' as customer_name")
            ]);

        return $deliveryNoteItems->unionAll($warehouseTransferItems);
    }

    public function getWaitToAcceptListData(Request $request)
    {
        $employeeWarehouseIds = auth()->user()->employee?->warehouses->pluck('warehouse_id')->toArray() ?? [];

        if (empty($employeeWarehouseIds)) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        $baseUnion = $this->getWaitToAcceptItemsQuery($employeeWarehouseIds);
        $query = DB::query()->fromSub($baseUnion, 'items');

        $recordsTotal = (clone $query)->count();

        $globalSearch = trim((string) $request->input('search.value', ''));
        if ($globalSearch !== '') {
            $query->where(function ($q) use ($globalSearch) {
                $q->where('type_text', 'like', "%{$globalSearch}%")
                    ->orWhere('document_number', 'like', "%{$globalSearch}%")
                    ->orWhere('foreign_receipt_name', 'like', "%{$globalSearch}%")
                    ->orWhere('shipping_label', 'like', "%{$globalSearch}%")
                    ->orWhere('warehouse_from_name', 'like', "%{$globalSearch}%")
                    ->orWhere('warehouse_to_name', 'like', "%{$globalSearch}%")
                    ->orWhere('product_name', 'like', "%{$globalSearch}%")
                    ->orWhere('customer_name', 'like', "%{$globalSearch}%");
            });
        }

        $columnMap = [
            1 => 'type_text',
            2 => 'document_number',
            3 => 'foreign_receipt_name',
            4 => 'shipping_label',
            5 => 'warehouse_from_name',
            6 => 'warehouse_to_name',
            7 => 'product_name',
            8 => 'quantity',
            9 => 'customer_name',
        ];

        foreach ($columnMap as $index => $columnName) {
            $value = trim((string) $request->input("columns.{$index}.search.value", ''));
            if ($value !== '') {
                $query->where($columnName, 'like', "%{$value}%");
            }
        }

        $recordsFiltered = (clone $query)->count();

        $orderColumnIndex = (int) $request->input('order.0.column', 2);
        $orderDir = strtolower((string) $request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columnMap[$orderColumnIndex] ?? 'document_number';

        $start = max((int) $request->input('start', 0), 0);
        $length = (int) $request->input('length', 25);
        if ($length <= 0) {
            $length = 25;
        }

        $rows = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($length)
            ->get();

        $data = $rows->map(function ($item) {
            return [
                'checkbox' => '<input type="checkbox" class="note-checkbox" data-type="' . e($item->type) . '" data-item_id="' . e($item->item_id) . '" data-id="' . e($item->id) . '">',
                'type_text' => $item->type_text,
                'document_number' => $item->document_number,
                'foreign_receipt_name' => $item->foreign_receipt_name,
                'shipping_label' => $item->shipping_label,
                'warehouse_from_name' => $item->warehouse_from_name,
                'warehouse_to_name' => $item->warehouse_to_name,
                'product_name' => $item->product_name,
                'quantity_text' => $item->quantity . ' db',
                'customer_name' => $item->customer_name,
                'actions' => '<button type="button" class="btn btn-sm btn-primary open-transfer-details" data-type="' . e($item->type) . '" data-id="' . e($item->id) . '"><i class="fa fa-eye"></i> Adatlap</button>',
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function getWaitToAcceptItemDetails(Request $request)
    {
        $request->validate([
            'type' => 'required|in:delivery_note,warehouse_transfer',
            'id' => 'required|integer|min:1',
        ]);

        $employeeWarehouseIds = auth()->user()->employee?->warehouses->pluck('warehouse_id')->toArray() ?? [];
        if (empty($employeeWarehouseIds)) {
            return response()->json(['success' => false, 'message' => 'Nincs hozzárendelt raktár.'], 403);
        }

        $type = $request->input('type');
        $id = (int) $request->input('id');

        if ($type === 'delivery_note') {
            $header = DB::table('delivery_note as dn')
                ->leftJoin('warehouses as wf', 'dn.warehouse_from', '=', 'wf.warehouse_id')
                ->leftJoin('warehouses as wt', 'dn.warehouse_to', '=', 'wt.warehouse_id')
                ->where('dn.delivery_note_id', $id)
                ->select([
                    'dn.delivery_note_id as id',
                    'dn.delivery_note_number as document_number',
                    'dn.employee_name',
                    'dn.customer_name',
                    'dn.created_at',
                    DB::raw('COALESCE(wf.name, "") as warehouse_from_name'),
                    DB::raw('COALESCE(wt.name, "") as warehouse_to_name'),
                ])
                ->first();

            $items = DB::table('delivery_note_items as dni')
                ->leftJoin('inventory as i', 'dni.inventory_item_id', '=', 'i.inventory_id')
                ->where('dni.delivery_note_id', $id)
                ->whereNull('dni.accepted_at')
                ->whereIn('dni.warehouse_to', $employeeWarehouseIds)
                ->select([
                    'dni.note_items_id as item_id',
                    'i.inventory_id',
                    DB::raw('COALESCE(dni.product_name, "") as product_name'),
                    DB::raw('COALESCE(i.description, "") as description'),
                    DB::raw('COALESCE(dni.quantity, 0) as quantity'),
                    DB::raw('COALESCE(dni.weight, 0) as weight'),
                ])
                ->get();

            $typeText = 'Szállítólevél';
        } else {
            $header = DB::table('warehouse_transfer as wtr')
                ->leftJoin('warehouses as wf', 'wtr.warehouse_from', '=', 'wf.warehouse_id')
                ->leftJoin('warehouses as wt', 'wtr.warehouse_to', '=', 'wt.warehouse_id')
                ->where('wtr.warehouse_transfer_id', $id)
                ->select([
                    'wtr.warehouse_transfer_id as id',
                    'wtr.warehouse_transfer_number as document_number',
                    'wtr.employee_name',
                    DB::raw('"" as customer_name'),
                    'wtr.created_at',
                    DB::raw('COALESCE(wf.name, "") as warehouse_from_name'),
                    DB::raw('COALESCE(wt.name, "") as warehouse_to_name'),
                ])
                ->first();

            $items = DB::table('warehouse_transfer_items as wti')
                ->join('inventory as i', 'wti.inventory_item_id', '=', 'i.inventory_id')
                ->where('wti.warehouse_transfer_id', $id)
                ->whereNull('wti.accepted_at')
                ->whereIn('wti.warehouse_to', $employeeWarehouseIds)
                ->select([
                    'wti.transfer_items_id as item_id',
                    'i.inventory_id',
                    DB::raw('COALESCE(i.name, "") as product_name'),
                    DB::raw('COALESCE(i.description, "") as description'),
                    DB::raw('COALESCE(wti.quantity, 0) as quantity'),
                    DB::raw('COALESCE(i.weight, 0) as weight'),
                ])
                ->get();

            $typeText = 'Raktárközi';
        }

        if (!$header || $items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Nincs megjeleníthető adatlap ehhez a bizonylathoz.'], 404);
        }

        $html = '<div class="mb-3">'
            . '<div><strong>Bizonylat típusa:</strong> ' . e($typeText) . '</div>'
            . '<div><strong>Bizonylatszám:</strong> ' . e($header->document_number) . '</div>'
            . '<div><strong>Átadó:</strong> ' . e($header->employee_name ?? '-') . '</div>'
            . '<div><strong>Ügyfél:</strong> ' . e($header->customer_name ?? '-') . '</div>'
            . '<div><strong>Honnan:</strong> ' . e($header->warehouse_from_name ?? '-') . '</div>'
            . '<div><strong>Hova:</strong> ' . e($header->warehouse_to_name ?? '-') . '</div>'
            . '<div><strong>Dátum:</strong> ' . e($header->created_at ? Carbon::parse($header->created_at)->format('Y-m-d H:i') : '-') . '</div>'
            . '</div>';

        $html .= '<div class="table-responsive">'
            . '<table class="table table-sm table-striped table-bordered">'
            . '<thead><tr><th>Inventory ID</th><th>Megnevezés</th><th>Leírás</th><th>Db</th><th>Súly</th></tr></thead>'
            . '<tbody>';

        foreach ($items as $row) {
            $html .= '<tr>'
                . '<td>' . e($row->inventory_id ?? '-') . '</td>'
                . '<td>' . e($row->product_name ?? '-') . '</td>'
                . '<td>' . e($row->description ?? '-') . '</td>'
                . '<td>' . e($row->quantity ?? 0) . '</td>'
                . '<td>' . e($row->weight ?? 0) . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table></div>';

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function displayOwnList(Request $request)
    {
        $employeeWarehouseIds = auth()->user()->employee?->warehouses->pluck('warehouse_id')->toArray();
        $allWarehouses = Warehouse::all()->keyBy('warehouse_id');
        $items = DB::table('warehouse_transfer_items as wti')
            ->leftJoin('warehouse_transfer as wt', 'wti.warehouse_transfer_id', '=', 'wt.warehouse_transfer_id')
            ->join('inventory as i', 'wti.inventory_item_id', '=', 'i.inventory_id')
            ->join('products as p', 'i.product_id', '=', 'p.product_id')
            ->whereIn('wti.warehouse_to', $employeeWarehouseIds)
            ->where(function ($q) {
                $q->whereNull('wti.accepted_at')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('wti.accepted_at')->whereNull('wti.back_to_storage_at');
                  });
            })
            ->select([
                'wti.transfer_items_id as item_id',
                'p.name as product_name',
                'i.description',
                'wti.quantity',
                'wti.warehouse_from',
                'wti.warehouse_to',
                'wti.repair_price',
                'wti.accepted_at',
                'wti.back_to_storage_at',
                'wti.note as notes',
                'wt.warehouse_transfer_number',
                'wt.warehouse_transfer_id as id',
                DB::raw("'warehouse_transfer' as type")
            ])
            ->get();
        return view('purchase_tickets.own_list', ['accept_list' => $items, 'all_warehouse' => $allWarehouses]);
    }

    public function editPTI(Request $request)
    {
        $inventory = Inventory::findOrFail($request->pti_id);
        $tags = Tag::orderBy('name', 'ASC')->get();
        return view('purchase_tickets.edit_pti', ['pti' => $inventory, 'tags' => $tags]);
    }

    public function savePTIComment(Request $request)
    {
        Inventory::where('inventory_id', $request->pti_id)->update(['comment' => $request->pti_comment]);
    }

    public function receiveItem(Request $request)
    {
        // A régi purchase_ticket_item_user tábla már nem használatos.
        // Ha kell, itt implementálhatod az átvétel logikáját (pl. inventory státusz frissítése).
    }

    public function backItem(Request $request)
    {
        // A régi back_to_storage_at logika – most a warehouse_transfer_items táblában.
        DB::table('warehouse_transfer_items')
            ->where('transfer_items_id', $request->pti_id)
            ->update(['back_to_storage_at' => now()]);
    }

    public function acceptItem(Request $request)
    {
        DB::table('warehouse_transfer_items')
            ->where('transfer_items_id', $request->transfer_items_id)
            ->update(['accepted_at' => now()]);
        return response()->json(['data' => ['success' => 'Tétel sikeresen elfogadva!']]);
    }

    public function check_login(Request $request)
    {
        if (Auth::validate($request->only('email', 'password'))) {
            $user = User::where('email', $request->email)->first();
            return response()->json(['success' => true, 'employee' => $user->employee]);
        }
        return response()->json(['success' => false]);
    }
}