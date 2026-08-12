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
use App\Models\WebshopOrder;
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


class WebshopController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Lekérdezzük az összes megrendelést, beleértve a kapcsolódó tételeket és metaadatokat
        $orders = WebshopOrder::with(['items', 'order_status', 'customer'])->get();

        // Visszaadjuk a megrendeléseket egy nézetnek
        return view('webshop.index', compact('orders'));
    }

    //EDIT
    public function edit($id)
    {
        // Lekérjük a megrendelést, beleértve a kapcsolódó tételeket és metaadatokat
        $order = WebshopOrder::with(['items', 'order_status', 'customer'])->findOrFail($id);

        // Visszaadjuk a megrendelést egy nézetnek
        return view('webshop.edit', compact('order'));
    }




}
