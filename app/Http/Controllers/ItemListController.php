<?php
namespace App\Http\Controllers;

use App\Enums\ItemListStatus;
use App\Models\Document;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Business;
use App\Models\BusinessAlias;
use App\Models\BusinessMarking;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Services\ItemList\ItemListImport;
use App\Models\ItemList\ItemList;
use App\Models\ItemListItem\ItemListItem;
use App\Models\ItemList\DataTransferObjects\ItemListItemData;
use App\Models\Warehouse;
use App\Models\StockEntry;
use App\Models\Currency;
use App\Models\Karat;
use App\Models\VatType;
use App\Category;
use App\Models\Category2;
use App\Models\ProductStatus;
use App\Models\ProductType;
use App\Models\ProductColor;
use App\Models\ProductSign;
use App\Models\UnitPriceType;
use App\Models\CustomerMarking;
use App\Enums\InventoryStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\ItemList\CreateItemListItem;
use App\Services\ItemList\ItemListReceiptPDF;
use Illuminate\Http\JsonResponse;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Illuminate\Http\UploadedFile;

class ItemListController extends Controller
{

    // Index - Show all items
    public function index()
    {
        $items = ItemList::with(['user:id,name', 'business:business_id,name'])
            ->orderByDesc('id')
            ->get();
        return view('item_lists.index', compact('items'));
    }

     // Create - Show creation form
    public function create()
    {
        return view('item_lists.create');
    }

    // Store - Save new item
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_list_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'is_unique' => 'sometimes|boolean|string',
                'description' => 'nullable|string',
                'factory_number' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'quantity' => 'nullable|integer|min:1',
                'oneprice' => 'nullable|numeric|min:0',
                'price' => 'nullable|numeric|min:0',
                'storage_id' => 'required|integer|exists:warehouses,warehouse_id',
                'business_id' => 'required|integer',
            ]);

            $itemList = ItemList::findOrFail($validated['item_list_id']);
            $itemList->incImportCount($request->get('factory_number'));

            $warehouse = Warehouse::find($validated['storage_id']);
            if (!$warehouse) {
                $warehouse = Business::find(auth()->user()->employee?->active_business_id)->warehouses->first();
            }
            if (!$warehouse) {
                return response()->json(['success' => false, 'message' => 'Warehouse not found'], 404);
            }

            // Prepare data for service
            $data = [
                'item_list_id' => (int)$validated['item_list_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? $validated['name'],
                'quantity' => isset($validated['quantity']) ? (int)$validated['quantity'] : 1,
                'oneprice' => isset($validated['oneprice']) ? (int)$validated['oneprice'] : null,
                'price' => isset($validated['price']) ? (int)$validated['price'] : null,
                'factory_number' => $validated['factory_number'] ?? null,
                'weight' => isset($validated['weight']) ? (float)$validated['weight'] : null,
                'storage_id' => $warehouse->warehouse_id,
                'is_unique' => filter_var($validated['is_unique'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];

            // Call the refactored service
            $result = CreateItemListItem::From($data);

            if ($result) {
                // Retrieve newly created inventory items
                $createdItems = Inventory::where('source_type', 'item_list')
                    ->where('source_header_id', $validated['item_list_id'])
                    ->where('name', $validated['name'])
                    ->latest()
                    ->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Item list item created successfully',
                    'data' => $createdItems
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create item list item'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Alternative method to handle is_unique properly
     */
    public function storeWithUniqueHandling(Request $request): JsonResponse
    {
        try {
            // Validate with original field names
            $validated = $request->validate([
                'item_list_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'is_unique' => 'sometimes|boolean',
                'description' => 'nullable|string',
                'factory_number' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'quantity' => 'nullable|integer|min:1',
                'oneprice' => 'nullable|numeric|min:0',
                'price' => 'nullable|numeric|min:0',
                'storage_id' => 'required|integer|exists:warehouses,warehouse_id',
                'business_id' => 'required|integer',
            ]);

            // Convert is_unique to boolean if it's a string
            if (isset($validated['is_unique'])) {
                if (is_string($validated['is_unique'])) {
                    $validated['is_unique'] = filter_var($validated['is_unique'], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $warehouse = Warehouse::where('warehouse_id', $validated['storage_id'])->first();
            
            if (!$warehouse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warehouse not found'
                ], 404);
            }

            // Prepare data for DTO with is_unique in comment
            $dtoData = [
                'item_list_id' => (int)$validated['item_list_id'],
                'product_id' => 0,
                'main_product_id' => 0,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? $validated['name'],
                'quantity' => (int)($validated['quantity'] ?? 1),
                'oneprice' => isset($validated['oneprice']) ? (int)$validated['oneprice'] : null,
                'price' => isset($validated['price']) ? (int)$validated['price'] : null,
                'factory_number' => $validated['factory_number'] ?? null,
                'weight' => isset($validated['weight']) ? (float)$validated['weight'] : null,
                'investigation' => false,
                'investigated_at' => null,
                'investigation_user_id' => null,
                'hallmark' => null,
                'repair' => null,
                'is_fake' => null,
                'warehouse_from' => null,
                'warehouse_to' => $warehouse->warehouse_id,
                // Store is_unique in comment for the service to read
                'comment' => $validated['is_unique'] ? 'unique_item' : 'non_unique_item',
                'inv_comment' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status' => 1,
            ];

            // Create DTO
            $data = ItemListItemData::fromArray($dtoData);

            // Create item list item using service
            $result = CreateItemListItem::From($data);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item list item created successfully',
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create item list item'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


    // Show - Display single item
    public function show(ItemList $itemList)
    {
        return view('item_lists.show', compact('itemList'));
    }

    // Edit - Show edit form
    public function subindex(ItemList $itemList)
    {
        $businesses = Business::where('status', 1)
            ->whereIn('business_id', Auth::user()->employee?->businesses?->pluck('business_id')->toArray() ?? [])
            ->orderBy('name', 'ASC')
            ->get();
        return view('item_lists.subindex', compact('itemList','businesses'));
    }

    // Edit - Show edit form
    public function edit(ItemList $itemList)
    {
        return view('item_lists.edit', compact('itemList'));
    }

    // Update - Process edit form
    public function update(Request $request, ItemList $itemList)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'tipus' => 'required|string',
            'status' => 'nullable|string',
            'store.store_id' => 'required|integer',
            'store.name' => 'required|string',
            'amount' => 'required|numeric',
            'creater.user_id' => 'required|integer',
            'creater.name' => 'required|string',
            'customer.customer_id' => 'required|integer',
            'customer.name' => 'required|string',
            // NEW: Import validation rules
            'import.status' => 'required|string',
            'import.rows' => 'required|array',
            'import.rows.*.status' => 'required|string',
            'import.rows.*.row1' => 'nullable|string',
            'import.rows.*.row2' => 'nullable|string',
            // Add validation for additional row fields
        ]);

        // Update with custom method
        $itemList->updateWithJson([
            'user_id' => $validated['user_id'],
            'tipus' => $validated['tipus'],
            'status' => $validated['status'] ?? 'active',
            'store' => $validated['store'],
            'amount' => $validated['amount'],
            'creater' => $validated['creater'],
            'customer' => $validated['customer'],
            // NEW: Import data
            'import' => [
                'status' => $validated['import']['status'],
                'rows' => $validated['import']['rows']
            ]
        ]);

        return redirect()->route('item-lists.show', $itemList);
    }

    // Delete - Remove item
    public function destroy(ItemList $itemList)
    {
        $itemList->softDelete();
        return redirect()->route('item-lists.index');
    }

    public function import(Request $request)
    {
        $importSource = strtolower((string) $request->input('import_source', ''));

        if ($importSource === 'json') {
            return $this->importJson($request);
        }

        if ($importSource === 'pdf') {
            return $this->importPdf($request);
        }

        if ($request->hasFile('file')) {
            $extension = strtolower((string) $request->file('file')->getClientOriginalExtension());

            if ($extension === 'json') {
                return $this->importJson($request);
            }

            if ($extension === 'pdf') {
                return $this->importPdf($request);
            }
        }

        try {
            $importer = new ItemListImport();
            $itemList = $importer->import($request);
            return redirect()->route('item_list.index', $itemList)
                ->with('success', __('File imported successfully!'));
                
        } catch (\Exception $e) {
            Log::error('Import failed: '.$e->getMessage());
            return back()->with('error', __('Import failed: '.$e->getMessage()));
        }
    }

    public function importJson(Request $request)
    {
        $request->validate([
            'file' => 'nullable|file|mimes:json|max:20480',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:json|max:20480',
        ]);

        $user = Auth::user();
        $employee = $user?->employee;

        $businessId = $employee?->active_business_id;
        if (!$businessId) {
            return back()->with('error', 'A felhasznalohoz nincs aktiv uzlet hozzarendelve (employee.active_business_id).');
        }

        $business = Business::find($businessId);
        $storageId = $business?->warehouses()->value('warehouse_id');

        if (!$storageId) {
            return back()->with('error', 'Az aktiv uzlethez nincs raktar hozzarendelve.');
        }

        $jsonFiles = [];
        if ($request->hasFile('file')) {
            $jsonFiles[] = $request->file('file');
        }
        if ($request->hasFile('files')) {
            $jsonFiles = array_merge($jsonFiles, $request->file('files'));
        }

        if (count($jsonFiles) === 0) {
            return back()->with('error', 'Nem erkezett feldolgozhato JSON fajl.');
        }

        $processed = 0;
        $failed = [];

        foreach ($jsonFiles as $jsonFile) {
            $result = $this->processJson($jsonFile, $user->id, (int) $businessId, (int) $storageId);

            if ($result['success']) {
                $processed++;
            } else {
                $failed[] = ($jsonFile->getClientOriginalName() ?: 'ismeretlen.json') . ' - ' . $result['message'];
            }
        }

        if ($processed === 0) {
            return back()->with('error', 'Minden JSON feldolgozasa sikertelen: ' . implode(' | ', $failed));
        }

        if (count($failed) > 0) {
            return redirect()->route('item_list.index')
                ->with('success', $processed . ' JSON sikeresen feldolgozva, ' . count($failed) . ' sikertelen.');
        }

        return redirect()->route('item_list.index')->with('success', $processed . ' JSON sikeresen feldolgozva.');
    }

    public function importPdf(Request $request)
    {
        $validated = $request->validate([
            'file' => 'nullable|file|mimes:pdf|max:20480',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:pdf|max:20480',
        ]);

        $user = Auth::user();
        $employee = $user?->employee;

        $businessId = $employee?->active_business_id;
        if (!$businessId) {
            return back()->with('error', 'A felhasznalohoz nincs aktiv uzlet hozzarendelve (employee.active_business_id).');
        }

        $business = Business::find($businessId);
        $storageId = $business?->warehouses()->value('warehouse_id');

        if (!$storageId) {
            return back()->with('error', 'Az aktiv uzlethez nincs raktar hozzarendelve.');
        }

        $pdfFiles = [];
        if ($request->hasFile('file')) {
            $pdfFiles[] = $request->file('file');
        }
        if ($request->hasFile('files')) {
            $pdfFiles = array_merge($pdfFiles, $request->file('files'));
        }

        if (count($pdfFiles) === 0) {
            return back()->with('error', 'Nem erkezett feldolgozhato PDF fajl.');
        }

        $processed = 0;
        $failed = [];

        foreach ($pdfFiles as $index => $pdf) {
            $result = $this->processPdf($pdf, $user->id, (int) $businessId, (int) $storageId);

            if ($result['success']) {
                $processed++;
            } else {
                $failed[] = ($pdf->getClientOriginalName() ?: 'ismeretlen.pdf') . ' - ' . $result['message'];
            }

            // Free Tier vedelme: ket keres kozott 5 masodperc varakozas.
            if ($index < count($pdfFiles) - 1) {
                sleep(5);
            }
        }

        if ($processed === 0) {
            return back()->with('error', 'Minden PDF feldolgozasa sikertelen: ' . implode(' | ', $failed));
        }

        if (count($failed) > 0) {
            return redirect()->route('item_list.index')
                ->with('success', $processed . ' PDF sikeresen feldolgozva, ' . count($failed) . ' sikertelen.');
        }

        return redirect()->route('item_list.index')->with('success', $processed . ' PDF sikeresen feldolgozva.');
    }

    public function setStatus(Request $request){
        $itemList = ItemList::find($request->get('item_list_id'));
        if ($itemList) {
            $itemList->update(['status' => $request->get('status')]);
        }

        return response()->json(['success' => true], 200);
    }

    public function setItemStatus(Request $request){
        $itemList = ItemList::find($request->get('item_list_id'));
        if (!$itemList) {
            return response()->json(['success' => false, 'message' => 'Item list not found'], 404);
        }

        $newStatus = (int) $request->get('status', 1);
        $targetSor = (string) $request->get('item_sor', '');
        $targetRow1 = (string) $request->get('item_row1', '');

        $json = $itemList->json ?? [];
        $updated = false;

        if (isset($json['tetelek']) && is_array($json['tetelek'])) {
            foreach ($json['tetelek'] as &$tetel) {
                if (!is_array($tetel)) {
                    continue;
                }

                $matchBySor = isset($tetel['sor']) && (string) $tetel['sor'] === $targetSor;
                $matchByTicket = isset($tetel['zalogjegy_szama']) && (string) $tetel['zalogjegy_szama'] === $targetRow1;

                if ($matchBySor || $matchByTicket) {
                    $tetel['status'] = $newStatus;
                    $updated = true;
                    break;
                }
            }
            unset($tetel);
        }

        if (!$updated) {
            $import = $itemList->import;
            $rows = $import['rows'] ?? [];

            foreach ($rows as &$row) {
                if (($row['row1'] ?? null) === $targetRow1) {
                    $row['status'] = $newStatus;
                    $updated = true;
                    break;
                }
            }
            unset($row);

            if ($updated) {
                $itemList->import = [
                    'rows' => $rows
                ];
                $itemList->save();
                return response()->json(['success' => true], 200);
            }
        }

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Row not found'], 404);
        }

        $itemList->json = $json;
        $itemList->save();

        return response()->json(['success' => true], 200);
    }

    public function showTetel(ItemList $itemList, int $sor)
    {
        $rows = $itemList->json['tetelek'] ?? [];
        $rowIndex = null;

        foreach ($rows as $index => $row) {
            if ((int) ($row['sor'] ?? -1) === $sor) {
                $rowIndex = $index;
                break;
            }
        }

        if ($rowIndex === null) {
            abort(404, 'Tétel nem található.');
        }

        $item = $rows[$rowIndex];

        return view('item_lists.item_row_show', compact('itemList', 'item'));
    }

    public function editTetel(ItemList $itemList, int $sor)
    {
        $rows = $itemList->json['tetelek'] ?? [];
        $rowIndex = null;

        foreach ($rows as $index => $row) {
            if ((int) ($row['sor'] ?? -1) === $sor) {
                $rowIndex = $index;
                break;
            }
        }

        if ($rowIndex === null) {
            abort(404, 'Tétel nem található.');
        }

        $item = $rows[$rowIndex];
        $itemJson = json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return view('item_lists.item_row_edit', compact('itemList', 'item', 'itemJson'));
    }

    /**
     * Egy kényszerlista tétel átemelése az ékszer vásárlás (stock_entry) rendszerbe.
     * A header adatokból létrehoz egy új stock_entry-t és egy hozzá tartozó inventory tételt,
     * majd a meglévő ékszer vásárlás szerkesztő oldalra irányít.
     */
    public function transferTetel(ItemList $itemList, int $sor)
    {
        $rows = $itemList->json['tetelek'] ?? [];
        $rowIndex = null;

        foreach ($rows as $index => $row) {
            if ((int) ($row['sor'] ?? -1) === $sor) {
                $rowIndex = $index;
                break;
            }
        }

        if ($rowIndex === null) {
            abort(404, 'Tétel nem található.');
        }

        $tetel = $rows[$rowIndex];

        $customer = $this->resolveTransferCustomer($tetel['ugyfel'] ?? []);

        $header = $itemList->json['header'] ?? [];
        $businessName = $header['atvevo_bolt']['nev'] ?? null;
        $businessAlias = $businessName
            ? BusinessAlias::with('business', 'warehouse')->where('name', $businessName)->where('aktiv', 1)->first()
            : null;

        if ($businessAlias) {
            $business = $businessAlias->business;
            $storage = $businessAlias->warehouse ?? $business?->warehouses()->orderBy('warehouse_id')->first();
        } else {
            $business = $businessName ? Business::where('name', $businessName)->first() : null;
            $storage = $business?->warehouses()->orderBy('warehouse_id')->first();
        }

        $karatValue = $tetel['karat'] ?? $tetel['karat_finomsag'] ?? null;
        $karat = $karatValue ? Karat::where('name', $karatValue)->first() : null;

        $vatType = VatType::where('value', 27)->first();
        $currency = Currency::where('name', 'Forint')->first() ?? Currency::where('default', 1)->first();

        $beszerzesiAr = $tetel['atveteli_ar'] ?? null;

        $defaultBusinessMarking = null;
        if ($business) {
            $defaultBusinessMarking = BusinessMarking::with('productStatus.vatType')
                ->where('business_id', $business->business_id)
                ->where('status', 1)
                ->orderByDesc('default')
                ->first();
        }

        $markingPrice = optional($defaultBusinessMarking?->productStatus)->price;
        $markingVatTypeId = optional($defaultBusinessMarking?->productStatus)->vat_type_id;

        $atadasNapjaRaw = $header['atadas_napja'] ?? $header['atadas_datuma'] ?? null;
        $fulfillmentDate = now()->toDateString();
        if ($atadasNapjaRaw) {
            try {
                $cleanedDate = trim(str_replace(['.', '/', ' '], ['-', '-', ''], rtrim($atadasNapjaRaw, '. ')));
                $fulfillmentDate = \Carbon\Carbon::parse($cleanedDate)->toDateString();
            } catch (\Throwable $e) {
                $fulfillmentDate = now()->toDateString();
            }
        }

        $bizonylatSzama1 = $header['bizonylat_szama_1'] ?? $header['bizonylatszam_1'] ?? $header['bizonylatszam'] ?? $header['bizonylat_szama'] ?? '';
        $zalogjegySzama = $tetel['zalogjegy_szama'] ?? '';

        // Ha már korábban megnyitottuk ezt a tételt, a meglévő (még üres) bizonylatot használjuk újra, hogy ne halmozzódjanak a fejek.
        $stock_entry = !empty($tetel['stock_entry_id']) ? StockEntry::find($tetel['stock_entry_id']) : null;

        if (!$stock_entry) {
            $stock_entry = StockEntry::create([
                // sorszám a tetel zálogjegy száma, státusz 0 (Nyitott)
                'ticket_number' => $zalogjegySzama ?: '--empty_row--',
                'status' => 0,
                'business_id' => $business?->business_id,
                'storage_id' => $storage?->warehouse_id,
                'customer_id' => $customer?->customer_id,
                'currency_id' => $currency?->currency_id,
                'currency_value' => 1.00,
                'fulfillment_date' => $fulfillmentDate,
                'contract_number' => $bizonylatSzama1 ?: null,
                'invoice_number' => $zalogjegySzama ?: null,
                'has_contract' => 1,
                'header_data' => [
                    'customer_marking_id' => $defaultBusinessMarking?->id,
                    'category_id' => null,
                    'beszerzesi_ar' => $beszerzesiAr,
                    'eladasi_ar' => $markingPrice,
                    'vat_type_id' => $markingVatTypeId ?? $vatType?->vat_type_id,
                    'hallmarks' => 0,
                    'karat_id' => $karat?->karat_id,
                    'label_type' => null,
                ],
            ]);

            $tetel['stock_entry_id'] = $stock_entry->stock_entry_id;
            $rows[$rowIndex] = $tetel;

            $json = $itemList->json ?? [];
            $json['tetelek'] = $rows;
            $itemList->json = $json;
            $itemList->save();
        } else {
            if ($customer && (int) $stock_entry->customer_id !== (int) $customer->customer_id) {
                $stock_entry->customer_id = $customer->customer_id;
            }
            if ($bizonylatSzama1 && empty($stock_entry->contract_number)) {
                $stock_entry->contract_number = $bizonylatSzama1;
            }
            if ($zalogjegySzama && empty($stock_entry->invoice_number)) {
                $stock_entry->invoice_number = $zalogjegySzama;
            }
            if ($fulfillmentDate && $stock_entry->fulfillment_date !== $fulfillmentDate) {
                $stock_entry->fulfillment_date = $fulfillmentDate;
            }
            if ($business && !$stock_entry->printed_at) {
                $stock_entry->business_id = $business->business_id;
                if ($storage) {
                    $stock_entry->storage_id = $storage->warehouse_id;
                }
            }
            $stock_entry->save();
        }

        // Az "új tétel" űrlap alapértelmezett értékei - még nem kerül semmi az adatbázisba, csak a felhasználó jóváhagyása után (save_new_item_btn).
        $prefillWeight = (float) ($tetel['netto_suly'] ?? ($tetel['brutto_suly'] ?? 0));
        $prefillOnePrice = ($prefillWeight > 0 && (float) $beszerzesiAr > 0)
            ? (float) $beszerzesiAr / $prefillWeight
            : ($tetel['atlag_beszerzesi_grammar'] ?? $beszerzesiAr);
        if (is_numeric($prefillOnePrice)) {
            $prefillOnePrice = round((float) $prefillOnePrice);
        }

        $prefill = [
            'item_list_id' => $itemList->id,
            'item_list_sor' => $sor,
            //'description' => $tetel['targyleiras'] ?? '',
            'weight' => $tetel['netto_suly'] ?? ($tetel['brutto_suly'] ?? ''),
            'quantity' => 1,
            'oneprice' => $prefillOnePrice,
            'price' => $beszerzesiAr,
            'factory_number' => '',
        ];

        return $this->renderTransferView($stock_entry, $itemList, $tetel, $sor, $prefill);
    }

    private function resolveTransferCustomer(array $customerData): ?Customer
    {
        $name = trim((string) ($customerData['nev'] ?? ''));
        if ($name === '') {
            return null;
        }

        $customer = Customer::where('name', $name)->first();
        if (!$customer) {
            $customer = Customer::create([
                'name' => $name,
                'status' => 1,
            ]);
        }

        $address = $customer->addresses()->where('address_type', 'Lakcím')->first();
        if (!$address) {
            CustomerAddress::create([
                'customer_id' => $customer->customer_id,
                'address_type' => 'Lakcím',
                'zip' => $customerData['iranyitoszam'] ?? null,
                'city' => $customerData['telepules'] ?? null,
                'address' => $customerData['cim'] ?? null,
            ]);
        }

        return $customer;
    }

    /**
     * Kényszerlista tétel átemelésének dedikált nézete.
     */
    private function renderTransferView(StockEntry $stock_entry, ItemList $itemList, array $tetel, int $sor, array $prefill = [])
    {
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();
        $businesses = Business::where('status', 1)
            ->whereIn('business_id', auth()->user()->employee?->businesses?->pluck('business_id')->toArray() ?? [])
            ->orderBy('name', 'ASC')
            ->get();

        if ($businesses->isEmpty()) {
            $businesses = Business::where('status', 1)->orderBy('name', 'ASC')->get();
        }

        $categoryController = app(CategoryController::class);
        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $categories = $categories->map(function ($category) use ($categoryController) {
            $category->depth = 0;
            return $categoryController->setCategoryDepth($category);
        });

        $storageId = $stock_entry->storage_id;
        $warehouse = Warehouse::find($storageId);

        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();
        $business_markings = collect();
        if (!empty($stock_entry->business_id)) {
            $business_markings = BusinessMarking::with('productStatus.vatType')
                ->where('business_id', $stock_entry->business_id)
                ->where('status', 1)
                ->orderByDesc('default')
                ->orderBy('name', 'ASC')
                ->get();
        }

        $inventoryItems = Inventory::where('source_type', 'stock_entry')
            ->where('source_header_id', $stock_entry->stock_entry_id)
            ->get();

        $inventoryItems = $inventoryItems->map(function ($item) {
            $itemData = $item->getAttribute('item_data');
            if (is_string($itemData)) $itemData = json_decode($itemData, true);
            if (!is_array($itemData)) $itemData = [];

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

        return view('item_lists.transfer', [
            'itemList' => $itemList,
            'sor' => $sor,
            'tetel' => $tetel,
            'stock_entry' => $stock_entry,
            'customers' => $customers,
            'businesses' => $businesses,
            'categories' => $categories,
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
            'business_markings' => $business_markings,
            'inventoryItems' => $inventoryItems,
            'groupedInventoryItems' => $groupedInventoryItems,
            'prefill' => $prefill,
            'show' => false,
        ]);
    }

    public function updateTetel(Request $request, ItemList $itemList, int $sor)
    {
        $validated = $request->validate([
            'item_json' => 'required|string',
        ]);

        $rows = $itemList->json['tetelek'] ?? [];
        $rowIndex = null;

        foreach ($rows as $index => $row) {
            if ((int) ($row['sor'] ?? -1) === $sor) {
                $rowIndex = $index;
                break;
            }
        }

        if ($rowIndex === null) {
            return back()->withErrors(['item_json' => 'A tétel nem található.'])->withInput();
        }

        $decoded = json_decode((string) $validated['item_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return back()->withErrors(['item_json' => 'Érvénytelen JSON formátum.'])->withInput();
        }

        $original = $rows[$rowIndex];

        // A sor azonosító csak olvasható, mentéskor mindig az eredeti marad.
        $decoded['sor'] = $original['sor'] ?? $sor;

        $rows[$rowIndex] = $decoded;

        $json = $itemList->json ?? [];
        $json['tetelek'] = $rows;
        $itemList->json = $json;
        $itemList->save();

        return redirect()
            ->route('item_list.row.show', ['itemList' => $itemList->id, 'sor' => $sor])
            ->with('success', 'A tétel sikeresen mentve.');
    }

    public function getItemListRows(Request $request)
    {
        try {
            // Get the item list (adjust this based on your application logic)
            $itemListId = $request->input('item_list_id');
            $itemList = ItemList::find($itemListId);
            
            if (!$itemList) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item list not found'
                ]);
            }
            
            // Get the rows from the JSON data
            $rows = $itemList->json['import']['rows'] ?? [];
            
            return response()->json([
                'success' => true,
                'rows' => $rows
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching rows: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all rows from all ItemList models
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllItemListRows(Request $request)
    {
        $itemLists = new ItemList();
        return $itemLists->getAllItemListRows();
    }
    
    /**
     * Alternative: Get paginated results for large datasets
     */
    public function getPaginatedItemListRows(Request $request)
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 50);
            
            // Get all item lists with their JSON data
            $itemLists = ItemList::all();
            
            // Initialize an array to hold all rows
            $allRows = [];
            
            // Extract rows from each item list
            foreach ($itemLists as $itemList) {
                if (isset($itemList->json['import']['rows']) && is_array($itemList->json['import']['rows'])) {
                    $rowsWithSource = array_map(function($row) use ($itemList) {
                        $row['source_item_list_id'] = $itemList->id;
                        return $row;
                    }, $itemList->json['import']['rows']);
                    
                    $allRows = array_merge($allRows, $rowsWithSource);
                }
            }
            
            // Implement manual pagination
            $offset = ($page - 1) * $perPage;
            $paginatedRows = array_slice($allRows, $offset, $perPage);
            $total = count($allRows);
            $lastPage = ceil($total / $perPage);
            
            return response()->json([
                'success' => true,
                'rows' => $paginatedRows,
                'pagination' => [
                    'current_page' => (int)$page,
                    'per_page' => (int)$perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching paginated item list rows: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching rows: ' . $e->getMessage(),
                'rows' => []
            ], 500);
        }
    }

    public function printReceipt($id)
    {
        $itemList = ItemList::with(['items'])
            ->findOrFail($id);
        
        $pdfGenerator = new ItemListReceiptPDF($itemList);
        $pdf = $pdfGenerator->generate();
        
        $fileName = 'atveteli_jegyzokonyv_' . $itemList->id . '_' . date('Ymd') . '.pdf';
        
        return $pdf->stream($fileName);
    }

    private function buildGeminiModelCandidates(string $configuredModel): array
    {
        $configuredModel = $this->normalizeGeminiModelName($configuredModel);

        $candidates = [
            $configuredModel,
            'models/gemini-2.0-flash',
            'models/gemini-2.0-flash-001',
            'models/gemini-2.0-flash-lite',
            'models/gemini-2.0-flash-lite-001',
            'models/gemini-flash-latest',
        ];

        return array_values(array_unique($candidates));
    }

    private function normalizeGeminiModelName(string $model): string
    {
        $trimmedModel = trim($model);
        if ($trimmedModel === '') {
            return 'models/gemini-2.0-flash';
        }

        return str_starts_with($trimmedModel, 'models/') ? $trimmedModel : 'models/' . $trimmedModel;
    }

    private function normalizeImportedPayload(array $data): array
    {
        if (!isset($data['tetelek']) || !is_array($data['tetelek'])) {
            return $data;
        }

        $karatAverageMap = $this->buildFooterKaratAverageMap($data['footer'] ?? []);
        $data = $this->applySharedTicketPriceAllocation($data, $karatAverageMap);
        $ticketCounts = $this->buildTicketCounts($data['tetelek']);

        foreach ($data['tetelek'] as $index => $tetel) {
            if (!is_array($tetel)) {
                continue;
            }

            if (isset($tetel['szamolt_tobbkaratos_arak']['kivetel_tipus']) && $tetel['szamolt_tobbkaratos_arak']['kivetel_tipus'] === 'ticket_megosztas') {
                continue;
            }

            $ticketNumber = trim((string) ($tetel['zalogjegy_szama'] ?? ''));
            $karatok = $tetel['karatok'] ?? [];

            if (!is_array($karatok) || count($karatok) === 0) {
                continue;
            }

            $isDuplicateTicket = $ticketNumber !== '' && ($ticketCounts[$ticketNumber] ?? 0) > 1;
            $hasMultipleKarat = count($karatok) > 1;

            if (!$isDuplicateTicket && !$hasMultipleKarat) {
                continue;
            }

            $breakdown = $this->calculateKaratBreakdown($tetel, $karatAverageMap);
            if ($breakdown === null) {
                continue;
            }

            $tetel['szamolt_tobbkaratos_arak'] = $breakdown;
            $data['tetelek'][$index] = $tetel;
        }

        $data = $this->applyAveragePurchasePricePerGram($data);

        return $data;
    }

    private function applyAveragePurchasePricePerGram(array $data): array
    {
        foreach ($data['tetelek'] as $index => $tetel) {
            if (!is_array($tetel)) {
                continue;
            }

            $netWeight = $this->toFloat($tetel['netto_suly'] ?? null);
            $atveteliAr = $this->toFloat($tetel['atveteli_ar'] ?? null);

            if ($netWeight === null || $netWeight <= 0 || $atveteliAr === null || $atveteliAr <= 0) {
                continue;
            }

            $averagePerGram = round($atveteliAr / $netWeight, 2);
            $darab = (int) ($tetel['darab'] ?? 1);
            if ($darab < 1) {
                $darab = 1;
            }

            $tetel['atlag_beszerzesi_grammar'] = $averagePerGram;
            $tetel['elszamolasi_alap'] = [
                'atveteli_ar_osszesen' => (int) round($atveteliAr),
                'netto_suly_osszesen' => $netWeight,
                'atlag_beszerzesi_grammar' => $averagePerGram,
                'darab' => $darab,
                'egyedi_suly_felosztas_szukseges' => $darab > 1,
            ];

            $data['tetelek'][$index] = $tetel;
        }

        return $data;
    }

    private function applySharedTicketPriceAllocation(array $data, array $karatAverageMap): array
    {
        $grouped = [];

        foreach ($data['tetelek'] as $index => $tetel) {
            if (!is_array($tetel)) {
                continue;
            }

            $ticket = trim((string) ($tetel['zalogjegy_szama'] ?? ''));
            if ($ticket === '') {
                continue;
            }

            $grouped[$ticket][] = $index;
        }

        foreach ($grouped as $ticket => $indexes) {
            if (count($indexes) < 2) {
                continue;
            }

            $totals = [];
            foreach ($indexes as $idx) {
                $value = $this->toFloat($data['tetelek'][$idx]['atveteli_ar'] ?? null);
                if ($value !== null) {
                    $totals[] = (int) round($value);
                }
            }

            $uniqueTotals = array_values(array_unique($totals));
            if (count($uniqueTotals) !== 1) {
                continue;
            }

            $sharedTotal = $uniqueTotals[0];
            if ($sharedTotal <= 0) {
                continue;
            }

            $parts = [];
            foreach ($indexes as $idx) {
                $tetel = $data['tetelek'][$idx];
                [$karat, $netWeight] = $this->extractTetelKaratAndWeight($tetel);

                if ($karat === null || $netWeight === null || $netWeight <= 0) {
                    continue;
                }

                $karatKey = $this->normalizeKaratKey($karat);
                $avgPerGram = $karatKey !== null ? ($karatAverageMap[$karatKey] ?? null) : null;

                $parts[] = [
                    'idx' => $idx,
                    'karat' => $karat,
                    'netto_suly' => $netWeight,
                    'netto_atveteli_atlagar' => $avgPerGram,
                    'atveteli_ar' => null,
                    'forras' => $avgPerGram !== null ? 'footer' : 'ismeretlen',
                ];
            }

            if (count($parts) < 2) {
                continue;
            }

            $primaryIndex = 0;
            $maxWeight = -1;
            foreach ($parts as $i => $part) {
                if ($part['netto_suly'] > $maxWeight) {
                    $maxWeight = $part['netto_suly'];
                    $primaryIndex = $i;
                }
            }

            $knownOthers = 0;
            foreach ($parts as $i => &$part) {
                if ($i === $primaryIndex) {
                    continue;
                }

                if ($part['netto_atveteli_atlagar'] !== null) {
                    $part['atveteli_ar'] = (int) round($part['netto_atveteli_atlagar'] * $part['netto_suly']);
                    $knownOthers += $part['atveteli_ar'];
                }
            }
            unset($part);

            $primaryAmount = $sharedTotal - $knownOthers;
            if ($primaryAmount < 0) {
                $primaryAmount = 0;
            }

            $parts[$primaryIndex]['atveteli_ar'] = (int) round($primaryAmount);
            $parts[$primaryIndex]['forras'] = 'maradek';
            if ($parts[$primaryIndex]['netto_suly'] > 0) {
                $parts[$primaryIndex]['netto_atveteli_atlagar'] = round(
                    $parts[$primaryIndex]['atveteli_ar'] / $parts[$primaryIndex]['netto_suly'],
                    2
                );
            }

            $calculatedTotal = 0;
            foreach ($parts as $i => &$part) {
                if ($part['atveteli_ar'] === null && $part['netto_atveteli_atlagar'] !== null) {
                    $part['atveteli_ar'] = (int) round($part['netto_atveteli_atlagar'] * $part['netto_suly']);
                }

                if ($part['atveteli_ar'] !== null) {
                    $calculatedTotal += $part['atveteli_ar'];
                }

                $targetIdx = $part['idx'];
                $data['tetelek'][$targetIdx]['eredeti_atveteli_ar'] = $sharedTotal;
                $data['tetelek'][$targetIdx]['atveteli_ar'] = $part['atveteli_ar'];
                $data['tetelek'][$targetIdx]['netto_atveteli_atlagar'] = $part['netto_atveteli_atlagar'];
                $data['tetelek'][$targetIdx]['atlag_beszerzesi_grammar'] = $part['netto_atveteli_atlagar'];
                $data['tetelek'][$targetIdx]['szamolt_tobbkaratos_arak'] = [
                    'kivetel_alkalmazva' => true,
                    'kivetel_tipus' => 'ticket_megosztas',
                    'zalogjegy_szama' => $ticket,
                    'ticket_ossz_atveteli_ar' => $sharedTotal,
                    'szamolt_ticket_osszesen' => null,
                    'fo_karat' => $parts[$primaryIndex]['karat'] ?? null,
                    'fo_karat_netto_suly' => $parts[$primaryIndex]['netto_suly'] ?? null,
                    'fo_karat_atveteli_ar' => $parts[$primaryIndex]['atveteli_ar'] ?? null,
                    'fo_karat_atlag_beszerzesi_grammar' => $parts[$primaryIndex]['netto_atveteli_atlagar'] ?? null,
                    'tetel_felbontas' => [
                        'karat' => $part['karat'],
                        'netto_suly' => $part['netto_suly'],
                        'atveteli_ar' => $part['atveteli_ar'],
                        'netto_atveteli_atlagar' => $part['netto_atveteli_atlagar'],
                        'forras' => $part['forras'],
                    ],
                ];
            }
            unset($part);

            foreach ($parts as $part) {
                $data['tetelek'][$part['idx']]['szamolt_tobbkaratos_arak']['szamolt_ticket_osszesen'] = $calculatedTotal;
            }
        }

        return $data;
    }

    private function extractTetelKaratAndWeight(array $tetel): array
    {
        $rowKarat = $this->toFloat($tetel['karat'] ?? null);
        $rowNetWeight = $this->toFloat($tetel['netto_suly'] ?? null);

        if ($rowKarat !== null && $rowNetWeight !== null && $rowNetWeight > 0) {
            return [$rowKarat, $rowNetWeight];
        }

        $karatok = $tetel['karatok'] ?? [];
        if (!is_array($karatok) || count($karatok) === 0) {
            return [null, null];
        }

        $first = $karatok[0] ?? null;
        if (!is_array($first)) {
            return [null, null];
        }

        return [
            $this->toFloat($first['karat'] ?? null),
            $this->toFloat($first['netto_suly'] ?? null),
        ];
    }

    private function buildTicketCounts(array $tetelek): array
    {
        $counts = [];

        foreach ($tetelek as $tetel) {
            if (!is_array($tetel)) {
                continue;
            }

            $ticket = trim((string) ($tetel['zalogjegy_szama'] ?? ''));
            if ($ticket === '') {
                continue;
            }

            $counts[$ticket] = ($counts[$ticket] ?? 0) + 1;
        }

        return $counts;
    }

    private function buildFooterKaratAverageMap(array $footer): array
    {
        $map = [];
        $sections = [
            'arany_ekszer_allapotonkent_es_karatonkent',
            'kulonleges_arany_ekszer_allapotonkent',
        ];

        foreach ($sections as $sectionName) {
            $rows = $footer[$sectionName] ?? [];
            if (!is_array($rows)) {
                continue;
            }

            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }

                $karatKey = $this->normalizeKaratKey($row['karat_finomsag'] ?? null);
                $avgPrice = $this->toFloat($row['netto_atveteli_atlagar'] ?? null);

                if ($karatKey === null || $avgPrice === null) {
                    continue;
                }

                $map[$karatKey] = $avgPrice;
            }
        }

        return $map;
    }

    private function calculateKaratBreakdown(array $tetel, array $karatAverageMap): ?array
    {
        $karatok = $tetel['karatok'] ?? [];
        if (!is_array($karatok) || count($karatok) === 0) {
            return null;
        }

        $parts = [];
        foreach ($karatok as $idx => $karatRow) {
            if (!is_array($karatRow)) {
                continue;
            }

            $netWeight = $this->toFloat($karatRow['netto_suly'] ?? null);
            if ($netWeight === null || $netWeight <= 0) {
                continue;
            }

            $karatKey = $this->normalizeKaratKey($karatRow['karat'] ?? null);
            $avgPerGram = $karatKey !== null ? ($karatAverageMap[$karatKey] ?? null) : null;

            $parts[] = [
                'index' => $idx,
                'karat' => $this->toFloat($karatRow['karat'] ?? null),
                'karat_key' => $karatKey,
                'netto_suly' => $netWeight,
                'brutto_suly' => $this->toFloat($karatRow['brutto_suly'] ?? null),
                'netto_atveteli_atlagar' => $avgPerGram,
                'atveteli_ar' => null,
                'forras' => $avgPerGram !== null ? 'footer' : 'ismeretlen',
            ];
        }

        if (count($parts) === 0) {
            return null;
        }

        $primaryPartIndex = 0;
        $maxWeight = -1;
        foreach ($parts as $i => $part) {
            if ($part['netto_suly'] > $maxWeight) {
                $maxWeight = $part['netto_suly'];
                $primaryPartIndex = $i;
            }
        }

        $totalAtveteliAr = $this->toFloat($tetel['atveteli_ar'] ?? null);

        $knownOtherAmount = 0;
        foreach ($parts as $i => &$part) {
            if ($i === $primaryPartIndex) {
                continue;
            }

            if ($part['netto_atveteli_atlagar'] !== null) {
                $part['atveteli_ar'] = (int) round($part['netto_atveteli_atlagar'] * $part['netto_suly']);
                $knownOtherAmount += $part['atveteli_ar'];
            }
        }
        unset($part);

        if ($totalAtveteliAr !== null) {
            $primaryAmount = (int) round($totalAtveteliAr - $knownOtherAmount);
            if ($primaryAmount < 0) {
                $primaryAmount = 0;
            }

            $parts[$primaryPartIndex]['atveteli_ar'] = $primaryAmount;
            $parts[$primaryPartIndex]['forras'] = 'maradek';

            if ($parts[$primaryPartIndex]['netto_suly'] > 0) {
                $parts[$primaryPartIndex]['netto_atveteli_atlagar'] = round(
                    $primaryAmount / $parts[$primaryPartIndex]['netto_suly'],
                    2
                );
            }
        }

        $calculatedTotal = 0;
        foreach ($parts as &$part) {
            if ($part['atveteli_ar'] === null && $part['netto_atveteli_atlagar'] !== null) {
                $part['atveteli_ar'] = (int) round($part['netto_atveteli_atlagar'] * $part['netto_suly']);
            }

            if ($part['atveteli_ar'] !== null) {
                $calculatedTotal += (int) $part['atveteli_ar'];
            }

            unset($part['index']);
            unset($part['karat_key']);
        }
        unset($part);

        return [
            'kivetel_alkalmazva' => true,
            'zalogjegy_szama' => $tetel['zalogjegy_szama'] ?? null,
            'eredeti_atveteli_ar' => $totalAtveteliAr !== null ? (int) round($totalAtveteliAr) : null,
            'szamolt_atveteli_ar_osszesen' => $calculatedTotal,
            'karat_felbontas' => $parts,
            'fo_karat' => $parts[$primaryPartIndex]['karat'] ?? null,
            'fo_karat_netto_suly' => $parts[$primaryPartIndex]['netto_suly'] ?? null,
            'fo_karat_atveteli_ar' => $parts[$primaryPartIndex]['atveteli_ar'] ?? null,
            'fo_karat_atlag_beszerzesi_grammar' => $parts[$primaryPartIndex]['netto_atveteli_atlagar'] ?? null,
        ];
    }

    private function normalizeKaratKey($value): ?string
    {
        $numeric = $this->toFloat($value);
        if ($numeric === null) {
            return null;
        }

        return number_format($numeric, 2, '.', '');
    }

    private function toFloat($value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $normalized = str_replace(["\xc2\xa0", ' '], '', trim($value));
        if ($normalized === '') {
            return null;
        }

        $hasDot = strpos($normalized, '.') !== false;
        $hasComma = strpos($normalized, ',') !== false;

        if ($hasDot && $hasComma) {
            if (strrpos($normalized, ',') > strrpos($normalized, '.')) {
                $normalized = str_replace('.', '', $normalized);
                $normalized = str_replace(',', '.', $normalized);
            } else {
                $normalized = str_replace(',', '', $normalized);
            }
        } elseif ($hasComma) {
            $normalized = str_replace(',', '.', $normalized);
        }

        $normalized = preg_replace('/[^0-9.\-]/', '', $normalized);
        if ($normalized === '' || $normalized === '-' || $normalized === '.') {
            return null;
        }

        return is_numeric($normalized) ? (float) $normalized : null;
    }

    private function processPdf(UploadedFile $pdf, int $userId, int $businessId, int $storageId): array
    {
        $itemList = ItemList::create([
            'user_id' => $userId,
            'tipus' => 1,
            'status' => ItemListStatus::PROCESSING->value,
            'business_id' => $businessId,
            'storage_id' => $storageId,
            'json' => [
                'source' => [
                    'type' => 'pdf',
                    'original_name' => $pdf->getClientOriginalName(),
                ],
            ],
        ]);

        $pdfPath = $pdf->store('item_list_docs/' . $itemList->id, 'document_storage');

        $document = Document::create([
            'resource_id' => $itemList->id,
            'document_path' => $pdfPath,
            'document_comment' => 'ItemList PDF import',
            'document_module' => 'item_list_doc',
            'document_type_id' => null,
        ]);

        try {
            $promptText = Storage::disk('public')->get('template/Readme.md');
            $fullPrompt = $promptText . "\n\nFontos: Kizarolag ervenyes JSON kodot adj vissza valaszul, markdown blokkok nelkul!";

            $absolutePdfPath = public_path('document_storage/' . $pdfPath);
            $pdfContent = file_get_contents($absolutePdfPath);

            $response = null;
            $usedModel = null;
            $modelErrors = [];
            $modelCandidates = $this->buildGeminiModelCandidates((string) config('gemini.model', 'gemini-2.0-flash'));

            foreach ($modelCandidates as $candidateModel) {
                try {
                    $response = Gemini::generativeModel($candidateModel)->generateContent(
                        $fullPrompt,
                        new Blob(
                            mimeType: MimeType::APPLICATION_PDF,
                            data: base64_encode($pdfContent)
                        )
                    );
                    $usedModel = $candidateModel;
                    break;
                } catch (\Throwable $modelException) {
                    $modelErrors[] = $candidateModel . ': ' . $modelException->getMessage();
                }
            }

            if (!$response) {
                Log::error('ItemList PDF import failed for all Gemini model candidates', ['errors' => $modelErrors]);
                return ['success' => false, 'message' => 'Nincs hasznalhato Gemini modell vagy nincs kvota.'];
            }

            $rawJsonResponse = $response->text();
            $cleanJson = preg_replace('/^```json\s*|\s*```$/i', '', trim($rawJsonResponse));
            $data = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'message' => 'Ervenytelen JSON valasz erkezett a Geminitol.'];
            }

            if (!isset($data['header']) || !isset($data['tetelek']) || !is_array($data['tetelek']) || !isset($data['footer']) || !is_array($data['footer'])) {
                return ['success' => false, 'message' => 'A valasz nem tartalmazza a kotelezo header+tetelek+footer szerkezetet.'];
            }

            $data = $this->normalizeImportedPayload($data);

            $atvevoBoltNev = $data['header']['atvevo_bolt']['nev'] ?? null;
            if ($atvevoBoltNev) {
                $alias = BusinessAlias::with('warehouse')->where('name', $atvevoBoltNev)->where('aktiv', 1)->first();
                if ($alias) {
                    $itemList->business_id = $alias->business_id;
                    if ($alias->warehouse_id) {
                        $itemList->storage_id = $alias->warehouse_id;
                    }
                }
            }

            $jsonFileName = 'gemini-code-' . round(microtime(true) * 1000) . '.json';
            $jsonRelativePath = 'gemini/' . $jsonFileName;

            Storage::disk('local')->put(
                $jsonRelativePath,
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );

            $itemList->json = [
                'source' => [
                    'type' => 'pdf',
                    'original_name' => $pdf->getClientOriginalName(),
                    'pdf_document_id' => $document->document_id,
                    'pdf_path' => $pdfPath,
                    'gemini_json_file' => $jsonRelativePath,
                    'gemini_model' => $usedModel,
                ],
                'header' => $data['header'],
                'tetelek' => $data['tetelek'],
                'footer' => $data['footer'],
                'import' => [
                    'status' => 'completed',
                    'rows' => [],
                ],
            ];
            $itemList->status = ItemListStatus::PROCESSING->value;
            $itemList->save();

            return [
                'success' => true,
                'message' => 'ok',
                'item_list_id' => $itemList->id,
                'items' => count($data['tetelek']),
            ];
        } catch (\Throwable $e) {
            Log::error('ItemList PDF import failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function processJson(UploadedFile $jsonFile, int $userId, int $businessId, int $storageId): array
    {
        try {
            $rawJson = file_get_contents($jsonFile->getRealPath());
            $data = json_decode((string) $rawJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                return ['success' => false, 'message' => 'Ervenytelen JSON fajl.'];
            }

            if (!isset($data['header']) || !isset($data['tetelek']) || !is_array($data['tetelek']) || !isset($data['footer']) || !is_array($data['footer'])) {
                return ['success' => false, 'message' => 'A JSON nem tartalmazza a kotelezo header+tetelek+footer szerkezetet.'];
            }

            $data = $this->normalizeImportedPayload($data);

            $atvevoBoltNev = $data['header']['atvevo_bolt']['nev'] ?? null;
            if ($atvevoBoltNev) {
                $alias = BusinessAlias::with('warehouse')->where('name', $atvevoBoltNev)->where('aktiv', 1)->first();
                if ($alias) {
                    $businessId = $alias->business_id;
                    if ($alias->warehouse_id) {
                        $storageId = $alias->warehouse_id;
                    }
                }
            }

            $storedJsonPath = $jsonFile->store('gemini', 'local');

            $itemList = ItemList::create([
                'user_id' => $userId,
                'tipus' => 1,
                'status' => ItemListStatus::PROCESSING->value,
                'business_id' => $businessId,
                'storage_id' => $storageId,
                'json' => [
                    'source' => [
                        'type' => 'json',
                        'original_name' => $jsonFile->getClientOriginalName(),
                        'uploaded_json_file' => $storedJsonPath,
                    ],
                    'header' => $data['header'],
                    'tetelek' => $data['tetelek'],
                    'footer' => $data['footer'],
                    'import' => [
                        'status' => 'completed',
                        'rows' => [],
                    ],
                ],
            ]);

            return [
                'success' => true,
                'message' => 'ok',
                'item_list_id' => $itemList->id,
                'items' => count($data['tetelek']),
            ];
        } catch (\Throwable $e) {
            Log::error('ItemList JSON import failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
