<?php

namespace App\Http\Controllers;


use App\Models\Business;
use App\Models\Employee;
use App\Category;
use Carbon\Carbon;
use App\Models\WebshopOrder;
use App\Models\WebshopOrderItem;
use App\Models\WebshopOrderMeta;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\StockEntryItem;
use App\Models\StockEntry;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Hallmark;
use App\Models\ProductColor;
use App\Models\ProductStatus;
use App\Models\ProductSign;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\OrderStatus;
use App\Models\Address\Zip;
use App\Models\Address\City;
use Illuminate\Http\Request;
use DB;



class ApiController extends Controller
{
    public function __construct()
    {

    }


    public function test()
    {
        $file = public_path('keszlet.csv');
        $allData = [];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            // Fejléc beolvasása
            $header = fgetcsv($handle, 1000, ';');

            // Sorok feldolgozása
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // Ellenőrizd, hogy a sor nem üres
                if (count($data) > 1) {
                    $price = $this->convertToNumber($data[7]);
                    $weight = $this->convertToNumber(str_replace(',', '.', $data[5]));
                    $sei_oneprice = ($weight > 0) ? $price / $weight : 0;

                    $datas = array(
                        'sei_name' => $data[2], // megnevezes
                        'sei_description' => $data[16] ?: '', // leiras
                        'sei_weight' => str_replace(',', '.', $data[5]), // suly
                        'sei_oneprice' => $price,
                        'sei_factory_number' => $data[9], // fin
                        'sei_comment' => $data[3], // megjegyzes_tipus
                        'hallmark_type' => $data[3],
                        'stock_entry_id' => 187
                    );

                    // Hívja meg a saveStockEntryItem függvényt az adat objektummal
                    $this->saveStockEntryItem((object) $datas);

                    // Adat hozzáadása a teljes tömbhöz
                    $allData[] = $datas;
                }
            }

            fclose($handle);
        }

        return response()->json(['data' => $allData], 200);
    }

    private function convertToNumber($value)
    {
        $value = str_replace(',', '.', $value);
        return is_numeric($value) ? (float)$value : 0;
    }

    // teszt függvény
    public function test_old()
    {
        $file = public_path('keszlet.csv');
        $allData = [];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            // Fejléc beolvasása
            $header = fgetcsv($handle, 1000, ';');

            // Sorok feldolgozása
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // Ellenőrizd, hogy a sor nem üres
                if (count($data) > 1) {
                    $datas = array(
                        'esorsz' => $data[0],
                        'uzlet' => $data[1],
                        'megnevezes' => $data[2],
                        'megjegyzes_tipus' => $data[3],
                        'meret' => $data[4],
                        'suly' => $data[5],
                        'eladasi_ar' => $data[6],
                        'beszerzesi_ar' => $data[7],
                        'szallito' => $data[8],
                        'fin' => $data[9],
                        'beszerzesi_ido' => $data[10],
                        'eladas' => $data[11],
                        'bizonylat_szam' => $data[12],
                        'eladasi_ar_kedvezmeny' => $data[13],
                        'elad_gr_ar' => $data[14],
                        'leiras' => $data[15]
                    );


                    
                    // Adat hozzáadása a teljes tömbhöz
                    $allData[] = $datas;
                }
            }

            fclose($handle);
        }

        return response()->json(['data' => $allData], 200);
    }


    public function saveStockEntryItem($request)
    {
        $mainProduct = new Product();
        $mainProduct->name = $request->sei_name;
        $mainProduct->short_description = $request->sei_description;
        $mainProduct->status = 1;
        $mainProduct->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
        $mainProduct->weight = 0;
        $mainProduct->vat_type = 6; // 27%
        $mainProduct->save();

        //Prod insert
        $product = new Product();
        $product->main_product_id = $mainProduct->product_id;
        $product->name = $mainProduct->name;
        $product->short_description = $request->sei_description;
        $product->status = 1;
        $product->is_unique = 1;
        $product->hallmark_type = $request->hallmark_type;
        $product->vat_type = 6; // 27%
        $product->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
        $product->weight = $request->sei_weight ? $request->sei_weight : 0;
        $product->real_price = $request->sei_oneprice;
        $product->save();
        //PTI INSERTS
        $stock_entry_item = new StockEntryItem();
        $stock_entry_item->stock_entry_id = $request->stock_entry_id;
        $stock_entry_item->name = $product->name;
        $stock_entry_item->description = $request->sei_description;
        $stock_entry_item->quantity = 1;
        $stock_entry_item->weight = $request->sei_weight;
        $stock_entry_item->factory_number = $request->sei_factory_number;
        $stock_entry_item->oneprice = $request->sei_oneprice;
        $stock_entry_item->price = $request->sei_oneprice*1;
        $stock_entry_item->comment = $request->sei_comment;
        $stock_entry_item->product_id = $product->product_id;
        $stock_entry_item->main_product_id = $mainProduct->product_id;
        $stock_entry_item->warehouse_to = Warehouse::where('is_central', '1')->first()->warehouse_id;


        if(!$stock_entry_item->status){
            $stock_entry_item->status = 1;
        }

        $stock_entry_item->save();

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

        return $stock_entry_item->stock_entry_item_id;
    }


     /**
     * Fogadja a bejövő rendeléseket és menti az adatbázisba
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOrder(Request $request)
    {
        // Validáljuk a bejövő adatokat
        /*$request->validate([
            'order_id' => 'required|string|unique:webshop_orders,order_id',
            'customer_id' => 'nullable|integer|exists:customers,customer_id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'total' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric',
            'meta' => 'sometimes|array',
            'meta.*.meta_key' => 'required_with:meta|string',
            'meta.*.meta_value' => 'required_with:meta|string',
        ]);*/

        // Ellenőrizzük, hogy a vevő létezik-e
        $email = $request->billing['email'];
        $customer_find = Customer::where('email', $email)->first();
        if ($customer_find) {
            $customer_id = $customer_find->customer_id;
        } else {
            
            $customer = Customer::create([
                'name' => $request->billing['last_name'] . ' ' . $request->billing['first_name'],
                'email' => $request->billing['email'],
                'is_company' => 0,
                'phone' => $request->billing['phone'],
                'payment_id' => 7,
                'status' => 1
            ]);

            $customer_id = $customer->customer_id;
            //SAVE CUSOMER ADDRESSES
            //Lakcím
            $lc_address = new CustomerAddress();
            $zip_one = Zip::where('name', $request->billing['postcode'])->first();
            $city_one = City::where('name', $request->billing['city'])->first();
    
            $lc_address->customer_id = $customer_id;
            $lc_address->address_type = 'Lakcím';
            $lc_address->zip = $zip_one->name;
            $lc_address->zip_id = $zip_one->id;
            $lc_address->city = $city_one->name;
            $lc_address->city_id = $city_one->id;
            $lc_address->address = $request->billing['address_1'];
            $lc_address->save();
            //Szállítási cím
            $sz_address = new CustomerAddress();
            $zip_two = Zip::where('name', $request->shipping['postcode'])->first();
            $city_two = City::where('name', $request->shipping['city'])->first();

            $sz_address->customer_id = $customer_id;
            $sz_address->address_type = 'Szállítási cím';
            $sz_address->zip = $zip_two->name;
            $sz_address->zip_id = $zip_two->id;
            $sz_address->city = $city_two->name;
            $sz_address->city_id = $city_two->id;
            $sz_address->address = $request->shipping['address_1'];
            $sz_address->save();
        }

        // Ellenőrizzük, hogy a státusz létezik-e
        $status_id = OrderStatus::where('slug', $request->status)->first()->id;

        // Létrehozzuk a megrendelést
        $order = WebshopOrder::create([
            'order_id' => $request->order_id,
            'customer_id' => $customer_id,
            'order_date' => $request->order_date,
            'status' => $request->status,
            'status_id' => $status_id ?: 1,
            'total' => $request->total,
            'shipping_name' => $request->extra['shipping_name'] ?: null,
            'shipping_cost' => $request->extra['shipping_cost'] ?: 0,
            'cod_fee' => $request->extra['cash_on_delivery_fee'] ?: 0,
        ]);

        // Létrehozzuk a rendelés tételeit
        foreach ($request->items as $item) {
            WebshopOrderItem::create([
                'order_id' => $order->id,
                'shop_product_id' => $item['product_id'],
                'erp_product_id' => 0,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['quantity'] * $item['price'],
            ]);
        }

        // Létrehozzuk a rendelés metaadatait
        if ($request->has('meta')) {
            foreach ($request->meta as $meta) {
                WebshopOrderMeta::create([
                    'order_id' => $order->id,
                    'meta_key' => $meta['meta_key'],
                    'meta_value' => $meta['meta_value'],
                ]);
            }
        }

        // Mentjük a vevő és szállítási adatokat
        BillingAddress::create([
            'order_id' => $order->id,
            'customer_id' => $customer_id,
            'first_name' => $request->billing['first_name'],
            'last_name' => $request->billing['last_name'],
            'company' => $request->billing['company'] ?: null,
            'address_1' => $request->billing['address_1'],
            'address_2' => $request->billing['address_2'] ?: null,
            'city' => $request->billing['city'],
            'state' => $request->billing['state'],
            'postcode' => $request->billing['postcode'],
            'country' => $request->billing['country'],
            'email' => $request->billing['email'],
            'phone' => $request->billing['phone'],
        ]);

        ShippingAddress::create([
            'order_id' => $order->id,
            'customer_id' => $customer_id,
            'first_name' => $request->shipping['first_name'],
            'last_name' => $request->shipping['last_name'],
            'company' => $request->shipping['company'] ?: null,
            'address_1' => $request->shipping['address_1'],
            'address_2' => $request->shipping['address_2'] ?: null,
            'city' => $request->shipping['city'],
            'state' => $request->shipping['state'],
            'postcode' => $request->shipping['postcode'],
            'country' => $request->shipping['country'],
        ]);

        Log::info('Billing and shipping addresses saved successfully for order: ', ['order_id' => $order->id]);

        return response()->json(['message' => 'Order created successfully'], 201);
    }




}
