<?php
namespace App\Http\Controllers;

use App\Models\PurchaseTicket;
use App\Models\PurchaseTicketItem;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\WarehouseTransferItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Business;
use App\Models\Product;
use App\Models\Employee;
use App\Models\ZImage;
use App\Item;
use App\Models\Hallmark;
use App\Models\Warehouse;
use App\Models\ProductColor;
use App\Models\ProductSign;
use App\Models\ProductStatus;
use App\Models\ProductType;
use App\Models\VatType;
use App\Models\Karat;
use App\Models\Category2;
use App\Models\Inventory;
use App\Models\InventoryPicture;
use App\Models\CustomerMarking;
use App\Enums\InventoryStatus;
use App\Enums\ProductTipus;
use App\Category;
use App\Tag;
use App\User;
use App\Http\Controllers\CategoryController;
use Carbon\Carbon;
use App\Http\Requests\StockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use PDF;
use File;

class StockController extends Controller
{
   const STATUSES = [
    1 => 'Raktáron',
    2 => 'Függőben',
    3 => 'Javítás alatt',
    4 => 'Bizományban',
    5 => 'Foglalt',
    6 => 'Szállítólevél',
    7 => 'Raktárközi átadás',
    8 => 'Fémjelzés alatt',
];
    public function __construct()
    {
        // no direct Stock model exists in this application, use inventory-specific actions instead
    }

    private function getStatusText($status)
    {
        $statuses = [
            1 => 'Raktáron',
            2 => 'Függőben',
            3 => 'Javítás alatt',
            4 => 'Bizományban',
            5 => 'Foglalt',
            6 => 'Szállítólevél',
            7 => 'Raktárközi átadás',
            8 => 'Fémjelzés alatt',
        ];
        return $statuses[$status] ?? 'Ismeretlen';
    }

    private function getOrderColumnNameInventory($index, $columns)
    {
        $columnMap = [
            1 => 'i.inventory_id',
            2 => 'i.hallmark',
            3 => 'i.termek_tipus_id',
            4 => 'i.weight',
            5 => 'i.weight',
            6 => 'i.description',
            7 => 'b.name',
            8 => 'i.price',
            9 => 'i.oneprice',
            10 => 'i.name',
            11 => 'i.quantity',
            12 => 'i.created_at',
        ];
        return $columnMap[$index] ?? 'i.inventory_id';
    }

    private function getProductTipusName($termekTipusId): string
    {
        return ProductTipus::getName($termekTipusId ? (int) $termekTipusId : null);
    }

    private function applyInventoryFilters($query, Request $request)
    {
        // Üzlet szűrés (business_ids)
        if ($request->filled('business_ids')) {
            $businessIds = (array) $request->business_ids;
            $query->whereIn('b.business_id', $businessIds);
        }

        // Raktár szűrés
        if ($request->filled('warehouse_ids')) {
            $warehouseIds = (array) $request->warehouse_ids;
            $query->whereIn('i.storage_id', $warehouseIds);
        }

        // Státusz szűrés
        if ($request->filled('status_ids')) {
            $statusIds = (array) $request->status_ids;
            $query->whereIn('i.status', $statusIds);
        }

        if ($request->filled('karat_ids')) {
            $query->whereIn(DB::raw("COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(i.item_data, '$.karat_id')), 'null'), NULLIF(JSON_UNQUOTE(JSON_EXTRACT(i.item_data, '$.karats[0]')), 'null'))"), (array) $request->karat_ids);
        }

        // Beszállító szűrés – mivel a készletnél nincs közvetlen kapcsolat, ezt a szűrőt most kihagyjuk,
        // vagy ha szükséges, akkor a termék beszállítói alapján kell szűrni (bonyolult).
        // Itt egyszerűen figyelmen kívül hagyjuk, mert a beszállító szűrés a get_actual_stock_search-ben működik.

        // Dátum szűrés (a termék létrehozási ideje alapján)
        if ($request->filled('purchase_from')) {
            $query->whereDate('i.created_at', '>=', $request->purchase_from);
        }
        if ($request->filled('purchase_to')) {
            $query->whereDate('i.created_at', '<=', $request->purchase_to);
        }

        // Súly szűrés
        if ($request->filled('weight_min')) {
            $query->where('i.weight', '>=', $request->weight_min);
        }
        if ($request->filled('weight_max')) {
            $query->where('i.weight', '<=', $request->weight_max);
        }

        // Grammár szűrés (beszerzési ár / súly)
        if ($request->filled('price_per_gram_min')) {
            $query->whereRaw('(i.oneprice / NULLIF(i.weight, 0)) >= ?', [$request->price_per_gram_min]);
        }
        if ($request->filled('price_per_gram_max')) {
            $query->whereRaw('(i.oneprice / NULLIF(i.weight, 0)) <= ?', [$request->price_per_gram_max]);
        }

        // Felhasználói jogosultság: ha nem admin és nem ötvös, csak a saját raktárait láthatja
        if (!Auth::user()->isAdmin() && !Auth::user()->is_goldsmith) {
            $userWarehouseIds = auth()->user()->employee?->warehouses->pluck('warehouse_id')->toArray();
            if (!empty($userWarehouseIds)) {
                $query->whereIn('i.storage_id', $userWarehouseIds);
            }
        }
    }
    public function actual_stock(Request $request) {
//->where('is_receiving', 0)
        $available_warehouses = auth()->user()->employee?->warehouses;
        
        $show = '';
        if ($request->has('filter')) {
            $stock = $this->get_actual_stock_search($request);
            $show = 'show';
        }else{
            $stock = $this->get_actual_stock();
        }
    
        return view('stock.actual_stock', 
            [
                'products' => $stock,
                //'products' => null,
                'available_warehouses' => $available_warehouses,
                'warehouses' => Warehouse::all(),
                'businesses' => Business::all(),
                'suppliers' => Customer::all(),
                'karats' => Karat::where('status', 1)->orderBy('name', 'ASC')->get(),
                'products_all' => Product::all(),
                'show' => $show,
            ]);
    }
    /**
     * Aktuális készlet lekérése az inventory táblából.
     * Csak a nem kivezetett (status != 0) rekordokat vesszük figyelembe.
     *
     * @param int|array|null $warehouse_id       Szűkítés raktár(ak)ra
     * @param bool           $non_accepted_items (nem használt, a metódus aláírás miatt marad)
     * @param int            $product_search     Ha >0, csak az adott termékre szűkít
     * @return array
     */
    public function get_actual_stock($warehouse_id = null, $non_accepted_items = false, $product_search = 0)
    {
        $query = DB::table('inventory as i')
            ->leftJoin('warehouses as w', 'i.storage_id', '=', 'w.warehouse_id')
            ->where('i.status', '!=', 0)
            ->where('i.quantity', '>', 0)
            ->select(
                'i.product_id',
                'i.storage_id as warehouse_id',
                DB::raw('SUM(i.quantity) as total_quantity'),
                DB::raw('ANY_VALUE(i.code) as code'),
                DB::raw('ANY_VALUE(i.name) as product_name'),
                DB::raw('ANY_VALUE(i.description) as short_description'),
                DB::raw("'' as product_brand"),
                DB::raw('ANY_VALUE(i.weight) as weight'),
                DB::raw('ANY_VALUE(i.created_at) as ins_date'),
                DB::raw('ANY_VALUE(i.oneprice) as real_price'),
                DB::raw('ANY_VALUE(w.name) as warehouse_name'),
                DB::raw('NULL as business_id'),
                DB::raw('NULL as business_name'),
                DB::raw("'' as unit_price_type"),
                DB::raw('ANY_VALUE(i.status) as status'),
                DB::raw('ANY_VALUE(i.investigation) as investigation'),
                DB::raw('ANY_VALUE(i.repair) as repair'),
                DB::raw('ANY_VALUE(i.hallmark) as hallmark'),
                DB::raw('ANY_VALUE(i.is_fake) as is_fake'),
                DB::raw('ANY_VALUE(i.factory_number) as factory_number'),
                DB::raw('ANY_VALUE(i.comment) as comment')
            )
            ->groupBy('i.product_id', 'i.storage_id');

        // Termék szűrő
        if ($product_search > 0) {
            $query->where('i.product_id', $product_search);
        }

        // Raktár szűrő
        if ($warehouse_id) {
            if (is_array($warehouse_id)) {
                $query->whereIn('i.storage_id', $warehouse_id);
            } else {
                $query->where('i.storage_id', $warehouse_id);
            }
        }

        // Felhasználói jogosultság
        if (!Auth::user()->isAdmin() && !Auth::user()->is_goldsmith) {
            $userWarehouseIds = auth()->user()->employee?->warehouses->pluck('warehouse_id')->toArray();
            if (!empty($userWarehouseIds)) {
                $query->whereIn('i.storage_id', $userWarehouseIds);
            }
        }

        $results = $query->get();

        $statuses = [
            1 => 'Raktáron',
            2 => 'Függőben',
            3 => 'Javítás alatt',
            4 => 'Bizományban',
            5 => 'Foglalt',
            6 => 'Szállítólevél',
            7 => 'Raktárközi átadás',
            8 => 'Fémjelzés alatt',
        ];

        $finalStocks = [];
        foreach ($results as $row) {
            // A kalkulált árat itt nem számoljuk, mert a raktárközi átadásnál nem használjuk.
            // Ha később szükséges, külön metódussal kérhető le.
            $finalStocks[] = [
                'product_id'        => $row->product_id,
                'code'              => $row->code,
                'product_name'      => $row->product_name,
                'short_description' => $row->short_description,
                'product_brand'     => $row->product_brand,
                'warehouse_id'      => $row->warehouse_id,
                'warehouse_name'    => $row->warehouse_name,
                'business_id'       => $row->business_id,
                'business_name'     => $row->business_name,
                'unit_price_type'   => $row->unit_price_type,
                'ins_date'          => $row->ins_date,
                'stock'             => $row->total_quantity,
                'weight'            => $row->weight,
                'status'            => $row->status,
                'status_text'       => $statuses[$row->status] ?? 'Ismeretlen',
                'calculated_price'  => 0, // nem használjuk itt
                'real_price'        => $row->real_price,
                'investigation'     => $row->investigation,
                'repair'            => $row->repair,
                'hallmark'          => $row->hallmark,
                'is_fake'           => $row->is_fake,
                'factory_number'    => $row->factory_number,
                'comment'           => $row->comment,
            ];
        }

        return $finalStocks;
    }
    /**
     * Keresés az aktuális készletben a szűrőparaméterek alapján.
     * A szűrés a get_actual_stock() eredményén történik (post‑filter).
     *
     * @param \Illuminate\Http\Request $request  A szűrőparamétereket tartalmazó kérés
     * @return array
     */
    public function get_actual_stock_search($request)
    {
        // Először lekérjük a teljes aktuális készletet (nem kivezetett tételek)
        $allStocks = $this->get_actual_stock();

        $filtered = [];
        foreach ($allStocks as $stock) {
            // --- Beszállító szűrés ---
            if (!empty($request->supplier_ids)) {
                $suppliersForProduct = $this->get_suppliers_and_purchase_data_by_product_id($stock['product_id']);
                $supplierIds = array_column($suppliersForProduct, 'supplier_id');
                if (empty(array_intersect($request->supplier_ids, $supplierIds))) {
                    continue;
                }
            }

            // --- Grammár (beszerzési ár / súly) szűrés ---
            if (!empty($request->price_per_gram_min) || !empty($request->price_per_gram_max)) {
                $purchaseData = $this->get_suppliers_and_purchase_data_by_product_id($stock['product_id']);
                $withinRange = false;
                foreach ($purchaseData as $p) {
                    $ppg = $p['price_per_gram'] ?? 0;
                    if ((!$request->price_per_gram_min || $ppg >= $request->price_per_gram_min) &&
                        (!$request->price_per_gram_max || $ppg <= $request->price_per_gram_max)) {
                        $withinRange = true;
                        break;
                    }
                }
                if (!$withinRange) {
                    continue;
                }
            }

            // --- Súly szűrés ---
            if (!empty($request->weight_min) && ($stock['weight'] ?? 0) < $request->weight_min) {
                continue;
            }
            if (!empty($request->weight_max) && ($stock['weight'] ?? 0) > $request->weight_max) {
                continue;
            }

            // --- Üzlet szűrés ---
            if (!empty($request->business_ids) && !in_array($stock['business_id'], $request->business_ids)) {
                continue;
            }

            // --- Raktár szűrés ---
            if (!empty($request->warehouse_ids) && !in_array($stock['warehouse_id'], $request->warehouse_ids)) {
                continue;
            }

            // --- Beszerzés dátuma szűrés (inventory létrehozási ideje alapján) ---
            $stockDate = !empty($stock['ins_date']) ? Carbon::parse($stock['ins_date']) : null;
            if (!empty($request->purchase_from) && $stockDate && $stockDate->lt(Carbon::createFromFormat('Y-m-d', $request->purchase_from)->startOfDay())) {
                continue;
            }
            if (!empty($request->purchase_to) && $stockDate && $stockDate->gt(Carbon::createFromFormat('Y-m-d', $request->purchase_to)->endOfDay())) {
                continue;
            }

            // --- Státusz szűrés ---
            if (!empty($request->status_ids) && !in_array($stock['status'], $request->status_ids)) {
                continue;
            }

            // Ha minden szűrőn átment, hozzáadjuk a találatokhoz
            $filtered[] = $stock;
        }

        return $filtered;
    }
    private function getUserWarehouses($warehouse_id = null)
    {
        if (Auth::user()->isAdmin() || Auth::user()->is_goldsmith) {
            $warehouses = DB::table('warehouses')->get();
        } else {
            $warehouses = auth()->user()->employee?->warehouses;
        }
        if ($warehouse_id) {
            if (is_array($warehouse_id)) {
                $warehouses = $warehouses->whereIn('warehouse_id', $warehouse_id);
            } else {
                $warehouses = $warehouses->where('warehouse_id', $warehouse_id);
            }
        }
        return $warehouses;
    }

    public function producthistoryByProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // 1. Incoming movements (from inventory with source_type)
        $incoming = DB::table('inventory')
            ->leftJoin('purchase_tickets', function($join) {
                $join->on('inventory.source_header_id', '=', 'purchase_tickets.purchase_ticket_id')
                    ->where('inventory.source_type', '=', 'purchase');
            })
            ->leftJoin('stock_entries', function($join) {
                $join->on('inventory.source_header_id', '=', 'stock_entries.stock_entry_id')
                    ->where('inventory.source_type', '=', 'stock_entry');
            })
            ->leftJoin('consignment_receipts', function($join) {
                $join->on('inventory.source_header_id', '=', 'consignment_receipts.consignment_receipt_id')
                    ->where('inventory.source_type', '=', 'consignment');
            })
            ->leftJoin('item_lists', function($join) {
                $join->on('inventory.source_header_id', '=', 'item_lists.id')
                    ->where('inventory.source_type', '=', 'item_lists');
            })
            ->where('inventory.product_id', $id)
            ->selectRaw("
                CASE 
                    WHEN inventory.source_type = 'purchase' THEN 'Beérkezés (Vételi jegy)'
                    WHEN inventory.source_type = 'stock_entry' THEN 'Ékszervásárlás'
                    WHEN inventory.source_type = 'consignment' THEN 'Bizományosi átvétel'
                    WHEN inventory.source_type = 'item_lists' THEN 'Kényszerlista'
                END as type,
                COALESCE(purchase_tickets.created_at, stock_entries.fulfillment_date, consignment_receipts.created_at, item_lists.created_at, inventory.created_at) as date,
                COALESCE(purchase_tickets.employee_id, stock_entries.employee_id, consignment_receipts.employee_id, item_lists.user_id) as employee_id,
                COALESCE(purchase_tickets.customer_id, stock_entries.customer_id, consignment_receipts.customer_id, NULL) as customer_id,
                inventory.quantity,
                inventory.weight,
                inventory.price,
                inventory.warehouse_from,
                inventory.warehouse_to,
                inventory.source_type,
                inventory.inventory_id as source_id
            ")
            ->get();

        // 2. Outgoing via delivery_note_items (external delivery, no warehouse_to)
        $deliveryOut = DB::table('delivery_note_items')
            ->join('inventory', 'delivery_note_items.inventory_item_id', '=', 'inventory.inventory_id')
            ->join('delivery_note', 'delivery_note_items.delivery_note_id', '=', 'delivery_note.delivery_note_id')
            ->where('inventory.product_id', $id)
            ->whereNull('delivery_note_items.warehouse_to')
            ->selectRaw("
                'Kivétel (Szállítólevél)' as type,
                delivery_note.created_at as date,
                delivery_note.employee_id,
                delivery_note_items.quantity as quantity,
                delivery_note_items.weight as weight,
                delivery_note_items.price as price,
                delivery_note_items.warehouse_from as warehouse_from,
                NULL as warehouse_to,
                delivery_note.customer_id,
                'delivery_note' as source,
                delivery_note_items.delivery_note_id as source_id
            ")
            ->get();

        // 3. Internal transfers via delivery_note_items (with warehouse_to)
        $deliveryTransfer = DB::table('delivery_note_items')
            ->join('inventory', 'delivery_note_items.inventory_item_id', '=', 'inventory.inventory_id')
            ->join('delivery_note', 'delivery_note_items.delivery_note_id', '=', 'delivery_note.delivery_note_id')
            ->where('inventory.product_id', $id)
            ->whereNotNull('delivery_note_items.warehouse_to')
            ->selectRaw("
                'Raktárközi mozgás (szállítólevél)' as type,
                delivery_note.created_at as date,
                delivery_note.employee_id,
                delivery_note_items.quantity as quantity,
                delivery_note_items.weight as weight,
                delivery_note_items.warehouse_from as warehouse_from,
                delivery_note_items.warehouse_to as warehouse_to,
                NULL as customer_id,
                'delivery_transfer' as source,
                delivery_note_items.delivery_note_id as source_id
            ")
            ->get();

        // 4. Warehouse transfers (from warehouse_transfer_items)
        $warehouseTransfer = DB::table('warehouse_transfer_items')
            ->join('inventory', 'warehouse_transfer_items.inventory_item_id', '=', 'inventory.inventory_id')
            ->join('warehouse_transfer', 'warehouse_transfer_items.warehouse_transfer_id', '=', 'warehouse_transfer.warehouse_transfer_id')
            ->where('inventory.product_id', $id)
            ->selectRaw("
                'Raktárközi mozgás (átadás)' as type,
                warehouse_transfer.created_at as date,
                warehouse_transfer.employee_id,
                warehouse_transfer_items.quantity,
                inventory.weight,
                warehouse_transfer_items.warehouse_from,
                warehouse_transfer_items.warehouse_to,
                NULL as customer_id,
                'warehouse_transfer' as source,
                warehouse_transfer_items.transfer_items_id as source_id
            ")
            ->get();

        // Merge and sort by date
        $history = $incoming->merge($deliveryOut)
                            ->merge($deliveryTransfer)
                            ->merge($warehouseTransfer)
                            ->sortBy('date')
                            ->values();

        return view('stock.product_history', compact('product', 'history'));
    }

    public function producthistory(Request $request, $id)
{
    $inventory = Inventory::findOrFail($id);
    $product = Product::findOrFail($inventory->product_id);

    // 1. Beérkezések (inventory rekordok létrejötte)
    $incoming = DB::table('inventory')
        ->leftJoin('purchase_tickets', function($join) {
            $join->on('inventory.source_header_id', '=', 'purchase_tickets.purchase_ticket_id')
                 ->where('inventory.source_type', '=', 'purchase');
        })
        ->leftJoin('stock_entries', function($join) {
            $join->on('inventory.source_header_id', '=', 'stock_entries.stock_entry_id')
                 ->where('inventory.source_type', '=', 'stock_entry');
        })
        ->leftJoin('consignment_receipts', function($join) {
            $join->on('inventory.source_header_id', '=', 'consignment_receipts.consignment_receipt_id')
                 ->where('inventory.source_type', '=', 'consignment');
        })
        ->leftJoin('item_lists', function($join) {
            $join->on('inventory.source_header_id', '=', 'item_lists.id')
                 ->where('inventory.source_type', '=', 'item_list');
        })
        ->where('inventory.inventory_id', $id)
        ->selectRaw("
            CASE 
                WHEN inventory.source_type = 'purchase' THEN 'Beérkezés (Vételi jegy)'
                WHEN inventory.source_type = 'stock_entry' THEN 'Ékszervásárlás'
                WHEN inventory.source_type = 'consignment' THEN 'Bizományosi átvétel'
                WHEN inventory.source_type = 'item_list' THEN 'Kényszerlista'
            END as type,
            COALESCE(purchase_tickets.created_at, stock_entries.fulfillment_date, consignment_receipts.created_at, item_lists.created_at, inventory.created_at) as date,
            COALESCE(purchase_tickets.employee_id, stock_entries.employee_id, consignment_receipts.employee_id, 
                (SELECT employee_id FROM employees WHERE user_id = item_lists.user_id LIMIT 1)
            ) as employee_id,
            COALESCE(purchase_tickets.customer_id, stock_entries.customer_id, consignment_receipts.customer_id, NULL) as customer_id,
            inventory.quantity,
            inventory.weight,
            inventory.price,
            inventory.warehouse_from,
            inventory.warehouse_to,
            inventory.inventory_id as source_id
        ");

    // 2. Szállítólevél mozgások (kivétel vagy raktárközi átadás)
    $deliveryMovements = DB::table('delivery_note_items')
        ->join('inventory', 'delivery_note_items.inventory_item_id', '=', 'inventory.inventory_id')
        ->join('delivery_note', 'delivery_note_items.delivery_note_id', '=', 'delivery_note.delivery_note_id')
        ->where('inventory.inventory_id', $id)
        ->selectRaw("
            CASE 
                WHEN delivery_note_items.warehouse_to IS NULL THEN 'Kivétel (Szállítólevél)'
                ELSE 'Raktárközi mozgás (szállítólevél)'
            END as type,
            delivery_note.created_at as date,
            delivery_note.employee_id,
            delivery_note.customer_id,
            delivery_note_items.quantity as quantity,
            delivery_note_items.weight as weight,
            delivery_note_items.price as price,
            delivery_note_items.warehouse_from as warehouse_from,
            delivery_note_items.warehouse_to as warehouse_to,
            delivery_note_items.note_items_id as source_id
        ");

    // 3. Raktárközi átadások (warehouse_transfer_items)
    $warehouseMovements = DB::table('warehouse_transfer_items')
        ->join('inventory', 'warehouse_transfer_items.inventory_item_id', '=', 'inventory.inventory_id')
        ->join('warehouse_transfer', 'warehouse_transfer_items.warehouse_transfer_id', '=', 'warehouse_transfer.warehouse_transfer_id')
        ->where('inventory.inventory_id', $id)
        ->selectRaw("
            'Raktárközi mozgás (átadás)' as type,
            warehouse_transfer.created_at as date,
            warehouse_transfer.employee_id,
            NULL as customer_id,
            warehouse_transfer_items.quantity,
            inventory.weight,
            NULL as price,
            warehouse_transfer_items.warehouse_from,
            warehouse_transfer_items.warehouse_to,
            warehouse_transfer_items.transfer_items_id as source_id
        ");

    // Egyesítés és rendezés
    $history = $incoming->union($deliveryMovements)
                        ->union($warehouseMovements)
                        ->orderBy('date')
                        ->get();

    return view('stock.product_history', compact('product', 'history'));
}
    public function getActualStockData(Request $request)
    {
        try {
            $warehouseBusinessSub = DB::table('business_warehouse as bw')
                ->selectRaw('bw.warehouse_warehouse_id, MIN(bw.business_business_id) as business_id')
                ->groupBy('bw.warehouse_warehouse_id');

            $query = DB::table('inventory as i')
                ->leftJoin('warehouses as w', 'i.storage_id', '=', 'w.warehouse_id')
                ->leftJoinSub($warehouseBusinessSub, 'wb', function ($join) {
                    $join->on('w.warehouse_id', '=', 'wb.warehouse_warehouse_id');
                })
                ->leftJoin('businesses as b', 'wb.business_id', '=', 'b.business_id')
                ->select(
                    'i.inventory_id',
                    'i.product_id',
                    'i.storage_id as warehouse_id',
                    'i.quantity',
                    'i.item_data',
                    'i.weight',
                    'i.price',
                    'i.oneprice',
                    'i.termek_tipus_id',
                    'i.hallmark',
                    'i.status',
                    'i.created_at',
                    'b.name as business_name',
                    'w.name as warehouse_name'
                );

            $totalRecords = (clone $query)->count();

            $this->applyInventoryFilters($query, $request);

            $searchValue = $request->input('search.value');
            if (!empty($searchValue)) {
                if (preg_match('/PRD-\s*(\d+)/i', $searchValue, $matches)) {
                    $query->where('i.inventory_id', (int) $matches[1]);
                } else {
                    $query->where(function($q) use ($searchValue) {
                        $q->where('i.name', 'LIKE', "%{$searchValue}%")
                            ->orWhere('i.code', 'LIKE', "%{$searchValue}%")
                            ->orWhere('i.description', 'LIKE', "%{$searchValue}%")
                            ->orWhere('w.name', 'LIKE', "%{$searchValue}%")
                            ->orWhere('b.name', 'LIKE', "%{$searchValue}%")
                            ->orWhere('i.item_data', 'LIKE', "%{$searchValue}%");
                    });
                }
            }

            $filteredRecords = (clone $query)->count();

            $totals = (clone $query)->select([])->selectRaw("
                COALESCE(SUM(i.weight), 0) as weight,
                COALESCE(SUM(CAST(COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(i.item_data, '$.eladasi_ar')), 'null'), i.price, 0) AS DECIMAL(20, 2))), 0) as selling_price,
                COALESCE(SUM(CAST(COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(i.item_data, '$.beszerzesi_ar')), 'null'), i.oneprice, 0) AS DECIMAL(20, 2))), 0) as purchase_price
            ")->first();

            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'asc');
            $columns = $request->input('columns', []);
            $orderColumnName = $this->getOrderColumnNameInventory($orderColumnIndex, $columns);
            if ($orderColumnName) {
                $query->orderBy($orderColumnName, $orderDirection);
            }

            $start = $request->input('start', 0);
            $length = $request->input('length', 25);
            if ($length > 0) {
                $query->offset($start)->limit($length);
            }

            $results = $query->get();
            $karatMap = Karat::where('status', 1)->pluck('name', 'karat_id');

            $formattedData = $results->map(function($row) use ($karatMap) {
                $itemData = $row->item_data;
                if (is_string($itemData)) {
                    $itemData = json_decode($itemData, true);
                }
                if (!is_array($itemData)) {
                    $itemData = [];
                }

                $termekTipusId = $itemData['termek_tipus_id'] ?? $row->termek_tipus_id ?? null;
                $karatId = $itemData['karat_id'] ?? ($itemData['karats'][0] ?? null);
                $hallmarkValue = $itemData['hallmarks'] ?? ($itemData['hallmark'] ?? $row->hallmark ?? 0);
                $eladasiAr = $itemData['eladasi_ar'] ?? $row->price ?? 0;
                $beszerzesiAr = $itemData['beszerzesi_ar'] ?? $row->oneprice ?? 0;

                return [
                    'checkbox' => '<input type="checkbox" class="note-checkbox" 
                        data-warehouse_id="' . ($row->warehouse_id ?? '') . '" 
                        data-item_id="' . ($row->product_id ?? '') . '" 
                        data-business_id="">',
                    'inventory_id_text' => 'PRD-' . ($row->inventory_id ?? ''),
                    'inventory_hallmark' => ((int) $hallmarkValue === 1) ? 'Van' : 'Nincs',
                    'inventory_termek_tipus' => \App\Models\ProductType::getName($termekTipusId),
                    'inventory_karat' => (string) ($karatMap[$karatId] ?? ''),
                    'product_weight' => number_format($row->weight ?? 0, 2, ',', ' ') . ' g',
                    'inventory_size' => $itemData['size'] ?? '',
                    'business_name' => $row->business_name.'<br />('.($row->warehouse_name ?? '') . ')',
                    'inventory_eladasi_ar' => number_format((float) $eladasiAr, 0, ',', ' ') . ' Ft',
                    'inventory_beszerzesi_ar' => number_format((float) $beszerzesiAr, 0, ',', ' ') . ' Ft',
                    'inventory_vzj' => $itemData['vzj'] ?? '',
                    'inventory_stock_status' => $this->getStatusText((int) ($row->status ?? 0)),
                    'inventory_created_at' => $row->created_at ? date('Y-m-d H:i:s', strtotime($row->created_at)) : '',
                    'actions' => $this->getActionButtons((object)[
                        'product_id' => $row->product_id,
                        'warehouse_id' => $row->warehouse_id,
                        'inventory_id' => $row->inventory_id
                    ])
                ];
            });

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'totals' => [
                    'weight' => (float) $totals->weight,
                    'selling_price' => (float) $totals->selling_price,
                    'purchase_price' => (float) $totals->purchase_price,
                ],
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            \Log::error('Stock AJAX Error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Hiba történt az adatok betöltése során.'
            ], 500);
        }
    }
   
    public function get_suppliers_and_purchase_data_by_product_id($product_id)
    {
        $data = collect();

        // Purchase tickets
        $purchaseData = DB::table('inventory')
            ->join('purchase_tickets', 'inventory.source_header_id', '=', 'purchase_tickets.purchase_ticket_id')
            ->where('inventory.product_id', $product_id)
            ->where('inventory.source_type', 'purchase')
            ->select('purchase_tickets.customer_id as supplier_id', 'purchase_tickets.printed_at as purchase_date', 'inventory.oneprice as price_per_gram')
            ->get();

        // Stock entries
        $stockData = DB::table('inventory')
            ->join('stock_entries', 'inventory.source_header_id', '=', 'stock_entries.stock_entry_id')
            ->where('inventory.product_id', $product_id)
            ->where('inventory.source_type', 'stock_entry')
            ->select('stock_entries.customer_id as supplier_id', 'stock_entries.fulfillment_date as purchase_date', 'inventory.oneprice as price_per_gram')
            ->get();

        // Consignment receipts
        $consignmentData = DB::table('inventory')
            ->join('consignment_receipts', 'inventory.source_header_id', '=', 'consignment_receipts.consignment_receipt_id')
            ->where('inventory.product_id', $product_id)
            ->where('inventory.source_type', 'consignment')
            ->select('consignment_receipts.customer_id as supplier_id', 'consignment_receipts.printed_at as purchase_date', 'inventory.oneprice as price_per_gram')
            ->get();

        $data = $data->merge($purchaseData)->merge($stockData)->merge($consignmentData);
        return $data->unique(function($item) {
            return $item->supplier_id . '|' . $item->purchase_date . '|' . $item->price_per_gram;
        })->map(fn($i) => (array)$i)->values()->all();
    }

    public function prod_sell_out(Request $request)
    {
        $employee_id = Auth::user()->employee?->employee_id;
        $warehouse = Warehouse::findOrFail($request->warehouse_id);
        $act_price = $request->prod_out_price ?? 0;

        // Find an inventory item with this product and warehouse, status = 1 (in stock)
        $inventoryItem = Inventory::where('product_id', $request->product_id)
            ->where('storage_id', $warehouse->warehouse_id)
            ->where('status', InventoryStatus::IN_STOCK->value)
            ->where('quantity', '>', 0)
            ->first();

        if (!$inventoryItem) {
            return response()->json(['success' => false, 'message' => 'Nincs ilyen termék a raktárban!']);
        }

        // Determine quantity to remove (usually 1, but could be more)
        $quantityToRemove = 1;

        // Create delivery note (external)
        $deliveryNoteId = $this->create_delivery_note(
            warehouse_from: $warehouse->warehouse_id,
            warehouse_to: null,
            employee_id: $employee_id,
            business_id: $warehouse->businesses->first()->business_id,
            customer_id: 0
        );

        // Create delivery note item linked to inventory item
        $this->create_delivery_note_item(
            delivery_note_id: $deliveryNoteId,
            inventory_item_id: $inventoryItem->inventory_id,
            quantity: $quantityToRemove,
            warehouse_from_id: $warehouse->warehouse_id,
            warehouse_to_id: null,
            price: $act_price
        );

        // Update inventory: either reduce quantity or change status
        if ($inventoryItem->quantity > $quantityToRemove) {
            $inventoryItem->quantity -= $quantityToRemove;
            $inventoryItem->save();
        } else {
            $inventoryItem->status = InventoryStatus::SOLD_OUT->value;
            $inventoryItem->save();
        }

        return response()->json(['success' => true, 'message' => 'Termék kivezetve']);
    }

    private function create_delivery_note($warehouse_from, $warehouse_to, $employee_id, $business_id, $customer_id)
    {
        $warehouse_from_name = Warehouse::find($warehouse_from)->name;
        $warehouse_to_name = $warehouse_to ? Warehouse::find($warehouse_to)->name : null;
        $business = Business::find($business_id);
        $employee = Employee::find($employee_id);
        $customer = $customer_id ? Customer::find($customer_id) : null;
        $serial = $this->getNextSerialDeliveryNote();
        $note_number = 'SZALL/'. $business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);

        $delivery_note = DeliveryNote::create([
            'delivery_note_number' => $note_number,
            'employee_id' => $employee_id,
            'employee_name' => $employee->lastname.' '.$employee->firstname,
            'business_id' => $business_id,
            'business_name' => $business->name,
            'business_address' => $business->zip.' '.$business->city.', '.$business->address,
            'customer_id' => $customer_id,
            'customer_name' => $customer->name ?? null,
            'received_at' => now(),
            'receive_employee_id' => $employee_id,
            'receive_employee_name' => $employee->lastname.' '.$employee->firstname,
            'warehouse_from' => $warehouse_from,
            'warehouse_from_name' => $warehouse_from_name,
            'warehouse_to' => $warehouse_to,
            'warehouse_to_name' => $warehouse_to_name,
        ]);
        return $delivery_note->delivery_note_id;
    }

    private function create_delivery_note_item($delivery_note_id, $inventory_item_id, $quantity, $warehouse_from_id, $warehouse_to_id, $price)
    {
        $inventory = Inventory::findOrFail($inventory_item_id);
        $product = $inventory->product;

        DeliveryNoteItem::create([
            'delivery_note_id' => $delivery_note_id,
            'inventory_item_id' => $inventory_item_id,
            'product_id' => $product->product_id,
            'main_product_id' => $product->main_product_id,
            'product_name' => $product->name,
            'product_description' => $inventory->description,
            'factory_number' => $inventory->factory_number,
            'weight' => $inventory->weight,
            'quantity' => $quantity,
            'price' => $price,
            'warehouse_from' => $warehouse_from_id,
            'warehouse_to' => $warehouse_to_id,
            'accepted_employee_id' => Auth::user()->employee?->employee_id,
            'accepted_at' => now(),
        ]);
    }

    private function applyFilters($query, Request $request)
    {
        // Business filter
        if ($request->filled('business_ids')) {
            $businessIds = is_array($request->business_ids) ? $request->business_ids : [$request->business_ids];
            $query->whereIn('se.business_id', $businessIds);
        }

        // Warehouse filter
        if ($request->filled('warehouse_ids')) {
            $warehouseIds = is_array($request->warehouse_ids) ? $request->warehouse_ids : [$request->warehouse_ids];
            $query->whereIn('sei.warehouse_to', $warehouseIds);
        }

        // Supplier filter (customer)
        if ($request->filled('supplier_ids')) {
            $supplierIds = is_array($request->supplier_ids) ? $request->supplier_ids : [$request->supplier_ids];
            $query->whereIn('se.customer_id', $supplierIds);
        }

        // Date range filter
        if ($request->filled('purchase_from')) {
            $query->whereDate('se.fulfillment_date', '>=', $request->purchase_from);
        }
        
        if ($request->filled('purchase_to')) {
            $query->whereDate('se.fulfillment_date', '<=', $request->purchase_to);
        }

        // Weight filter
        if ($request->filled('weight_min')) {
            $query->where('sei.weight', '>=', $request->weight_min);
        }
        
        if ($request->filled('weight_max')) {
            $query->where('sei.weight', '<=', $request->weight_max);
        }

        // Price per gram filter (calculate from price/weight)
        if ($request->filled('price_per_gram_min')) {
            $query->whereRaw('(sei.oneprice / NULLIF(sei.weight, 0)) >= ?', [$request->price_per_gram_min]);
        }
        
        if ($request->filled('price_per_gram_max')) {
            $query->whereRaw('(sei.oneprice / NULLIF(sei.weight, 0)) <= ?', [$request->price_per_gram_max]);
        }

        // Additional status filters
        if ($request->filled('status')) {
            $query->where('sei.status', $request->status);
        }

        if ($request->filled('investigation')) {
            $query->where('sei.investigation', $request->investigation);
        }

        if ($request->filled('hallmark')) {
            $query->where('sei.hallmark', $request->hallmark);
        }

        // User-specific warehouse restrictions (if not admin)
        if (!Auth::user()->isAdmin() && !Auth::user()->is_goldsmith) {
            $userWarehouseIds = auth()->user()->employee?->warehouses->pluck('warehouse_id')->toArray();
            if (!empty($userWarehouseIds)) {
                $query->whereIn('sei.warehouse_to', $userWarehouseIds);
            }
        }
        // Státusz szűrő (ha van kiválasztva)
        if ($request->filled('status_ids')) {
            $statusIds = (array) $request->status_ids;
            $query->whereIn('i.status', $statusIds);
        }
    }
    /**
         * Get order column name based on DataTable column index
     */
    private function getOrderColumnName($index, $columns)
    {
        $columnMap = [
            1 => 'sei.name',           // product_name
            2 => 'p.brand',           // product_brand
            3 => 'sei.description',   // short_description
            4 => 'b.name',            // business_name
            5 => 'w.name',            // warehouse_name
            6 => 'sei.created_at',    // ins_date
            7 => 'sei.quantity',      // stock
            8 => 'sei.weight',        // weight
            9 => 'upt.name',          // unit_price_type
            10 => 'sei.price',        // calculated_price
            11 => 'sei.oneprice',     // real_price
        ];

        return $columnMap[$index] ?? 'sei.name';
    }

    /**
     * Generate action buttons HTML
     */
    private function getActionButtons($item)
    {
        $editUrl = $item->inventory_id ? route('actual_stock.edit', $item->inventory_id) : '#';
        $showUrl = $item->inventory_id ? route('actual_stock.show', $item->inventory_id) : '#';
        return '
            <a href="'.$showUrl.'" class="btn btn-sm btn-primary">
                <i class="fa fa-eye"></i>
            </a>
            <a href="'.route('producthistory', $item->inventory_id).'" class="btn btn-sm btn-success">
                <i class="fa fa-history"></i>
            </a>
            <a href="'.$editUrl.'" class="btn btn-sm btn-info">
                <i class="fa fa-edit"></i>
            </a>
            <button type="button" class="btn btn-sm btn-secondary quick-view-images" 
                    data-inventory-id="'.$item->inventory_id.'" title="Kép gyorsnéző">
                <i class="fa fa-image"></i>
            </button>
            <button type="button" class="btn btn-sm btn-warning sell" 
                    data-id="'.$item->product_id.'" 
                    data-warehouse_id="'.$item->warehouse_id.'">
                <i class="fa fa-cart-arrow-down"></i>
            </button>
        ';
    }

    public function edit($id)
    {
        $inventory = Inventory::with(['product', 'storage', 'inventory_pictures'])->findOrFail($id);
        $warehouses = Warehouse::orderBy('name', 'asc')->get();
        $categories = Category::where('status', 1)->orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_types = ProductType::where('status', 1)->orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();
        $customer_markings = CustomerMarking::with('productStatus')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();

        $itemData = $inventory->item_data;
        if (is_string($itemData)) {
            $itemData = json_decode($itemData, true);
        }
        if (!is_array($itemData)) {
            $itemData = [];
        }

        if (isset($itemData['statuses']) && !is_array($itemData['statuses'])) {
            $itemData['statuses'] = $itemData['statuses'] ? [$itemData['statuses']] : [];
        }
        if (isset($itemData['colors']) && !is_array($itemData['colors'])) {
            $itemData['colors'] = $itemData['colors'] ? [$itemData['colors']] : [];
        }
        if (!isset($itemData['karat_id']) && isset($itemData['karats'][0])) {
            $itemData['karat_id'] = $itemData['karats'][0];
        }
        if (!isset($itemData['category2_id']) && array_key_exists('product_categories_2', $itemData)) {
            $itemData['category2_id'] = is_array($itemData['product_categories_2'])
                ? ($itemData['product_categories_2'][0] ?? null)
                : $itemData['product_categories_2'];
        }

        return view('stock.edit', compact(
            'inventory',
            'warehouses',
            'categories',
            'vat_types',
            'product_statuses',
            'product_colors',
            'product_types',
            'karats',
            'category2s',
            'customer_markings',
            'itemData'
        ));
    }

    public function show($id)
    {
        $inventory = Inventory::with(['product', 'storage', 'inventory_pictures'])->findOrFail($id);
        $warehouses = Warehouse::orderBy('name', 'asc')->get();
        $categories = Category::where('status', 1)->orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $karats = Karat::where('status', 1)->orderBy('name', 'ASC')->get();
        $category2s = Category2::where('status', 1)->orderBy('name', 'ASC')->get();

        $itemData = $inventory->item_data;
        if (is_string($itemData)) {
            $itemData = json_decode($itemData, true);
        }
        if (!is_array($itemData)) {
            $itemData = [];
        }

        if (isset($itemData['statuses']) && !is_array($itemData['statuses'])) {
            $itemData['statuses'] = $itemData['statuses'] ? [$itemData['statuses']] : [];
        }
        if (isset($itemData['colors']) && !is_array($itemData['colors'])) {
            $itemData['colors'] = $itemData['colors'] ? [$itemData['colors']] : [];
        }
        if (!isset($itemData['karat_id']) && isset($itemData['karats'][0])) {
            $itemData['karat_id'] = $itemData['karats'][0];
        }
        if (!isset($itemData['category2_id']) && array_key_exists('product_categories_2', $itemData)) {
            $itemData['category2_id'] = is_array($itemData['product_categories_2'])
                ? ($itemData['product_categories_2'][0] ?? null)
                : $itemData['product_categories_2'];
        }

        return view('stock.show', compact(
            'inventory',
            'warehouses',
            'categories',
            'vat_types',
            'product_statuses',
            'product_colors',
            'karats',
            'category2s',
            'itemData'
        ));
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $validated = $request->validate([
            //'storage_id' => 'required|integer|exists:warehouses,warehouse_id',
            'status' => 'required|integer',
            //'quantity' => 'required|numeric|min:0',
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
        ]);

        $itemDataValidated = $request->validate([
            'customer_marking_id' => 'nullable|integer|exists:customer_markings,id',
            'vzj' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer',
            'beszerzesi_ar' => 'nullable|numeric|min:0',
            'eladasi_ar' => 'nullable|numeric|min:0',
            'vat_type_id' => 'nullable|integer|exists:master_vat_types,vat_type_id',
            'hallmarks' => 'nullable|integer|in:0,1',
            'statuses' => 'nullable|array',
            'statuses.*' => 'integer|exists:master_product_statuses,product_status_id',
            'karat_id' => 'nullable|integer|exists:master_karats,karat_id',
            'colors' => 'nullable|array',
            'colors.*' => 'integer|exists:master_product_colors,product_color_id',
            'size' => 'nullable|string|max:255',
            'category2_id' => 'nullable|integer|exists:master_category2s,category2_id',
            'label_type' => 'nullable|integer',
            'termek_tipus_id' => 'nullable|integer',
        ]);

        $inventory->update(array_merge($validated, [
            'investigation' => $request->has('investigation') ? 1 : 0,
            'repair' => $request->has('repair') ? 1 : 0,
            'hallmark' => $request->has('hallmark') ? 1 : 0,
            'is_fake' => $request->has('is_fake') ? 1 : 0,
        ]));

        $itemData = $inventory->item_data;
        if (is_string($itemData)) {
            $itemData = json_decode($itemData, true);
        }
        if (!is_array($itemData)) {
            $itemData = [];
        }

        $selectedCustomerMarking = null;
        if (!empty($itemDataValidated['customer_marking_id'])) {
            $selectedCustomerMarking = CustomerMarking::with('productStatus')
                ->where('id', $itemDataValidated['customer_marking_id'])
                ->where('status', 1)
                ->first();
        }

        $itemData['customer_marking_id'] = $itemDataValidated['customer_marking_id'] ?? ($itemData['customer_marking_id'] ?? null);
        $itemData['vzj'] = $itemDataValidated['vzj'] ?? ($itemData['vzj'] ?? null);
        if ($selectedCustomerMarking) {
            $itemData['vzj'] = $selectedCustomerMarking->name;
        }
        $itemData['category_id'] = $itemDataValidated['category_id'] ?? ($itemData['category_id'] ?? null);
        $itemData['beszerzesi_ar'] = $itemDataValidated['beszerzesi_ar'] ?? ($itemData['beszerzesi_ar'] ?? null);
        $itemData['eladasi_ar'] = $itemDataValidated['eladasi_ar'] ?? ($itemData['eladasi_ar'] ?? null);
        $itemData['vat_type_id'] = $itemDataValidated['vat_type_id'] ?? ($itemData['vat_type_id'] ?? null);
        $itemData['hallmarks'] = $itemDataValidated['hallmarks'] ?? ($itemData['hallmarks'] ?? 0);
        $itemData['statuses'] = $itemDataValidated['statuses'] ?? ($itemData['statuses'] ?? []);
        $itemData['karat_id'] = $itemDataValidated['karat_id'] ?? ($itemData['karat_id'] ?? null);
        $itemData['colors'] = $itemDataValidated['colors'] ?? ($itemData['colors'] ?? []);
        $itemData['size'] = $itemDataValidated['size'] ?? ($itemData['size'] ?? null);
        $itemData['category2_id'] = $itemDataValidated['category2_id'] ?? ($itemData['category2_id'] ?? null);
        $itemData['label_type'] = $itemDataValidated['label_type'] ?? ($itemData['label_type'] ?? null);
        $itemData['termek_tipus_id'] = $itemDataValidated['termek_tipus_id'] ?? ($itemData['termek_tipus_id'] ?? null);

        $inventory->item_data = $itemData;
        $inventory->save();

        return redirect()->route('actual_stock')->withStatus('A készlet adatainak mentése sikeres.');
    }

    public function uploadInventoryImage(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|integer|exists:inventory,inventory_id',
            'images.*' => 'required|image|max:5120',
        ]);

        $inventory = Inventory::findOrFail($request->input('inventory_id'));
        $path = public_path('/document_storage/inventory_images/' . $inventory->inventory_id . '/');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $uploaded = [];
        foreach ($request->file('images', []) as $file) {
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file->getClientOriginalName());
            $image = Image::make($file->getRealPath());
            $image->save($path . $filename);
            
            $picture = InventoryPicture::create([
                'inventory_id' => $inventory->inventory_id,
                'image_path' => $filename,
            ]);

            $uploaded[] = $picture->id;
        }

        return response()->json(['success' => true, 'uploaded' => $uploaded]);
    }

   public function loadInventoryGallery(Request $request)
{
    $inventoryId = $request->input('inventory_id');
    $inventory = Inventory::find($inventoryId);
    if (!$inventory) {
        return '<div class="text-muted">Nincs találat.</div>';
    }

    $html = '<div class="row gallery-images">';

    // 1. Parent pictures (if parent exists)
    $parentInventoryId = $inventory->item_data['parent_inventory_id'] ?? null;
    if ($parentInventoryId) {
        $parentInventory = Inventory::with('inventoryPictures')->find($parentInventoryId);
        if ($parentInventory && $parentInventory->inventoryPictures->count()) {
            foreach ($parentInventory->inventoryPictures as $img) {
                $url = env('PUBLIC_URL') . '/document_storage/inventory_images/' . $parentInventory->inventory_id . '/' . $img->image_path;
                $html .= $this->buildImageHtml($url, $img->inventory_picture_id, false);
            }
        }
    }

    // 2. Current inventory pictures (with delete buttons)
    $inventory->load('inventoryPictures');
    foreach ($inventory->inventoryPictures as $img) {
        $url = env('PUBLIC_URL') . '/document_storage/inventory_images/' . $inventory->inventory_id . '/' . $img->image_path;
        $html .= $this->buildImageHtml($url, $img->inventory_picture_id, true);
    }

    $html .= '</div>';
    return $html;
}

/**
 * Helper to generate HTML for a single gallery image.
 */
private function buildImageHtml($imageUrl, $pictureId, $allowDelete = true)
{
    $deleteButton = '';
    if ($allowDelete) {
        $deleteButton = '<button type="button" class="btn btn-sm btn-danger position-absolute delete_inventory_image" style="top:8px; right:8px;" data-img_id="' . $pictureId . '">
                            <i class="fa fa-times"></i>
                        </button>';
    }
    return '<div class="col-md-3 mb-3">
                <div class="card">
                    <div class="position-relative">
                        <img src="' . $imageUrl . '" class="card-img-top img-fluid" alt="Kép">
                        ' . $deleteButton . '
                    </div>
                </div>
            </div>';
}
    public function deleteInventoryImage(Request $request)
    {
        $request->validate(['img_id' => 'required|integer']);
        $image = InventoryPicture::findOrFail($request->input('img_id'));
        $filePath = public_path('/document_storage/inventory_images/' . $image->inventory_id . '/' . $image->image_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        $image->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Helper function to get unit price (if you need it)
     */
    private function getUnitPrice($businessId, $productId)
    {
        // Implement your price calculation logic here
        // This is just a placeholder
        return DB::table('product_prices')
            ->where('business_id', $businessId)
            ->where('product_id', $productId)
            ->value('price') ?? 0;
    }
    //old
    public function get_unit_price_by_business_product($business_id,$product_id)
    {
        $product = Product::find($product_id);
        $business = Business::find($business_id);
        $product_prices = DB::table('product_prices')
        ->where('product_id', $product_id)
        ->where('business_id', $business_id)
        ->where('date_from', '<=', date('Y-m-d'))
        ->orderBy('date_from', 'desc')
        ->first();

        //Árrés megállapítás ha van
        if($business && $business->discount_type){
            $discount_value = $business->discount_value;
        }else{
            $discount_value = 0;
        }
        $weight = $product->weight ?? 1;
        if($product_prices){
            $act_price = round($product_prices->price);
            if($business->discount_type){
                if($business->discount_type == 1){
                    //fix árrés
                    $act_price = $act_price + $discount_value;
                }else{
                    // %-os árrés
                    $act_price = $act_price + ($act_price * $discount_value / 100);
                }
            }
            return $act_price;
        }else{
            $act_price = round($product->real_price);
            if($business && $business->discount_type){
                if($business->discount_type == 1){
                    //fix árrés
                    $act_price = $act_price + $discount_value;
                }else{
                    // %-os árrés
                    $act_price = $act_price + ($act_price * $discount_value / 100);
                }
            }
            return $act_price;
        }

    }

    public function group_pricing(Request $request)
    {
        $itemsData = $request->input('items');

        $tr = '';
        foreach ($itemsData as $item) {
            $product = Product::find($item['item_id']);
            $business = Business::find($item['business_id']);
            $warehouse = Warehouse::find($item['warehouse_id']);
            $tr .= '<tr>
                <td>'.$product->name.'</td>
                <td>'.number_format($this->get_unit_price_by_business_product($business->business_id,$product->product_id),0,""," ").' Ft</td>
                <td><input type="number" class="form-control" id="new_price_"'.$product->product_id.'_'.$warehouse->warehouse_id.'/></td>
            </tr>';
        }

        return response()->json(['success' => true, 'message' => 'Good', 'tr' => $tr]);
    }

    public function get_warehouses_transfer_from_exclude_this(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|integer'
        ]);

        $warehouses = Warehouse::where('warehouse_id', '!=', $request->warehouse_id)
            ->get();

        $html = '';
        foreach ($warehouses as $warehouse) {
            $html .= '<option value="' . $warehouse->warehouse_id . '">' . $warehouse->name . '</option>';
        }

        return response()->json(['html' => $html]);
    }

    //get_items_by_warehouse_id
    public function get_items_by_warehouse_id(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|integer'
        ]);

        $stock = $this->get_actual_stock($request->warehouse_id);
        
        $html = '<option value="">-- Válasszon --</option>';
        foreach ($stock as $item) {
            $html .= '<option data-prodname="'.$item['product_name'].'" data-description="'.$item['short_description'].'" 
            value="' . $item['product_id'] . '">' . $item['product_name'].' - '.$item['code'] .' / '. $item['stock'] . ' db)</option>';
        }

        return response()->json(['html' => $html]);
    }
public function getInventoryItemsByWarehouse(Request $request)
{
    $warehouseId = $request->warehouse_id;
    $search = $request->input('search', '');
    $searchInventoryId = null;

    if (!empty($search) && preg_match('/PRD-\s*(\d+)/i', $search, $matches)) {
        $searchInventoryId = (int) $matches[1];
    }

    if (!$warehouseId) {
        return response()->json(['results' => []]);
    }

    $query = Inventory::where('storage_id', $warehouseId)
        ->where('status', InventoryStatus::IN_STOCK->value)
        ->where('quantity', '>', 0);

    if (!is_null($searchInventoryId)) {
        $query->where('inventory_id', $searchInventoryId);
    } elseif (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
            });
    }

    $items = $query->limit(20)->get();

    $results = [];
    foreach ($items as $item) {
        $itemData = $item->item_data;
        if (is_string($itemData)) {
            $itemData = json_decode($itemData, true);
        }
        if (!is_array($itemData)) {
            $itemData = [];
        }

        $termekTipusId = $itemData['termek_tipus_id'] ?? null;
        $name = 'PRD-' . $item->inventory_id . ' - ' . \App\Enums\ProductTipus::getName($termekTipusId);
        $code = $item->code ?? '';
        //$description = $item->description ?? '';
        $description = '';
        $stock = $item->quantity;
        $weight = $item->weight;
        $results[] = [
            'id' => $item->inventory_id,
            'termek_tipus_id' => $termekTipusId,
            'text' => "{$name} - {$stock} db",
            'prodname' => "{$name}",
            'description' => $description,
            'weight' => $weight,
            'stock' => $stock,
        ];
    }

    return response()->json(['results' => $results]);
}
    public function getInventoryItemsByWarehouseOld(Request $request)
    {   
        $warehouseId = $request->warehouse_id;
        if (!$warehouseId) {
            return response()->json(['html' => '<option value="">-- Válasszon --</option>']);
        }

        $items = Inventory::where('storage_id', $warehouseId)
            ->where('status', InventoryStatus::IN_STOCK->value) // csak raktáron lévők
            ->where('quantity', '>', 0)
            ->with('product')             // betöltjük a termék adatait
            ->get();

        $html = '<option value="">-- Válasszon --</option>';
        foreach ($items as $item) {
            $product = $item->product;
            $name = $product->name ?? 'Ismeretlen';
            $description = $item->description ?? '';
            $stock = $item->quantity;
            $weight = $item->weight;
            $html .= sprintf(
                '<option value="%d" data-prodname="%s" data-description="%s" data-weight="%s" data-stock="%d">%s (%d db)</option>',
                $item->inventory_id,
                e($name),
                e($description),
                $weight,
                $stock,
                e($name),
                e($description),
                $stock
            );
        }

        return response()->json(['html' => $html]);
    }

    private function getNextSerialDeliveryNote()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'delivery_note')
            ->where('year', date('Y'))
            ->first();

        $serial = $record ? $record->serial_number : null;

        if (!$serial) {
            DB::table('serial_numbers')
                ->insert(['module' => 'delivery_note', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }

        $next = ($serial+1);

        DB::table('serial_numbers')
            ->where('module', 'delivery_note')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);

        return $serial;
    }

    /**
    * Get actual stock for a specific product
    */
    public function get_actual_stock_by_product($product_id = null)
    {
        try {
            // Validate product_id
            if (!$product_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product ID is required'
                ], 400);
            }

            // Check if product exists
            $product = Product::find($product_id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // Get warehouse_id from request if provided (optional)
            $warehouse_id = request()->get('warehouse_id');
            $non_accepted_items = request()->get('non_accepted_items', false);

            // Call your existing method with the product_id
            $stockData = $this->get_actual_stock($warehouse_id, $non_accepted_items, $product_id);

            // If no stock found, return empty array
            if (empty($stockData)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No stock found for this product'
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $stockData,
                'product' => [
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'brand' => $product->brand
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stock data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Alternative: Modify your existing method to also accept POST/GET with query parameters
    public function get_actual_stock_json(Request $request)
    {
        $warehouse_id = $request->get('warehouse_id');
        $product_id = $request->get('product_id');
        $non_accepted_items = $request->get('non_accepted_items', false);

        $stockData = $this->get_actual_stock($warehouse_id, $non_accepted_items, $product_id);

        return response()->json([
            'success' => true,
            'data' => $stockData
        ]);
    }

}
