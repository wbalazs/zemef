<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Enums\InventoryStatus;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Hallmark;
use App\Models\ProductColor;
use App\Models\ProductStatus;
use App\Models\ProductSign;
use App\Models\UnitPriceType;
use App\Models\VatType;
use App\Models\ProductType;
use App\Models\ZImage;
use App\Models\CustomerMarking;
use App\Http\Controllers\CategoryController;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use File;
use Log;
use PDF;


class ProductController extends Controller
{
    private $categoryController;

    public function __construct()
    {
        ////$this->authorizeResource(Product::class);

        $this->categoryController = new CategoryController();
    }

    /**
     * Display a listing of the products
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Product $model)
    {
        Product::where('name', '--empty_row--')->delete();

        // Lekérdezés a termékekre a feltételek szerint
        //$products = Product::all();
        $products = Product::where(function($query) {
            $query->where('is_unique', 1);
                
        })->orWhere(function($query) {
            $query->where('is_unique', 0);
               
        })->get();
        
        return view('products.index', 
            ['products' => $products]);
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
        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $pti_images = [];

        $product = Product::create(['name' => '--empty_row--']);
        $product->name = '';

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

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_signs' => $product_signs,
            'pti_images' => $pti_images,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'show' => false
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRequest $request, Product $model)
    {
        $product = $model->create($request->merge([
            'discount_allowed' => $request->discount_allowed,
            'is_rent_available' => $request->is_rent_available,
        ])->all());


        return redirect()->route('product.index')->withStatus('A termék sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $product
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();

        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $pti_images = [];

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_signs' => $product_signs,
            'pti_images' => $pti_images,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'show' => false
        ]);
    }

    public function showProduct($id)
    {
        $product = Product::find($id);
        $categories = Category::where('status', 1)->whereNull('parent_id')->orderBy('name', 'ASC')->get();

        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->categoryController->setCategoryDepth($category);
        });

        $hallmarks = Hallmark::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_colors = ProductColor::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_statuses = ProductStatus::where('status', 1)->orderBy('name', 'ASC')->get();
        $product_signs = ProductSign::where('status', 1)->orderBy('name', 'ASC')->get();
        $price_types = UnitPriceType::orderBy('name', 'ASC')->get();
        $vat_types = VatType::orderBy('name', 'ASC')->get();
        $pti_images = [];

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_signs' => $product_signs,
            'pti_images' => $pti_images,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'show' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update(
            array_merge($request->all(), [
                'discount_allowed' => $request->discount_allowed,
                'is_rent_available' => $request->is_rent_available,
            ])
        );

        if ($request->has('price_type')) {
            $unitprice = UnitPriceType::find($request->input('price_type'));
    
            if ($unitprice) {
                $product->price_type = $unitprice->unit_price_type_id;
                $product->real_price = $unitprice->price;
                $product->save();
            }
        }

        if ($request->has('vat_type')) {
            $vat = VatType::find($request->input('vat_type'));
            if ($vat) {
                $product->vat_type = $vat->vat_type_id;
                $product->save();
            }
        }

        $product->hallmarks()->syncWithoutDetaching($request->hallmarks);
        $product->product_colors()->syncWithoutDetaching($request->product_colors);
        $product->product_statuses()->syncWithoutDetaching($request->product_statuses);
        $product->product_signs()->syncWithoutDetaching($request->product_signs);

        foreach ($request->all() as $attr => $param) {
            if(substr($attr, 0, 9) == 'hallmark_'){
                DB::table('hallmark_product')
                    ->where('hallmark_hallmark_id', substr($attr, 9))
                    ->where('product_product_id', $product->product_id)
                    ->update(['value' => $param]);
            }
            elseif(substr($attr, 0, 14) == 'product_color_'){
                DB::table('product_product_color')
                    ->where('product_color_product_color_id', substr($attr, 14))
                    ->where('product_product_id', $product->product_id)
                    ->update(['value' => $param]);
            }
            elseif(substr($attr, 0, 15) == 'product_status_'){
                DB::table('product_product_status')
                    ->where('product_status_product_status_id', substr($attr, 15))
                    ->where('product_product_id', $product->product_id)
                    ->update(['comment' => $param]);
            }
            elseif(substr($attr, 0, 13) == 'product_sign_'){
                DB::table('product_product_sign')
                    ->where('product_sign_product_sign_id', substr($attr, 13))
                    ->where('product_product_id', $product->product_id)
                    ->update(['comment' => $param]);
            }
        }

        return redirect()->route('product.index')->withStatus('A termék adatai sikeresen mentve.');
    }

    public function setPrimaryImage(Request $request)
    {
        $image = ZImage::findOrFail($request->img_id);

        // Reset all images of the same product
        ZImage::where('resource_id', $image->resource_id)
            ->where('module', $image->module)
            ->update(['is_primary' => 0]);

        // Set selected one to primary
        $image->is_primary = 1;
        $image->save();

        return response()->json(['status' => 'ok']);
    }

    public function get_one_product_by_id(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        //return json product
        return response()->json($product);
    }

    public function getAllProductList()
    {
        $allRows = Product::where('main_product_id',0)->get();
        
        return response()->json([
            'success' => true,
            'rows' => $allRows,
            'count' => count($allRows)
        ]);
    }

    public function get_all_product(Request $request)
    {
        $term = $request->term;
        $products = Product::where('name', 'LIKE', '%' . $term . '%')->get();
        return response()->json($products);
        
    }

    //Terméktulajdonságok ajax
    public function product_colors_ajax(Request $request)
    {
        $product = Product::find($request->product_id);

        $color_ids = $request->has('colors') ? $request->colors : [];
        $product->product_colors()->sync($color_ids);

        $rows = '';
        foreach ($product->product_colors as $product_color) {
            $product_color_key = 'product_color_' . $product_color->product_color_id;
            $value = $product_color->pivot->value;

            $rows .= '<div class="row">
                        <div class="col-lg-12">
                            <fieldset>
                                <div class="form-group">
                                    <label class="form-control-label">' .
                                        $product_color->name . '</label>
                                    <input type="text" class="form-control" name="' . $product_color_key . '" value="' . $value . '">
                                </div>
                            </fieldset>
                        </div>
                    </div>';
        }

        return $rows;
    }

    public function product_statuses_ajax(Request $request)
    {
        $product = Product::find($request->product_id);

        $statuses_ids = $request->has('statuses') ? $request->statuses : [];
        $product->product_statuses()->sync($statuses_ids);

        $rows = '';
        foreach ($product->product_statuses as $product_status) {
            $product_status_key = 'product_status_' . $product_status->product_status_id;
            $value = $product_status->pivot->comment;

            $rows .= '<div class="row">
                        <div class="col-lg-12">
                            <fieldset>
                                <div class="form-group">
                                    <label class="form-control-label">' .
                                        $product_status->name . '</label>
                                    <input type="text" class="form-control" name="' . $product_status_key . '" value="' . $value . '">
                                </div>
                            </fieldset>
                        </div>
                    </div>';
        }

        return $rows;
    }

    public function product_signs_ajax(Request $request)
    {
        $product = Product::find($request->product_id);

        $signs_ids = $request->has('signs') ? $request->signs : [];
        $product->product_signs()->sync($signs_ids);

        $rows = '';
        foreach ($product->product_signs as $product_sign) {
            $product_sign_key = 'product_sign_' . $product_sign->product_sign_id;
            $value = $product_sign->pivot->comment;

            $rows .= '<div class="row">
                        <div class="col-lg-12">
                            <fieldset>
                                <div class="form-group">
                                    <label class="form-control-label">' .
                                        $product_sign->name . '</label>
                                    <input type="text" class="form-control" name="' . $product_sign_key . '" value="' . $value . '">
                                </div>
                            </fieldset>
                        </div>
                    </div>';
        }

        return $rows;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('product.index')->withStatus('A termék sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('product.index')->withErrors('Nem lehet törölni a terméket, mert kapcsolódó adatok vannak.');
        }
    }

    public function printLabel(Request $request)
    {
        // PDF készítő
        $pdf = \App::make('dompdf.wrapper');
        // Vonalkód generálása EAN-13 formátumban
        $generator = new BarcodeGeneratorPNG(); 

        if($request->input('inventory_id'))
        {
            // Termék adatainak lekérése
            $inventory = Inventory::find($request->input('inventory_id'));
            $itemData = $inventory->item_data;
            if (is_string($itemData)) {
                $itemData = json_decode($itemData, true) ?: [];
            }
            if (!is_array($itemData)) {
                $itemData = [];
            }

            $category = null;
            $vegyjel = 'AU';
            $termekTipusId = $inventory->termek_tipus_id ?? ($itemData['termek_tipus_id'] ?? null);
            if (empty($termekTipusId) && !empty($itemData['category_id'])) {
                $termekTipusId = $itemData['category_id'];
            }
            if (!empty($termekTipusId)) {
                $category = Category::find($termekTipusId);
                $vegyjel = $category->au ?? 'AU-';
            }
            $szallito = 'VZJ_A';
            $customerMarkingIdRaw = $itemData['customer_marking_id'] ?? null;
            $customerMarkingId = null;

            if (is_numeric($customerMarkingIdRaw)) {
                $customerMarkingId = (int) $customerMarkingIdRaw;
            } elseif (is_string($customerMarkingIdRaw) && preg_match('/\d+/', $customerMarkingIdRaw, $matches)) {
                $customerMarkingId = (int) $matches[0];
            }

            if (!empty($customerMarkingId)) {
                $customerMarkingName = CustomerMarking::where('id', $customerMarkingId)->value('name');
                if (!empty($customerMarkingName)) {
                    $szallito = $customerMarkingName;
                }
            }
            $productTypeIdForName = $inventory->termek_tipus_id ?? ($itemData['termek_tipus_id'] ?? null);
            $productName = !empty($productTypeIdForName)
                ? (ProductType::find($productTypeIdForName)?->name ?? '')
                : '';

            $sizeLabel = isset($itemData['size']) ? trim((string) $itemData['size']) : '';
            $hallmarksLabel = (!empty($itemData['hallmarks']) && (string) $itemData['hallmarks'] !== '0') ? 'FJ' : 'FN';

            $barcode = base64_encode($generator->getBarcode($inventory->product_id, $generator::TYPE_EAN_13));
            // Adatok előkészítése a sablonhoz
            $data = [
                'inventory_id' => $inventory->inventory_id,
                'product_name' => $productName,
                'price' => number_format($inventory->price, 0, ',', '.') . ' Ft',
                
                'karat' => $itemData['karat_id'] ?? '14Kt',
                'vegyjel' => $vegyjel,
                'szallito' => $szallito,
                'barcode' => $barcode,
                'hallmark' => $inventory->hallmark_id ? Hallmark::find($inventory->hallmark_id)->name : ($itemData['hallmarks'] ?? 'FN'),
                'weight' => number_format($inventory->weight, 1, ',', '.') . 'g',
                'size' => $sizeLabel,
                'hallmarks' => $hallmarksLabel,
            ];
        
         
        }
       
        // Címke típus kiválasztása
        $label_type = $request->input('label_type', 1); // Alapértelmezett: 1-es típus
       // return view("labels.type{$label_type}", $data);
        // Sablon betöltése és PDF generálása
        $pdf->setOptions(['defaultFont' => 'DejaVu Sans', 'isRemoteEnabled' => true]);
        $pdf->loadView("labels.type{$label_type}", $data);
        $pdf->setPaper([0, 0, 226.77, 28.35], 'portrait'); // 64x10mm méretű címke (226.77x28.35 pont)

        // PDF visszaadása a válaszban, megnyitásra kész, új lapon
        return $pdf->stream("termek_cimke.pdf");
    }

    public function autoSaveProductImage(Request $request)
    {
        $image = new ZImage();
        $image->resource_id = $request->product_id;
        $image->image_path = $request->image;
        $image->image_date = $request->image_date;
        $image->created_at = date('Y-m-d H:i:s');
        $image->module = 'product';
        $image->save();
    }

    
    public function uploadProductImage(Request $request)
    {
        $file = $request->file('product_image');
        $path = public_path('/document_storage/product_images/' . $request->product_id . '/');

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $image_resize = Image::make($file->getRealPath());              

        // $w = $image_resize->getWidth();
        // $h = $image_resize->getHeight();
        // $ratio = (1200/$w);
        // $height = ($h*$ratio);

        // $image_resize->resize(1200, $height);

        $image_resize->save($path . $file->getClientOriginalName());

        return $request->product_id . '/' . $file->getClientOriginalName();
    }


    public function loadProductGallery(Request $request)
    {
        $html = '';

        $product = Product::find($request->product_id);
        
        $html .= '<div class="delete_buttons">';

        foreach($product->product_images as $img)
        {
            $html .= '<div class="hidden_image_wrapper" data-img_id="' . $img->image_id . '">
                        <button type="button" data-img_id="'. $img->image_id . '" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed"><i data-img_id="'.$img->image_id.'" class="fa fa-times"></i></button>
                        </div>';
        }
    
        $html .= '</div>';
        

        $html .= '<div data-lightGallery="1" id="lightgallery_product_images" class="">';

        foreach($product->product_images as $img)
        {
                $html .= '<div data-sub-html="" data-img_id="' . $img->image_id . '" class="product_img_wrapper" data-src="' . env('PUBLIC_URL') . '/document_storage/product_images/' . $img->image_path . '"><img data-img_id="' . $img->image_id . '" class="img-responsive product_image_img" src="' . env('PUBLIC_URL') . '/document_storage/product_images/' . $img->image_path . '" alt=""></div>';
        }
        $html .= '</div>';
            
        return $html;
    }

    public function loadProductGalleryNew(Request $request)
    {
        $html = '';

        $product = Product::find($request->product_id);

        $html .= '<div data-lightGallery="1" id="lightgallery_product_images" class=""><table class="table-borderless"><tr>';

        foreach ($product->product_images as $img) {
            $imageUrl = env('PUBLIC_URL') . '/document_storage/product_images/' . $img->image_path;

            $html .= '<td style="padding: 10px; width: 200px; height: 230px; vertical-align: top;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 8px; margin-bottom: 4px;">
                            <button type="button"
                                    class="btn btn-sm ' . ($img->is_primary == 0 ? 'set_primary_btn' : '') . '"
                                    style="background: none; border: none; font-size: 20px !important; color: ' . ($img->is_primary ? '#ffc107' : '#ccc') . ';"
                                    data-img_id="' . $img->image_id . '">
                                <i class="' . ($img->is_primary ? 'fas' : 'far') . ' fa-star"></i>
                            </button>
                            <button type="button"
                                    class="btn btn-sm delete_img_new"
                                    style="background: none; border: none; font-size: 20px !important; color: #dc3545;"
                                    data-img_id="' . $img->image_id . '">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="lightgallery_item"
                            data-src="' . $imageUrl . '"
                            data-sub-html=""
                            style="width: 100%; height: 200px; overflow: hidden;">
                            <img class="img-fluid product_image_img"
                                src="' . $imageUrl . '"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                alt="Kép #' . $img->image_id . '">
                        </div>
                    </td>';
        }

        $html .= '</tr></table></div>';

        return $html;
    }



    public function deleteImg(Request $request)
    {
        $image = ZImage::find($request->img_id);

        if(file_exists(public_path() . '/document_storage/product_images/' . $image->image_path))
        {
            unlink(public_path() . '/document_storage/product_images/' . $image->image_path);
        }

        $image->delete();
    }
    
    public function searchProducts(Request $request)
    {
        $search = $request->get('search');
        $searchInventoryId = null;

        if (is_string($search) && preg_match('/PRD-\s*(\d+)/i', $search, $matches)) {
            $searchInventoryId = (int) $matches[1];
        }
        if ($searchInventoryId === null && is_string($search) && preg_match('/#\s*(\d+)/', $search, $matches)) {
            $searchInventoryId = (int) $matches[1];
        }
        
        $business_id = auth()->user()?->employee?->active_business_id;
        if (empty($business_id)) {
            return response()->json(['results' => []]);
        }
        
        $warehouseIds = DB::table('business_warehouse')
            ->where('business_business_id', $business_id)
            ->pluck('warehouse_warehouse_id');

        if ($warehouseIds->isEmpty()) {
            return response()->json(['results' => []]);
        }

        $products = Inventory::with(['storage'])
            ->where('status', InventoryStatus::IN_STOCK->value)
            ->where('quantity', '>', 0)
            ->whereIn('storage_id', $warehouseIds)
            ->when($searchInventoryId !== null, function($query) use ($searchInventoryId) {
                $query->where('inventory_id', $searchInventoryId);
            }, function($query) use ($search) {
                $query->when($search, function($innerQuery) use ($search) {
                    $innerQuery->where(function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhere('factory_number', 'like', '%' . $search . '%');
                    });
                });
            })
            ->orderBy('updated_at', 'DESC')
            ->limit(50) // Limit results for performance
            ->get()
            ->map(function($inventory) {
            $itemData = $inventory->item_data;
            if (is_string($itemData)) {
                $itemData = json_decode($itemData, true) ?: [];
            }
            if (!is_array($itemData)) {
                $itemData = [];
            }

            $termekTipusId = $itemData['termek_tipus_id']
                ?? $inventory->termek_tipus_id
                ?? $itemData['category_id']
                ?? null;

            $productTypeName = !empty($termekTipusId)
                ? ProductType::getName((int) $termekTipusId)
                : null;

            $name = $productTypeName ?: ($inventory->name ?: ('Tétel #' . $inventory->inventory_id));
            $priceNet = (float) ($inventory->oneprice ?? 0);
            return [
                'id' => $inventory->inventory_id,
                'text' => '#' . $inventory->inventory_id . ' - ' . $name . ' - ' . ($inventory->code ?? ''),
                'inventory_id' => $inventory->inventory_id,
                'product_id' => $inventory->product_id,
                'name' => $name,
                'code' => $inventory->code,
                'price_net' => $priceNet,
                'price_gross' => round($priceNet * 1.27),
                'quantity' => (int) $inventory->quantity,
                'warehouse_id' => $inventory->storage_id,
                'warehouse_name' => optional($inventory->storage)->name,
                'status' => ((int) $inventory->status === InventoryStatus::IN_STOCK->value ? 'Raktáron' : 'Nem elérhető'),
            ];
        });

        return response()->json([
        'results' => $products
    ]);
    }
}
