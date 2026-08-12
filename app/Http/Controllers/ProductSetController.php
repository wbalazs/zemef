<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\ProductSet;
use App\Models\Hallmark;
use App\Models\ProductColor;
use App\Models\ProductStatus;
use App\Models\ProductSign;
use App\Models\UnitPriceType;
use App\Models\VatType;
use App\Models\ZImage;
use App\Http\Controllers\CategoryController;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use File;
use Log;


class ProductSetController extends Controller
{
    private $categoryController;

    public function __construct()
    {
        ////$this->authorizeResource(ProductSet::class);

        $this->categoryController = new CategoryController();
    }

    /**
     * Display a listing of the products
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(ProductSet $model)
    {
        ProductSet::where('name', '--empty_row--')->delete();
        return view('products.sets', ['products' => $model->where('type', 'set')->get()]);
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

        $product = ProductSet::create(['name' => '--empty_row--', 'type' => 'set']);
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

        return view('products.set_edit', [
            'product_set' => $product,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_signs' => $product_signs,
            'pti_images' => $pti_images,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'set_products'  => ProductSet::where('type', '!=', 'set')->get(),
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
    public function store(ProductRequest $request, ProductSet $model)
    {
        $product = $model->create($request->merge([
        ])->all());

        return redirect()->route('product_set.index')->withStatus('A szett sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $product
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(ProductSet $product_set)
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

        return view('products.set_edit', [
            'product_set' => $product_set,
            'categories' => $categories,
            'hallmarks' => $hallmarks,
            'product_colors' => $product_colors,
            'product_statuses' => $product_statuses,
            'product_signs' => $product_signs,
            'pti_images' => $pti_images,
            'price_types' => $price_types,
            'vat_types' => $vat_types,
            'set_products'  => ProductSet::where('type', '!=', 'set')->get(),
            'show' => false
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductRequest $request, ProductSet $product_set)
    {
        $product_set->update(
            array_merge($request->all(), [
                'discount_allowed' => $request->discount_allowed
            ])
        );

        $product_set->setItems()->sync($request->get('set_products'));
        $product_set->hallmarks()->syncWithoutDetaching($request->hallmarks);
        $product_set->product_colors()->syncWithoutDetaching($request->product_colors);
        $product_set->product_statuses()->syncWithoutDetaching($request->product_statuses);
        $product_set->product_signs()->syncWithoutDetaching($request->product_signs);

        foreach ($request->all() as $attr => $param) {
            if(substr($attr, 0, 9) == 'hallmark_'){
                DB::table('hallmark_product')
                    ->where('hallmark_hallmark_id', substr($attr, 9))
                    ->where('product_product_id', $product_set->product_id)
                    ->update(['value' => $param]);
            }
            elseif(substr($attr, 0, 14) == 'product_color_'){
                DB::table('product_product_color')
                    ->where('product_color_product_color_id', substr($attr, 14))
                    ->where('product_product_id', $product_set->product_id)
                    ->update(['value' => $param]);
            }
            elseif(substr($attr, 0, 15) == 'product_status_'){
                DB::table('product_product_status')
                    ->where('product_status_product_status_id', substr($attr, 15))
                    ->where('product_product_id', $product_set->product_id)
                    ->update(['comment' => $param]);
            }
            elseif(substr($attr, 0, 13) == 'product_sign_'){
                DB::table('product_product_sign')
                    ->where('product_sign_product_sign_id', substr($attr, 13))
                    ->where('product_product_id', $product_set->product_id)
                    ->update(['comment' => $param]);
            }
        }

        return redirect()->route('product_set.index')->withStatus('A szett adatai sikeresen mentve.');
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


    public function deleteImg(Request $request)
    {
        $image = ZImage::find($request->img_id);

        if(file_exists(public_path() . '/document_storage/product_images/' . $image->image_path))
        {
            unlink(public_path() . '/document_storage/product_images/' . $image->image_path);
        }

        $image->delete();
    }

}
