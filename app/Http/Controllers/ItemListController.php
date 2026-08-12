<?php
namespace App\Http\Controllers;

use App\Enums\ItemListStatus;
use App\Models\Document;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Business;
use App\Services\ItemList\ItemListImport;
use App\Models\ItemList\ItemList;
use App\Models\ItemListItem\ItemListItem;
use App\Models\ItemList\DataTransferObjects\ItemListItemData;
use App\Models\Warehouse;
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
        $import = $itemList->import;
        $rows = $import['rows'];

        foreach ($rows as &$row) {
            if ($row['row1'] === $request->get('item_row1')) {
                $row['status'] = $request->get('status');
                break; // Exit after finding the first match
            }
        }

        $itemList->import = [
            'rows' => $rows
        ];

        $itemList->save();

        return response()->json(['success' => true], 200);
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

            if (!isset($data['header']) || !isset($data['tetelek']) || !is_array($data['tetelek'])) {
                return ['success' => false, 'message' => 'A valasz nem tartalmazza a kotelezo header+tetelek szerkezetet.'];
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

            if (!isset($data['header']) || !isset($data['tetelek']) || !is_array($data['tetelek'])) {
                return ['success' => false, 'message' => 'A JSON nem tartalmazza a kotelezo header+tetelek szerkezetet.'];
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
