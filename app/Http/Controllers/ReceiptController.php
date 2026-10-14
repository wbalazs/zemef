<?php

namespace App\Http\Controllers;

use App\Models\PurchaseTicket;
use App\Models\PurchaseTicketItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ZImage;
use App\Models\Hallmark;
use App\Models\ProductColor;
use App\Models\ProductSign;
use App\Models\ProductStatus;
use App\Models\ProductType;
use App\Models\WarehouseTransferItem;
use App\Models\Inventory;
use App\Models\WarehouseTransfer;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\ConsignmentReceipt;
use App\Models\ConsignmentReceiptItem;
use App\Models\TempSale;
use App\Models\TempSaleItem;
use App\Category;
use App\Tag;
use App\User;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockController;
use Carbon\Carbon;
use App\Http\Requests\ReceiptRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use PDF;
use File;
use Illuminate\Support\Facades\Storage;


class ReceiptController extends Controller
{
    protected $stockController;

    public function __construct()
    {
        //////$this->authorizeResource(Receipt::class);
        //$this->middleware('auth');
        $this->categoryController = new CategoryController();
        $this->stockController = new StockController();
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function all_purchase_ticket(PurchaseTicket $model)
    {
        PurchaseTicket::where('ticket_number', '--empty_row--')->delete();

        return view('receipts.all_purchase_ticket', ['purchase_tickets' => $model->orderBy('created_at', 'DESC')->get()]);
    }

    public function all_consignment_receipt(ConsignmentReceipt $model)
    {
        ConsignmentReceipt::where('ticket_number', '--empty_row--')->delete();
        
        return view('receipts.all_consignment_receipt', ['consignment_receipts' => $model->orderBy('created_at', 'DESC')->get()]);
    }

    public function all_stock_entry(StockEntry $model)
    {
        return view('receipts.all_stock_entry', ['stock_entries' => $model->orderBy('created_at', 'DESC')->get()]);
    }

    public function all_delivery_note(DeliveryNote $model)
    {

        return view('receipts.all_delivery_note', ['delivery_notes' => $model->orderBy('created_at', 'DESC')->get()]);
    }

    public function all_warehouse_transfer(WarehouseTransfer $model)
    {

        return view('receipts.all_warehouse_transfer', ['warehouse_transfers' => $model->orderBy('created_at', 'DESC')->get()]);
    }

    public function all_booking(TempSale $model)
    {

        return view('receipts.all_booking', ['bookings' => $model::where('sale_type', 'booking')->orderBy('created_at', 'DESC')->get()]);
    }

    public function printBooking(Request $request)
    {
        $sale_id = $request->booking_id;
        $type= "booking";
        // Töröljük a korábbi PDF-eket
        array_map('unlink', glob(public_path("pdf/sales/*")));

        $file_name = 'sale_' . $type . '_' . $sale_id . '_' . time() . '.pdf';
        $save_path = public_path("pdf/sales/" . $file_name);

        // TEMP tábla alapján lekérjük az adatokat
        $sale = \App\Models\TempSale::find($sale_id);
        if (!$sale) {
            abort(404, 'Értékesítés nem található.');
        }

        $items = \App\Models\TempSaleItem::where('temp_sale_id', $sale_id)->get();

        // Nyomtatási idő mentése első nyomtatáskor
        if (!$sale->printed_at) {
            $sale->printed_at = now();
            $sale->is_printed = 1;
            $sale->save();
        }

        // PDF generálás
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

        $view = 'pdf.sale_' . $type;
        if ($type === 'booking' && count($items) > 2) {
            $view = 'pdf.sale_booking_2';
        }

        $pdf->loadView($view, [
            'sale' => $sale,
            'items' => $items
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->save($save_path);

        return asset('pdf/sales/' . $file_name);
    }

    public function printDeliveryNote(Request $request)
    {
        // Clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/delivery_notes/*'));

        $delivery_note_id = $request->delivery_note_id;
        $file_name = 'delivery_note_' . $delivery_note_id . '_' . time() . '.pdf';
        $save_path = public_path() . '/pdf/delivery_notes/' . $file_name;

        $pdf = \App::make('dompdf.wrapper');

        $data = array();
        $delivery_note = DeliveryNote::with(['business', 'delivery_note_items'])
            ->find($delivery_note_id);

        if (!$delivery_note) {
            return response()->json([
                'message' => 'A szallitolevel nem talalhato.'
            ], 404);
        }

        if($delivery_note->printed_at == null){
            $delivery_note->printed_at = date('Y-m-d H:i:s');
            $delivery_note->save();
        }

        $data['delivery_note'] = $delivery_note;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.delivery_note', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save($save_path);

        return asset('pdf/delivery_notes/' . $file_name);
    }

    public function printPurchaseTicket(Request $request)
    {
        // Clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/purchase_tickets/*'));

        $purchase_ticket_id = $request->purchase_ticket_id;
        $file_name = 'purchase_ticket_' . $purchase_ticket_id . '_' . time() . '.pdf';
        $save_path = public_path() . '/pdf/purchase_tickets/' . $file_name;

        $pdf = \App::make('dompdf.wrapper');

        $data = array();
        $purchase_ticket = PurchaseTicket::find($purchase_ticket_id);
        if (!$purchase_ticket || $purchase_ticket->inventoryItems()->count() === 0) {
            return response()->json([
                'message' => 'Nyomtatas nem lehetseges, mert a veteli jegyhez nincs mentett tetel.'
            ], 422);
        }

        if($purchase_ticket->printed_at == null){
            $purchase_ticket->printed_at = date('Y-m-d H:i:s');
            $purchase_ticket->save();
        }

        $data['purchase_ticket'] = $purchase_ticket;
        $data['inventoryItems'] = $purchase_ticket->inventoryItems; // új relationship a PurchaseTicket modellben

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        if(count($purchase_ticket->inventoryItems) > 2){
            $pdf->loadView('pdf.purchase_ticket_2', ['data' => $data]);
        }else{
            $pdf->loadView('pdf.purchase_ticket', ['data' => $data]);
        }
        $pdf->setPaper('a4', 'profile');
        $pdf->save($save_path);

        return asset('pdf/purchase_tickets/' . $file_name);
    }

    public function printStockEntry(Request $request)
    {
        // Clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/stock_entries/*'));

        $stock_entry_id = $request->stock_entry_id;
        $file_name = 'stock_entry_' . $stock_entry_id . '_' . time() . '.pdf';
        $save_path = public_path() . '/pdf/stock_entries/' . $file_name;

        $pdf = \App::make('dompdf.wrapper');

        $data = array();
        $stock_entry = StockEntry::with(['inventoryItems', 'stock_entry_items.product'])->find($stock_entry_id); // Eager loading
        if ($stock_entry->printed_at == null) {
            $stock_entry->printed_at = now();
            $stock_entry->save();
        }

        $items = $stock_entry->inventoryItems;
        if ($items->isEmpty()) {
            $items = $stock_entry->stock_entry_items;
        }

        $data['stock_entry'] = $stock_entry;
        $data['items'] = $items;
        $data['product_type_names'] = ProductType::pluck('name', 'product_type_id')->toArray();
        
        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.stock_entry', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save($save_path);

        return asset('pdf/stock_entries/' . $file_name);
    }

    public function printConsignmentReceipt(Request $request)
    {
        // Clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/consignment_receipts/*'));

        $consignment_receipt_id = $request->consignment_receipt_id;
        $file_name = 'consignment_receipt_' . $consignment_receipt_id . '_' . time() . '.pdf';
        $save_path = public_path() . '/pdf/consignment_receipts/' . $file_name;

        $pdf = \App::make('dompdf.wrapper');

        $data = array();
        $consignment_receipt = ConsignmentReceipt::find($consignment_receipt_id);
        if($consignment_receipt->printed_at == null){
            $consignment_receipt->printed_at = date('Y-m-d H:i:s');
            $consignment_receipt->save();
        }

        $data['consignment_receipt'] = $consignment_receipt;
        $data['business'] = $consignment_receipt->business;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.consignment_receipt', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save($save_path);

        return asset('pdf/consignment_receipts/' . $file_name);
    }

    public function printWarehouseTransfer(Request $request)
    {
        // Clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/warehouse_transfers/*'));

        $warehouse_transfer_id = $request->warehouse_transfer_id;
        $file_name = 'purchase_ticket_' . $warehouse_transfer_id . '_' . time() . '.pdf';
        $save_path = public_path() . '/pdf/warehouse_transfers/' . $file_name;

        $pdf = \App::make('dompdf.wrapper');

        $data = array();
        $warehouse_transfer = WarehouseTransfer::find($warehouse_transfer_id);
        if($warehouse_transfer->printed_at == null){
            $warehouse_transfer->printed_at = date('Y-m-d H:i:s');
            $warehouse_transfer->save();
        }

        $data['warehouse_transfer'] = $warehouse_transfer;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.warehouse_transfer', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save($save_path);

        return asset('pdf/warehouse_transfers/' . $file_name);
    }



}
