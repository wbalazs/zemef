<?php

namespace App\Http\Controllers;

use App\Tag;
use App\User;
use App\Models\Tool;
use App\Models\ToolType;
use App\Models\DocType;
use App\Models\Business;
use App\Models\Inventory;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\PurchaseTicket;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Enums\InventoryStatus;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\DeliveryNoteRequest;
use Illuminate\Http\Request;
use DB;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Traits\ReceiptPrintTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;




class DeliveryNoteController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(DeliveryNote::class);
    }

    //transfer_items ajax
    public function transfer_items(Request $request)
    {
        $data = $request->all();
        
        // Duplikáció elleni védelem: ellenőrizzük, hogy az összes jegy már át lett-e adva.
        foreach ($request->ticketNumbers as $ticketNumber) {
            $purchase_ticket = PurchaseTicket::find($ticketNumber);
            if (!$purchase_ticket) {
                return response()->json(['data' => ['success' => 'error','message' => 'A vételi jegy nem található: ' . $ticketNumber]], 200);
            }

            if ($purchase_ticket->wait_to_transfer == 0) {
                return response()->json(['data' => ['success' => 'error','message' => 'A vételi jegy már át lett adva.']], 200);
            }
        }

        if($request->transferMethod == 'personal'){
            if($request->personalSelect == 'self'){
                $ownsigcode = $request->ownSignCode;
                $validate = $this->sign_code_check($ownsigcode);
                if(!$validate["isvalidcode"]){
                    return response()->json(['data' => ['success' => 'error','message' => 'Hibás aláíró kód!']], 200);
                }
            }else{
                $ownSignCode = $request->ownSignCode;
                $transferSignCode = $request->transferSignCode;
                $validate = $this->sign_code_check($ownSignCode);
                $validate2 = $this->sign_code_check($transferSignCode);
                if(!$validate["isvalidcode"] || !$validate2["isvalidcode"]){
                    return response()->json(['data' => ['success' => 'error','message' => 'Hibás aláíró kód!']], 200);
                }
            }  
        }else{
            $courierSignCode = $request->courierSignCode;
            $validate = $this->sign_code_check($courierSignCode);
            if(!$validate["isvalidcode"]){
                return response()->json(['data' => ['success' => 'error','message' => 'Hibás aláíró kód!']], 200);
            }
        }

        $serial = $this->getNextSerial();

        $purchase_ticket = PurchaseTicket::find($request->ticketNumbers[0]);
        $targetWarehouseId = (int) $request->input('warehouse_to');
        $targetWarehouse = Warehouse::find($targetWarehouseId);

        if (!$targetWarehouse) {
            return response()->json(['data' => ['success' => 'error', 'message' => 'A kiválasztott célraktár nem található.']], 200);
        }

        $business = Business::find($purchase_ticket->business_id);
        $customer = Customer::find($purchase_ticket->customer_id);
        //Added 2024.01.12
        $creator_search = User::find($validate["user"]["id"]);
        $creator = $creator_search->employee_id;
        $creator_employee = Employee::find($creator);
        //Added 2024.01.12
        if($request->transferMethod == 'personal'){
            if($request->personalSelect != 'self'){
                $receiver_search = User::find($validate2["user"]["id"]);
                $receiver = $receiver_search->employee_id;
                $receiver_employee = Employee::find($receiver);
            }
        }
        
        // Formátum: XX/EEEE/00001 - ahol XX - bolti azonosító
        $note_number = 'SZALL/'. $purchase_ticket->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);

        $delivery_note = new DeliveryNote();
        $delivery_note->delivery_note_number = $note_number;
        $delivery_note->shipping_label = $request->courierTrackingNumber;
        $delivery_note->shipping_date = $request->shippingDate ?? null;
        $delivery_note->employee_id = $creator;
        $delivery_note->employee_name = $creator_employee->lastname.' '.$creator_employee->firstname ?? null;
        $delivery_note->business_id = $purchase_ticket->business_id;
        $delivery_note->business_name = $business->name ?? null;
        $delivery_note->business_address = 
                $business->zip.' '.$business->city.', '.$business->address.' '.$business->floor.' '.$business->door ?? null;

        $delivery_note->customer_id = $purchase_ticket->customer_id;
        $delivery_note->customer_name = $customer->name ?? null;
        $delivery_note->received_at = date('Y-m-d H:i:s');
        if($request->transferMethod == 'personal'){
            if($request->personalSelect != 'self'){
                $delivery_note->receive_employee_id = $receiver;
                $delivery_note->receive_employee_name = $receiver_employee->lastname.' '.$receiver_employee->firstname ?? null;
            }
        }
        $delivery_note->warehouse_to = $targetWarehouseId;
        $delivery_note->warehouse_to_name = $targetWarehouse->name ?? null;
        $delivery_note->warehouse_from = Warehouse::find($purchase_ticket->storage_id)->warehouse_id ?? null;
        $delivery_note->warehouse_from_name = Warehouse::find($purchase_ticket->storage_id)->name ?? null;
        $delivery_note->save();

        $delivery_note_id = $delivery_note->delivery_note_id;
        foreach ($request->ticketNumbers as $ticketNumber) {
            $purchase_ticket = PurchaseTicket::find($ticketNumber);
            $inventoryItems = Inventory::where('source_type', 'purchase')
                ->where('source_header_id', $ticketNumber)
                ->get();
            
            foreach ($inventoryItems as $item) {
                $delivery_note_item = new DeliveryNoteItem();
                $delivery_note_item->delivery_note_id = $delivery_note_id;
                $delivery_note_item->inventory_item_id = $item->inventory_id;
                $delivery_note_item->product_name = $item->product?->name ?? $item->name;
                $delivery_note_item->product_description = $item->description;
                $delivery_note_item->factory_number = $item->factory_number;
                $delivery_note_item->weight = $item->weight;
                $delivery_note_item->quantity = $item->quantity;
                $delivery_note_item->price = $item->price;
                $delivery_note_item->warehouse_from = $item->storage_id;
                $delivery_note_item->warehouse_to = $targetWarehouseId;

                $delivery_note_item->save();
            }
        }

        // Képek és dokumentumok kezelése
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->getMimeType() === "image/jpeg" || $file->getMimeType() === "image/png" || $file->getMimeType() === "image/gif") {
                    $path = public_path('/document_storage/delivery_note_images/' . $delivery_note_id . '/');
                } else {
                    $path = public_path('/document_storage/delivery_note_docs/' . $delivery_note_id . '/');
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
                        'module' => 'delivery_note',
                        'resource_id' => $delivery_note_id,
                        'image_path' => "{$delivery_note_id}/{$uniqueName}",
                        'image_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    // Létrehozunk egy új rekordot a documents táblában
                    DB::table('documents')->insert([
                        'resource_id' => $delivery_note_id,
                        'document_path' => "{$delivery_note_id}/{$uniqueName}",
                        'document_comment' => $fileName.' - '.now(),
                        'document_type_id' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'document_module' => 'delivery_note_doc',
                        'doc_issue_date' => null,
                        'doc_expire_date' => null,
                        'doc_reminder_days' => null
                    ]);

                    // Fájl mentése a szerverre
                    $file->move($path, $uniqueName);
                }
            }
        }

        foreach ($request->ticketNumbers as $ticketNumber) {
            $purchase_ticket = PurchaseTicket::find($ticketNumber);
            $purchase_ticket->wait_to_transfer = 2;
            $purchase_ticket->save();
        }

        return response()->json(['data' => ['success' => 'success','message' => 'A vételi jegy már át lett adva.','delivery_note_id' => $delivery_note_id]], 200);
    }

    //delete_image ajax
    public function delete_image(Request $request)
    {
        $image_id = $request->image_id;

        $image = DB::table('images')->where('image_id', $image_id)->first();

        if (!$image) {
            return response()->json(['error' => 'A kép nem található!'], 404);
        }

        $image_path = public_path('/document_storage/'.$request->dir.'/' . $image->image_path);

        try {
            DB::beginTransaction();

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

            DB::table('images')->where('image_id', $image_id)->delete();

            DB::commit();

            return response()->json(['success' => 'A kép sikeresen törölve lett.']);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Hiba történt a kép törlése közben.'], 500);
        }
    }


    //delete_file ajax
    public function delete_file(Request $request)
    {
        $document_id = $request->document_id;

        $document = DB::table('documents')->where('document_id', $document_id)->first();

        if (!$document) {
            return response()->json(['error' => 'A fájl nem található!'], 404);
        }

        $document_path = public_path('/document_storage/'.$request->dir.'/' . $document->document_path);

        try {
            DB::beginTransaction();

            if (File::exists($document_path)) {
                File::delete($document_path);
            }

            DB::table('documents')->where('document_id', $document_id)->delete();

            DB::commit();

            return response()->json(['success' => 'A fájl sikeresen törölve lett.']);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Hiba történt a fájl törlése közben.'], 500);
        }
    }

    //accept items ajax
    public function accept_items(Request $request)
    {
        $itemsData = $request->input('items');
        $sign_code = $request->input('sign_code');
        $users = User::all();

        $isValidCode = false;
        foreach ($users as $user) {
            if ($sign_code === $user->sign_code) {
                $isValidCode = true;
                $act_user = User::find($user->id);
                break;
            }
        }

        if (!$isValidCode) {
            return response()->json(['success' => false, 'message' => 'Hibás aláíró kód!']);
        }

        foreach ($itemsData as $item) {
            if($item["type"] == 'delivery_note'){
                $delivery_note_item = DeliveryNoteItem::find($item["item_id"]);
                $delivery_note_item->accepted_at = date('Y-m-d H:i:s');
                $delivery_note_item->accepted_employee_id = $act_user->employee->employee_id;
                $delivery_note_item->save();
                $this->set_if_is_all_accepted_delivery_note_items($delivery_note_item->delivery_note_id,$act_user->employee->employee_id);
            }else{
                $warehouse_transfer_item = WarehouseTransferItem::find($item["item_id"]);
                $warehouse_transfer_item->accepted_at = date('Y-m-d H:i:s');
                $warehouse_transfer_item->accepted_employee_id = $act_user->employee->employee_id;
                $warehouse_transfer_item->save();

                $inventory = Inventory::find($warehouse_transfer_item->inventory_item_id);
                $inventory->storage_id = $warehouse_transfer_item->warehouse_to;
                // Speciális státusz beállítása, ha a cél raktár javító vagy fémjelző
                $inventory->status = InventoryStatus::IN_STOCK->value;
                $inventory->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'A termékeket sikeresen átvette.']);
    }

    public function set_if_is_all_accepted_delivery_note_items($delivery_note_id,$receiver_employee_id)
    {
        $delivery_note_items = DeliveryNoteItem::where('delivery_note_id', $delivery_note_id)->get();
        $is_all_accepted = true;
        foreach ($delivery_note_items as $item) {
            if (!$item->accepted_at) {
                $is_all_accepted = false;
                break;
            }
        }

        if ($is_all_accepted) {
            $delivery_note = DeliveryNote::find($delivery_note_id);
            $receiver_employee = Employee::find($receiver_employee_id);
            $delivery_note->receive_employee_id = $receiver_employee_id;
            $delivery_note->receive_employee_name = $receiver_employee->lastname.' '.$receiver_employee->firstname;
            $delivery_note->received_at = date('Y-m-d H:i:s');
            $delivery_note->save();
        }
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


    public function printDeliveryNote(Request $request)
    {
        //clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/delivery_notes/*'));

        $file_name = 'delivery_note.pdf';

        $pdf = \App::make('dompdf.wrapper');

        $data = array();
        $delivery_note = DeliveryNote::find($request->delivery_note_id);

        $delivery_note->printed_at = date('Y-m-d H:i:s');
        $delivery_note->save();

        $data['delivery_note'] = $delivery_note;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.delivery_note', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save(public_path() . '/pdf/delivery_notes/' . $file_name);

        return $file_name;
    }

    //get_delivery_note_images ajax
    public function get_delivery_note_images(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        $images = DB::table('images')
            ->where('module', 'delivery_note')
            ->where('resource_id', $delivery_note_id)
            ->get();

        return response()->json(['success' => 'success', 'images' => $images]);
    }

    //get_delivery_note_files ajax
    public function get_delivery_note_files(Request $request)
    {
        $delivery_note_id = $request->delivery_note_id;
        $files = DB::table('documents')
            ->where('document_module', 'delivery_note_doc')
            ->where('resource_id', $delivery_note_id)
            ->get();

        return response()->json(['success' => 'success', 'files' => $files]);
    }

    private function getNextSerial()
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
}
