<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Enums\InventoryStatus;
use App\Models\Tool;
use App\Models\Inventory;
use App\Models\ToolType;
use App\Models\DocType;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\PurchaseTicket;
use App\Models\PurchaseTicketItem;
use App\User;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\WarehouseTransferRequest;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Controllers\StockController;
use DB;
use Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;




class WarehouseTransferController extends Controller
{
    protected $stockController;

    public function __construct()
    {
        ////$this->authorizeResource(WarehouseTransfer::class);
        $this->stockController = new StockController();
    }

   public function warehouse_transfer(Request $request)
{
    $data = $request->all();
    $warehouse_from = Warehouse::findOrFail($data['from']);
    $warehouse_to = Warehouse::findOrFail($data['to']);
    $business = $warehouse_from->businesses->first();
    $validate = $this->sign_code_check($data['sign_code']);
    
    if (!$validate['isvalidcode']) {
        return response()->json(['error' => 'error', 'message' => 'Hibás aláírókód!']);
    }

    $user = User::find($validate["user"]["id"]);
    if (!$user) {
        return response()->json(['error' => 'error', 'message' => 'Nincs ilyen felhasználó!']);
    }
    $creator = $user->employee_id;
    $creator_employee = $user->employee;

    // Ellenőrizzük a készletet és gyűjtjük a tételeket
    $itemsToTransfer = [];
    foreach ($data['items'] as $item) {
        $inventory = Inventory::findOrFail($item['inventory_id']);
        if ($inventory->storage_id != $warehouse_from->warehouse_id) {
            return response()->json(['error' => 'error', 'message' => 'A tétel nem a megadott raktárban található!']);
        }
        if ($inventory->quantity < $item['quantity']) {
            return response()->json(['error' => 'error', 'message' => 'Nincs elegendő mennyiség a tételből!']);
        }
        $itemsToTransfer[] = [
            'inventory' => $inventory,
            'quantity' => $item['quantity'],
            'note' => $item['transfer_items_note'] ?? null
        ];
    }

    // Speciális raktárak (fémjelzés, javítás) esetén szállítólevelet kell készíteni
    if ($warehouse_to->is_hallmark || $warehouse_from->is_hallmark || $warehouse_to->is_repair || $warehouse_from->is_repair) {
        // Szállítólevél létrehozása
        $dn = $this->create_delivery_note(
            $warehouse_from->warehouse_id,
            $warehouse_to->warehouse_id,
            $creator,
            $business->business_id,
            null
        );
        foreach ($itemsToTransfer as $item) {
            $this->create_delivery_note_item(
                $dn,
                $item['inventory']->inventory_id,
                $item['quantity'],
                $warehouse_from->warehouse_id,
                $warehouse_to->warehouse_id,
                $item['inventory']->price  // opcionális ár
            );
        }
        
        return response()->json(['success' => 'success', 'delivery_note_id' => $dn]);
    }

    // Normál raktárközi átadás
    DB::beginTransaction();
    try {
        // Átadási bizonylat létrehozása
        $serial = $this->getNextSerial();
        $transfer_number = 'RKOZ/' . $business->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        $warehouseTransfer = WarehouseTransfer::create([
            'warehouse_transfer_number' => $transfer_number,
            'employee_id' => $creator,
            'employee_name' => $creator_employee->lastname . ' ' . $creator_employee->firstname,
            'business_id' => $business->business_id,
            'business_name' => $business->name,
            'business_address' => $business->zip . ' ' . $business->city . ', ' . $business->address,
            'warehouse_from' => $warehouse_from->warehouse_id,
            'warehouse_from_name' => $warehouse_from->name,
            'warehouse_to' => $warehouse_to->warehouse_id,
            'warehouse_to_name' => $warehouse_to->name,
        ]);

        foreach ($itemsToTransfer as $item) {
            $inventory = $item['inventory'];
            $quantity = $item['quantity'];

            // Ha a teljes mennyiséget adjuk át
            if ($inventory->quantity == $quantity) {
                // Frissítjük az utolsó átasás raktárakat
                $inventory->warehouse_from = $warehouse_from->warehouse_id;
                $inventory->warehouse_to = $warehouse_to->warehouse_id;
                $inventory->storage_id = $warehouse_from->warehouse_id;
                // Speciális státusz beállítása, ha a cél raktár javító vagy fémjelző
                if ($warehouse_to->is_repair) {
                    $inventory->status = InventoryStatus::UNDER_REPAIR->value;
                } elseif ($warehouse_to->is_hallmark) {
                    $inventory->status = InventoryStatus::HALLMARK_IN_PROGRESS->value;
                } else {
                    $inventory->status = InventoryStatus::WAREHOUSE_TRANSFER->value;
                }
                $inventory->save();
            } else {
                // Részleges átadás: az eredeti tétel mennyiségét csökkentjük, és új tételt hozunk létre
                $inventory->quantity -= $quantity;
                $inventory->save();

                $newInventory = $inventory->replicate();
                $newInventory->quantity = $quantity;
                $newInventory->warehouse_from = $warehouse_from->warehouse_id;
                $newInventory->warehouse_to = $warehouse_to->warehouse_id;
                if ($warehouse_to->is_repair) {
                    $newInventory->status = InventoryStatus::UNDER_REPAIR->value;
                } elseif ($warehouse_to->is_hallmark) {
                    $newInventory->status = InventoryStatus::HALLMARK_IN_PROGRESS->value;
                } else {
                    $newInventory->status = InventoryStatus::WAREHOUSE_TRANSFER->value;
                }
                $newInventory->save();
                // A továbbiakban a mozgás rögzítéséhez az új inventory ID-t használjuk
                $inventory = $newInventory;
            }

            // Raktárközi tétel rögzítése
            WarehouseTransferItem::create([
                'warehouse_transfer_id' => $warehouseTransfer->warehouse_transfer_id,
                'inventory_item_id' => $inventory->inventory_id,
                'quantity' => $quantity,
                'warehouse_from' => $warehouse_from->warehouse_id,
                'warehouse_to' => $warehouse_to->warehouse_id,
                'note' => $item['note'],
                'is_repair' => ($warehouse_to->is_repair || $warehouse_from->is_repair) ? 1 : 0,
            ]);
        }

        // Képek mentése (eredeti kód)
       // Képek és dokumentumok kezelése
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $file) {
                        if ($file->getMimeType() === "image/jpeg" || $file->getMimeType() === "image/png" || $file->getMimeType() === "image/gif") {
                            $path = public_path('/document_storage/warehouse_transfer_images/' . $warehouseTransfer->warehouse_transfer_id . '/');
                        } else {
                            $path = public_path('/document_storage/warehouse_transfer_docs/' . $warehouseTransfer->warehouse_transfer_id . '/');
                        }
                        if(!File::isDirectory($path)){
                            File::makeDirectory($path, 0777, true, true);
                        }

                        // A fájl nevének megszerzése és egyedivé tétele
                        $fileName = $file->getClientOriginalName();
                        $uniqueName = time() . '_' . $fileName;
                        $finalPath = "{$path}/{$uniqueName}";

                        if ($file->getMimeType() === "image/jpeg" || $file->getMimeType() === "image/png" || $file->getMimeType() === "image/gif") {
                            $image_resize = Image::make($file->getRealPath());
                            $image_resize->save($path . $uniqueName);

                            // Létrehozunk egy új rekordot az images táblában
                            DB::table('images')->insert([
                                'module' => 'warehouse_transfer',
                                'resource_id' => $warehouseTransfer->warehouse_transfer_id,
                                'image_path' => "{$warehouseTransfer->warehouse_transfer_id}/{$uniqueName}",
                                'image_date' => now(),
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        } else {
                            // Létrehozunk egy új rekordot a documents táblában
                            DB::table('documents')->insert([
                                'resource_id' => $warehouseTransfer->warehouse_transfer_id,
                                'document_path' => "{$warehouseTransfer->warehouse_transfer_id}/{$uniqueName}",
                                'document_comment' => $fileName.' - '.now(),
                                'document_type_id' => null,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'document_module' => 'warehouse_transfer_doc',
                                'doc_issue_date' => null,
                                'doc_expire_date' => null,
                                'doc_reminder_days' => null
                            ]);

                            // Fájl mentése a szerverre
                            $file->move($path, $uniqueName);
                        }
                    }
                }

        DB::commit();
        return response()->json(['success' => 'success', 'warehouse_transfer_id' => $warehouseTransfer->warehouse_transfer_id]);

    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['error' => 'error', 'message' => 'Hiba történt az átadás közben: ' . $e->getMessage()]);
    }
}
    // StockController.php

    public function getInventoryItemsByWarehouse(Request $request)
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
                //e($description),
                $stock
            );
        }

        return response()->json(['html' => $html]);
    }

    public function sign_code_check($sign_code)
    {
        $users = User::all();
        $isValidCode = false;
        foreach ($users as $user) {
            if ($sign_code === $user->sign_code) {
                $isValidCode = true;
                $data["user"] = User::find($user->id);
                break;
            }
        }
        $data["isvalidcode"] = $isValidCode;
        return $data;
    }


    //accept items ajax
    public function accept_items(Request $request)
    {
        $warehouse_transfer_ids = $request->warehouse_transfer_ids;
        
        foreach ($warehouse_transfer_ids as $item) {
            $warehouse_transfer_item = WarehouseTransfer::find($item);
            $warehouse_transfer_item->accepted_at = date('Y-m-d H:i:s');
            $warehouse_transfer_item->accepted_employee_id = Auth::user()->employee?->employee_id;
            $warehouse_transfer_item->save();
        }

        return response()->json(['success' => 'success']);
    }


    //Ötvös termék visszaadása
    public function return_item_old(Request $request)
    {
        $transfer_items_id = $request->transfer_items_id;
        $warehouse_transfer_item = WarehouseTransferItem::find($transfer_items_id);
        $warehouse_from = Warehouse::find($warehouse_transfer_item->transfer_items_warehouse_to);
        $warehouse_to = Warehouse::find($warehouse_transfer_item->transfer_items_warehouse_from);
        $quantity = $warehouse_transfer_item->transfer_items_product_quantity;

        if($warehouse_transfer_item->back_to_storage_at != null){
            return response()->json(['data' => ['error' => 'Ez a termék már vissza lett adva!']], 200);
        }


        //Készlet ellenőrzése az átadó raktár alapján
        $stock = collect($this->stockController->get_actual_stock($warehouse_from->warehouse_id));
        $stock_item = $stock->where('product_id', $warehouse_transfer_item->transfer_items_product_id)->first();
        if (!$stock_item || $stock_item['stock'] < $quantity) {
            return response()->json(['data' => ['error' => 'Ez a termék már nincs az adott raktárban!']], 200);
        }

        $product = Product::find($warehouse_transfer_item->transfer_items_product_id);
        $creator = Auth::user()->employee?->employee_id;
        $creator_employee = Employee::find($creator);
        $business = $warehouse_from->businesses->first();
        $serial = $this->getNextSerial();
        $transfer_number = 'RKOZ/' . $business->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);

        DB::beginTransaction();

        try {
            //Raktárközi átadás létrehozása
            $warehouseTransfer = new WarehouseTransfer();
            $warehouseTransfer->warehouse_transfer_number = $transfer_number;
            $warehouseTransfer->employee_id = $creator;
            $warehouseTransfer->employee_name = $creator_employee->lastname.' '.$creator_employee->firstname ?? null;
            $warehouseTransfer->business_id = $business->business_id;
            $warehouseTransfer->business_name = $business->name;
            $warehouseTransfer->business_address = $business->zip.' '.$business->city.', '.$business->address.' '.$business->floor.' '.$business->door ?? null;
            $warehouseTransfer->warehouse_from = $warehouse_from->warehouse_id;
            $warehouseTransfer->warehouse_from_name = $warehouse_from->name;
            $warehouseTransfer->warehouse_to = $warehouse_to->warehouse_id;
            $warehouseTransfer->warehouse_to_name = $warehouse_to->name;
            $warehouseTransfer->save();

            //Termékek hozzáadása a warehouse_transfer_items_táblához
            $transferItem = new WarehouseTransferItem();
            $transferItem->warehouse_transfer_id = $warehouseTransfer->warehouse_transfer_id;
            $transferItem->transfer_items_product_id = $warehouse_transfer_item->transfer_items_product_id;
            $transferItem->transfer_items_product_quantity = $quantity;
            $transferItem->transfer_items_product_name = $product->name;
            $transferItem->transfer_items_product_description = $product->description;
            $transferItem->transfer_items_product_factory_number = $product->factory_number;
            $transferItem->transfer_items_product_weight = $product->weight;
            $transferItem->transfer_items_product_price = $product->price;
            $transferItem->transfer_items_warehouse_from = $warehouse_from->warehouse_id;
            $transferItem->transfer_items_warehouse_to = $warehouse_to->warehouse_id;
            if($warehouse_from->is_goldsmith == '1' || $warehouse_from->is_repair == '1'){
                $old_transfer_item = WarehouseTransferItem::where('transfer_items_product_id', $warehouse_transfer_item->transfer_items_product_id)
                    ->where('is_repair', 1)
                    ->where('transfer_items_warehouse_to', $warehouse_from->warehouse_id)
                    ->first();
                $old_transfer_item->is_repair = '0';
                $old_transfer_item->repaired_at = now();
                $old_transfer_item->save();
            }
            $transferItem->repaired_at = now();
            $transferItem->save();

            //Átadott termék visszaadása időbélyeggel
            $old_transfer_item = WarehouseTransferItem::find($transfer_items_id);
            $old_transfer_item->back_to_storage_at = date('Y-m-d H:i:s');
            $old_transfer_item->save();

            DB::commit();

            return response()->json(['data' =>['success' => 'success', 'warehouse_transfer_id' => $warehouseTransfer->warehouse_transfer_id]],200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' =>['error' => 'error', 'message' => 'Hiba történt:'.$e->getMessage()]],200);
        }
    }
    /**
     * Ötvös termék visszaadása (visszaszállítás az eredeti raktárba)
     */
    public function return_item(Request $request)
    {
        $transfer_item_id = $request->transfer_items_id;
        $originalTransferItem = WarehouseTransferItem::findOrFail($transfer_item_id);

        // Ellenőrizzük, hogy nem lett-e már visszaadva
        if ($originalTransferItem->back_to_storage_at !== null) {
            return response()->json(['data' => ['error' => 'Ez a termék már vissza lett adva!']], 200);
        }

        $inventory = Inventory::findOrFail($originalTransferItem->inventory_item_id);
        $fromWarehouse = Warehouse::findOrFail($originalTransferItem->warehouse_to);   // ahol most van (pl. ötvös)
        $toWarehouse = Warehouse::findOrFail($originalTransferItem->warehouse_from);    // hová vissza kell adni

        // (Opcionális) Készletellenőrzés – mivel egyedi tételről van szó, elég ha az inventory létezik
        // Itt már nincs szükség a get_actual_stock hívására, mert az inventory rekord egyedisége garantált.

        $creator = Auth::user()->employee_id;
        $creatorEmployee = Employee::find($creator);
        $business = $toWarehouse->businesses->first(); // az eredeti raktár üzlete

        $serial = $this->getNextSerial();
        $transfer_number = 'RKOZ/' . $business->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);

        DB::beginTransaction();
        try {
            // Visszaadó bizonylat létrehozása
            $returnTransfer = WarehouseTransfer::create([
                'warehouse_transfer_number' => $transfer_number,
                'employee_id' => $creator,
                'employee_name' => $creatorEmployee->lastname . ' ' . $creatorEmployee->firstname,
                'business_id' => $business->business_id,
                'business_name' => $business->name,
                'business_address' => $business->zip . ' ' . $business->city . ', ' . $business->address,
                'warehouse_from' => $fromWarehouse->warehouse_id,
                'warehouse_from_name' => $fromWarehouse->name,
                'warehouse_to' => $toWarehouse->warehouse_id,
                'warehouse_to_name' => $toWarehouse->name,
            ]);

            // Visszaadó tétel rögzítése
            WarehouseTransferItem::create([
                'warehouse_transfer_id' => $returnTransfer->warehouse_transfer_id,
                'inventory_item_id' => $inventory->inventory_id,
                'quantity' => $originalTransferItem->quantity,
                'warehouse_from' => $fromWarehouse->warehouse_id,
                'warehouse_to' => $toWarehouse->warehouse_id,
                'note' => 'Visszaadás (javítás/fémjelzés után)',
                'is_repair' => 0,
            ]);

            // Inventory rekord frissítése – vissza az eredeti raktárba
            $inventory->storage_id = $toWarehouse->warehouse_id;
            $inventory->status = InventoryStatus::IN_STOCK->value;
            $inventory->save();

            // Eredeti tétel megjelölése visszaadottként
            $originalTransferItem->back_to_storage_at = now();
            $originalTransferItem->save();

            DB::commit();

            return response()->json(['data' => [
                'success' => 'success',
                'warehouse_transfer_id' => $returnTransfer->warehouse_transfer_id
            ]], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' => [
                'error' => 'error',
                'message' => 'Hiba történt a visszaadás közben: ' . $e->getMessage()
            ]], 200);
        }
    }
    //get_warehouse_transfer_images ajax
    public function get_warehouse_transfer_images(Request $request)
    {
        $warehouse_transfer_id = $request->warehouse_transfer_id;
        $images = DB::table('images')
            ->where('module', 'warehouse_transfer')
            ->where('resource_id', $warehouse_transfer_id)
            ->get();

        return response()->json(['success' => 'success', 'images' => $images]);
    }

    //get_warehouse_transfer_files ajax
    public function get_warehouse_transfer_files(Request $request)
    {
        $warehouse_transfer_id = $request->warehouse_transfer_id;
        $files = DB::table('documents')
            ->where('document_module', 'warehouse_transfer_doc')
            ->where('resource_id', $warehouse_transfer_id)
            ->get();

        return response()->json(['success' => 'success', 'files' => $files]);
    }

    public function printWarehouseTransfer(Request $request)
    {
        //clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/warehouse_transfers/*'));

        $file_name = 'warehouse_transfer.pdf';

        $pdf = \App::make('dompdf.wrapper');

        $data = array();
        $warehouse_transfer = WarehouseTransfer::find($request->warehouse_transfer_id);

        $warehouse_transfer->printed_at = date('Y-m-d H:i:s');
        $warehouse_transfer->save();

        $data['warehouse_transfer'] = $warehouse_transfer;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.warehouse_transfer', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save(public_path() . '/pdf/warehouse_transfers/' . $file_name);

        return $file_name;
    }

    //Szállítólevél fejléc elkészítése
    public function create_delivery_note($warehouse_from,$warehouse_to,$employee_id,$business_id,$customer_id)
    {
        $warehouse_from_name = Warehouse::find($warehouse_from)->name;
        $warehouse_to_name = Warehouse::find($warehouse_to)->name;
        $business = Business::find($business_id);
        $employee = Employee::find($employee_id);
        $customer = Customer::find($customer_id);
        $serial = $this->getNextSerialDeliveryNote();
        // Formátum: XX/EEEE/00001 - ahol XX - bolti azonosító
        $note_number = 'SZALL/'. $business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);

        $delivery_note = new DeliveryNote();
        $delivery_note->delivery_note_number = $note_number;
        $delivery_note->employee_id = $employee_id;
        $delivery_note->employee_name = $employee->lastname.' '.$employee->firstname ?? null;
        $delivery_note->business_id = $business_id;
        $delivery_note->business_name = $business->name ?? null;
        $delivery_note->business_address = 
                $business->zip.' '.$business->city.', '.$business->address.' '.$business->floor.' '.$business->door ?? null;

        $delivery_note->customer_id = $customer->customer_id ?? null;
        $delivery_note->customer_name = $customer->name ?? null;
        $delivery_note->received_at = date('Y-m-d H:i:s');
        $delivery_note->receive_employee_id = $employee_id;
        $delivery_note->receive_employee_name = $employee->lastname.' '.$employee->firstname ?? null;
        $delivery_note->warehouse_to = $warehouse_to;
        $delivery_note->warehouse_from = $warehouse_from;
        $delivery_note->warehouse_from_name = $warehouse_from_name ?? null;
        $delivery_note->warehouse_to_name = $warehouse_to_name ?? null;
        $delivery_note->save();

        return $delivery_note->delivery_note_id;
    }

    //Szállítólevél tételek hozzáadása
    private function create_delivery_note_item($delivery_note_id, $inventory_item_id, $quantity, $warehouse_from_id, $warehouse_to_id, $price = null)
    {
        $inventory = Inventory::findOrFail($inventory_item_id);

        DeliveryNoteItem::create([
            'delivery_note_id' => $delivery_note_id,
            'inventory_item_id' => $inventory_item_id,
            'product_name' => $inventory->name,
            'product_description' => $inventory->description,
            'factory_number' => $inventory->factory_number,
            'weight' => $inventory->weight,
            'quantity' => $quantity,
            'price' => $price ?? $inventory->price,
            'warehouse_from' => $warehouse_from_id,
            'warehouse_to' => $warehouse_to_id,
            'accepted_employee_id' => Auth::user()->employee?->employee_id,
            'accepted_at' => now(),
        ]);

        $inventory->status = InventoryStatus::DELIVERY_NOTE->value;
        $inventory->save();
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

    private function getNextSerial()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'warehouse_transfer')
            ->where('year', date('Y'))
            ->first();

        $serial = $record ? $record->serial_number : null;

        if (!$serial) {
            DB::table('serial_numbers')
                ->insert(['module' => 'warehouse_transfer', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }

        $next = ($serial+1);

        DB::table('serial_numbers')
            ->where('module', 'warehouse_transfer')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);

        return $serial;
    }
}
