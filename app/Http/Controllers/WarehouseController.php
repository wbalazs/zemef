<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Warehouse;
use App\Models\Business;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\WarehouseRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use PDF;



class WarehouseController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Warehouse::class);
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Warehouse $model)
    {
        Warehouse::where('name', '--empty_row--')->delete();
        return view('warehouses.index', ['warehouses' => $model->get()]);
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
        $warehouse = Warehouse::create(['name' => '--empty_row--']);
        $warehouse->name = "";
        $exist = false;

        return view('warehouses.edit', [
            'warehouse' => $warehouse,
            'exist' => $exist
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\ToolRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(WarehouseRequest $request, Warehouse $model)
    {
        $warehouse = $model->create($request->merge([
        ])->all());

        return redirect()->route('warehouse.index')->withStatus(['A raktár sikeresen létrehozva.']);
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $tool
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Warehouse $warehouse)
    {
        $exist = $this->checkWarehouseExists($warehouse->id);

        return view('warehouses.edit', [
            'warehouse' => $warehouse,
            //'exist' => $exist
            'exist' => 0
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(WarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update(
            array_merge($request->all(),
                ['status' => $request->status, 
                'is_central' => $request->is_central, 
                'is_receiving' => $request->is_receiving, 
                'is_store' => $request->is_store, 
                'is_goldsmith' => $request->is_goldsmith, 
                'is_fake' => $request->is_fake, 
                'is_hallmark' => $request->is_hallmark,
                'is_broken_gold' => $request->is_broken_gold,
                'is_broken_silver' => $request->is_broken_silver,
                'is_repair' => $request->is_repair, 
                ]
            )
        );

        return redirect()->route('warehouse.index')->withMessage(['A raktár sikeresen mentve.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Warehouse $warehouse)
    {
        try {
            $warehouse->delete();
            return redirect()->route('warehouse.index')->withMessage(['A raktár sikeresen törölve.']);
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('warehouse.index')->withErrors(['Nem lehet törölni a raktárat, mert kapcsolódó adatok vannak.']);
        }
    }

    public function checkWarehouseExists($warehouseId)
    {
        $existsInWarehouseTransfer = DB::table('warehouse_transfer_items')
            ->where('warehouse_from', $warehouseId)
            ->orWhere('warehouse_to', $warehouseId)
            ->exists();

        $existsInDeliveryNote = DB::table('delivery_note_items')
            ->where('warehouse_from', $warehouseId)
            ->orWhere('warehouse_to', $warehouseId)
            ->exists();

        $existsInPurchaseTicket = DB::table('purchase_ticket_items')
            ->where('warehouse_from', $warehouseId)
            ->orWhere('warehouse_to', $warehouseId)
            ->exists();

        $exists = $existsInWarehouseTransfer || $existsInDeliveryNote || $existsInPurchaseTicket;

        return $exists;
    }



   
}
