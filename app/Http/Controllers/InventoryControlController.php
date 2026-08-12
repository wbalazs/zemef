<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Employee;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\InventoryControl;
use App\Models\InventoryControlItem;
use App\Models\ZImage;
use App\Models\Hallmark;
use App\Models\ProductColor;
use App\Models\ProductSign;
use App\Models\ProductStatus;
use App\Models\UnitPriceType;
use App\Models\VatType;
use App\Models\Currency;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Category;
use App\Tag;
use App\User;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StockController;
use Carbon\Carbon;
use App\Http\Requests\InventoryControlRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use PDF;
use File;


class InventoryControlController extends Controller
{

    public function __construct()
    {
        ////$this->authorizeResource(InventoryControl::class);
        $this->categoryController = new CategoryController();
        $this->stockController = new StockController();
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(InventoryControl $model)
    {
        return view('inventory_controls.index', 
        [
            'inventory_controls' => $model->orderBy('created_at', 'DESC')->get()
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
        $warehouses = Warehouse::where('status', 1)->orderBy('name', 'ASC')->get();
        $users = User::orderBy('name', 'ASC')->get();

        $stock_entry = StockEntry::create(['ticket_number' => '--empty_row--']);
        $stock_entry->ticket_number = '';

        $businessIds = auth()->user()->employee?->businesses?->pluck('business_id')->toArray() ?? [];

        $businesses = Business::where('status', 1)
            ->whereIn('business_id', $businessIds)
            ->orderBy('name', 'ASC')
            ->get();

        return view('inventory_controls.create', [
            'customers' => $customers,
            'businesses' => $businesses,
            'warehouses' => $warehouses,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\InventoryControlRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InventoryControlRequest $request, InventoryControl $model)
{
    // Formátum: XX/EEEE/00001 - ahol XX - bolti azonosító
    $serial = $this->getNextSerial();
    $inventory_control = $model->create($request->all());

    $inventory_control->inventory_control_number = 'LELTAR/' . $request->business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
    $inventory_control->responsible_user_id = $request->responsible_user_id;
    $inventory_control->inspector_user_id = $request->inspector_user_id;
    $inventory_control->save();

    // Create InventoryControlItems
    $actual_items = $this->stockController->get_actual_stock($inventory_control->warehouse_id);
    foreach ($actual_items as $item) {
        $inventory_control_item = new InventoryControlItem();
        $inventory_control_item->inventory_control_id = $inventory_control->inventory_control_id;
        $inventory_control_item->product_id = $item["product_id"];
        // main_product_id removed – column should be dropped
        $inventory_control_item->name = $item["product_name"];
        $inventory_control_item->prod_ins_date = $item["ins_date"];
        $inventory_control_item->brand = $item["product_brand"];
        $inventory_control_item->description = $item["short_description"];
        $inventory_control_item->book_quantity = $item["stock"];
        $inventory_control_item->book_price = $item["calculated_price"];
        $inventory_control_item->book_weight = $item["weight"];
        $inventory_control_item->save();
    }

    return redirect()->route('inventory_control.edit', $inventory_control->inventory_control_id)
                     ->withStatus('A leltárív sikeresen megnyitva.');
}


    public function update_inventory_control(Request $request)
    {
        $inventory_control = InventoryControl::Find($request->inventory_control_id);
        $inventory_control_item = InventoryControlItem::Where('product_id', $request->product_id)->first();
        if($inventory_control_item){
            $inventory_control_item->inventory_quantity = $inventory_control_item->inventory_quantity+1;
            $inventory_control_item->inventory_price = $inventory_control_item->book_price;
            $inventory_control_item->inventory_weight = $inventory_control_item->book_weight;
            $inventory_control_item->save();
            //GET HTML TABLE
            $html_table = $this->get_inventory_control_items_table($request->inventory_control_id);
            return json_encode(['success' => true,'html_table' => $html_table]);
        }else{
            //get_actual_items
            $actual_items = $this->stockController->get_actual_stock(null,false,$request->product_id);
            $prod_search = Product::Find($request->product_id);
            if($prod_search){
                $actual_items_rows = $this->get_product_find_table($actual_items);
                //GET HTML TABLE
                $html_table = $this->get_inventory_control_items_table($request->inventory_control_id);
                return json_encode(['success' => false,'prod_find' => true,'prod_now' => $actual_items_rows, 'html_table' => $html_table]);
            }else{
                $html_table = $this->get_inventory_control_items_table($request->inventory_control_id);
                return json_encode(['success' => false,'prod_find' => false,'html_table' => $html_table]);
            }
        }
    }

    public function get_inventory_control_items_table($inventory_control_id)
    {
        $items = InventoryControlItem::Where('inventory_control_id', $inventory_control_id)->get();
        $html_table = '';
        foreach($items as $item){
            $row_color = '';
            if($item->book_quantity == $item->inventory_quantity){
                $row_color = 'style="background-color: rgba(0, 128, 0, 0.5);"';
            }else{
                $row_color = 'style="background-color: rgba(255, 0, 0, 0.5);"';
            }
            $html_table .= '<tr '.$row_color.'>
                                <td># ' . $item->product_id . '</td>
                                <td>' . $item->name . '</td>
                                <td>' . $item->brand . '</td>
                                <td>' . $item->description . '</td>
                                <td>' . $item->prod_ins_date . '</td>
                                <td>' . $item->book_quantity . ' db</td>
                                <td>' . $item->inventory_quantity . ' db</td>
                                <td>' . $item->book_weight . ' g</td>
                                <td>' . $item->unit_price_type . '</td>
                                <td>' . number_format($item->book_price, 0, ',', ' ') . ' Ft</td>
                            </tr>';
        }
        return $html_table;
    }

    public function get_product_find_table($stock)
    {
        $html_table = '';
        foreach($stock as $item){
            $html_table .= '<tr>
                                <td># ' . $item["product_id"] . '</td>
                                <td>' . $item["product_name"] . '</td>
                                <td>' . $item["business_name"] . '</td>
                                <td>' . $item["warehouse_name"] . '</td>
                                <td>' . $item["stock"] . ' db</td>
                            </tr>';
        }
        return $html_table;
    }



    /**
     * Show the form for editing the specified item
     *
     * @param  \App\InventoryControl  $inventory_control
     * @return \Illuminate\View\View
     */
    public function edit(InventoryControl $inventory_control)
    {
        if($inventory_control->is_closed == 1){
            return redirect()->route('inventory_control.index')->withErrors('Lezárt leltárív már nem szerkeszthető!');
        }

        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();
        $warehouses = Warehouse::where('status', 1)->orderBy('name', 'ASC')->get();
        $businesses = Business::where('status', 1)
                                ->whereIn('business_id', auth()->user()->employee?->businesses?->pluck('business_id')->toArray())
                                    ->orderBy('name', 'ASC')
                                    ->get();

        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();

        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $items = $inventory_control->inventory_control_items;


        return view('inventory_controls.edit', [
            'inventory_control' => $inventory_control,
            'businesses' => $businesses,
            'warehouses' => $warehouses,
            'items' => $items
        ]);
    }


    private function getNextSerial()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'inventory_control')
            ->where('year', date('Y'))
            ->first();

        $serial = $record ? $record->serial_number : null;

        if (!$serial) {
            DB::table('serial_numbers')
                ->insert(['module' => 'inventory_control', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }

        $next = ($serial+1);

        DB::table('serial_numbers')
            ->where('module', 'inventory_control')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);

        return $serial;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $stock_entry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(InventoryControlRequest $request, InventoryControl $inventory_control)
    {
        $inventory_control->update(
            array_merge($request->all(),
                ['is_closed' => 1]
            )
        );

        return redirect()->route('inventory_control.index')->withStatus('A leltárív lezárása sikeres volt.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $stock_entry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(InventoryControl $inventory_control)
    {
        try {
            $stock_entry->delete();
            return redirect()->route('inventory_control.index')->withStatus('A leltárív sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('inventory_control.index')->withErrors('Nem lehet törölni a leltárívet, mert kapcsolódó adatok vannak.');
        }
    }


    public function printInventoryControl(Request $request)
    {
        //clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/inventory_controls/*'));

        $file_name = 'leltariv.pdf';

        $pdf = \App::make('dompdf.wrapper');

        $inventory_control = InventoryControl::find($request->inventory_control_id);
        $items = $inventory_control->inventory_control_items;

        $pdf->setOptions(['defaultFont' => 'DejaVu Sans','isHtml5ParserEnabled' => true, 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.inventory_control.inventory_control', [
            'inventory_control' => $inventory_control,
            'items' => $items
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream($file_name);
    }

    public function showInventoryDiscrepancies(Request $request)
    {
        ini_set('memory_limit', '512M');
        $file_name = 'leltar_elteresek.pdf';

        $pdf = \App::make('dompdf.wrapper');
        $inventory_control = InventoryControl::find($request->inventory_control_id);
        $items = $inventory_control->inventory_control_items->filter(function($item) {
            return $item->book_quantity != $item->inventory_quantity;
        });

        $pdf->setOptions(['defaultFont' => 'DejaVu Sans','isHtml5ParserEnabled' => true, 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.inventory_control.discrepancies', [
            'inventory_control' => $inventory_control,
            'items' => $items
        ]);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream($file_name);
    }

    public function printInventoryReport(Request $request)
    {
        array_map('unlink', glob(public_path() . '/pdf/inventory_reports/*'));

        $file_name = 'leltari_jegyzokonyv.pdf';

        $pdf = \App::make('dompdf.wrapper');

        $inventory_control = InventoryControl::find($request->inventory_control_id);
        $items = $inventory_control->inventory_control_items;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.inventory_control.inventory_report', [
            'inventory_control' => $inventory_control,
            'items' => $items
        ]);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream($file_name);
    }





    public function get_storages_by_business_id_employee_inventory_control(Request $request)
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
            ->where('is_receiving', null) // Itt szűrjük a nem vételi jegyesekre
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


    public function printStockEntry(Request $request)
    {
        //clean up tmp folder
        array_map('unlink', glob(public_path() . '/pdf/stock_entries/*'));

        $file_name = 'ekszer_bevetelezes.pdf';

        $pdf = \App::make('dompdf.wrapper');

        $data = array();

        $stock_entry = StockEntry::find($request->stock_entry_id);

        $stock_entry->printed_at = date('Y-m-d H:i:s');
        $stock_entry->save();

        $data['stock_entry'] = $stock_entry;

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.stock_entry', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save(public_path() . '/pdf/stock_entries/' . $file_name);

        return $file_name;
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


    public function deleteImg(Request $request)
    {
        $image = ZImage::find($request->img_id);

        if(file_exists(public_path() . '/document_storage/stock_entry_images/' . $image->image_path))
        {
            unlink(public_path() . '/document_storage/stock_entry_images/' . $image->image_path);
        }

        $image->delete();
    }

}
