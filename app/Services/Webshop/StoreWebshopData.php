<?php

namespace App\Services\Webshop;

use App\Models\InternalRelationTransit\InternalRelationTransit;
use App\Models\Route\Route;
use App\Models\WebshopOrder;
use App\Models\WebshopOrderItem;
use App\Models\WebshopOrderMeta;
use App\Models\BillingAddress;
use App\Models\ShippingAddress;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\CustomerAddress;
use App\Models\OrderStatus;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Services\Webshop\SetLocalQuantity;
use Illuminate\Support\Facades\Log;
use DateTime;
use function trans;
use DB;

class StoreWebshopData
{

    /** @var array The provided parameters. */
    protected $data;
    
    /**
     *  Create an instance of the AssignmentData service.
     *
     *  @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;

    }
    
    /**
     *  Create users,profiles records and return the associated user.
     *
     *  @return self
     */
    public function handle()
    {
        foreach($this->data as $data){
            $this->storeData($data);
        }

        return true;
    }

     /**
     *  Create and return an string.
     *
     *  @param  array  $data

     *  @return string
     */
    public static function From(array $data): bool
    {
        return (new self($data))->handle();
    }

    public function storeData($data) {
        
        if (WebshopOrder::where('order_id', $data['order_key'])->first()) {
            return false;
        }
        Log::info('Start: ');

        // Ellenőrizzük, hogy a vevő létezik-e
        if ((! array_key_exists('billing', $data)) || (! array_key_exists('shipping', $data)))  {
            Log::info('hiba: ');
            return false;
        }
        
        $customer_find = Customer::where('email', $data['billing']['email'])->first();
        if ($customer_find) {
            $customer_id = $customer_find->customer_id;
        } else {
            
            $customer = Customer::create([
                'name' => $data['billing']['last_name'] . ' ' . $data['billing']['first_name'],
                'email' => $data['billing']['email'],
                'is_company' => 0,
                'phone' => $data['billing']['phone'],
                'payment_id' => 7,
                'status' => 1
            ]);

            $customer_id = $customer->customer_id;
            //SAVE CUSOMER ADDRESSES
            //Lakcím
            $lc_address = new CustomerAddress();
            $zip_one = Zip::where('name', $data['billing']['postcode'])->first();
            $city_one = City::where('name', $data['billing']['city'])->first();
            
            if ($zip_one && $city_one) {
                $lc_address->customer_id = $customer_id;
                $lc_address->address_type = 'Lakcím';
                $lc_address->zip = $zip_one->name;
                $lc_address->zip_id = $zip_one->id;
                $lc_address->city = $city_one->name;
                $lc_address->city_id = $city_one->id;
                $lc_address->address = $data['billing']['address_1'];
                $lc_address->save();
            }
            
            //Szállítási cím
            $sz_address = new CustomerAddress();
            $zip_two = Zip::where('name', $data['shipping']['postcode'])->first();
            $city_two = City::where('name', $data['shipping']['city'])->first();
            
            if ($zip_two && $city_two) {
                $sz_address->customer_id = $customer_id;
                $sz_address->address_type = 'Szállítási cím';
                $sz_address->zip = $zip_two->name;
                $sz_address->zip_id = $zip_two->id;
                $sz_address->city = $city_two->name;
                $sz_address->city_id = $city_two->id;
                $sz_address->address = $data['shipping']['address_1'];
                $sz_address->save();
            }
            
        }

        // Ellenőrizzük, hogy a státusz létezik-e
        $status = OrderStatus::where('slug', $data['status'])->first();
        if(! $status) {
            $status = OrderStatus::create([
                'name' => $data['status'],
                'slug' => $data['status'],
            ]);
        }
        
        // Létrehozzuk a megrendelést
        $order = WebshopOrder::create([
            'order_id' => $data['order_key'],
            'customer_id' => $customer_id,
            'order_date' => $data['date_created'],
            'status' => $data['status'],
            'status_id' => $status ? $status->id : null,
            'total' => $data['total'],
            'shipping_name' => count($data['shipping_lines']) ? $data['shipping_lines'][0]['method_title'] : null,
            'shipping_cost' => count($data['shipping_lines']) ? $data['shipping_lines'][0]['total'] : 0,
            'cod_fee' => count($data['fee_lines']) ? $data['fee_lines'][0]['total'] : 0,
            'json' => json_encode($data),
        ]);

        // Létrehozzuk a rendelés tételeit
        foreach ($data['line_items'] as $item) {
            Log::info('Webshop tétel: ', $item);

            // 1. Közvetlen keresés inventory tételben a ws_product_id alapján
            $inventory = Inventory::where('ws_product_id', $item['product_id'])->first();

            // 2. Ha nincs ilyen inventory tétel, akkor próbáljuk a terméktípust megkeresni,
            //    és abból létrehozni egy új inventory tételt (vagy használni a meglévőket)
            if (!$inventory) {
                $product = Product::where('ws_product_id', $item['product_id'])->first();
                if (!$product) {
                    Log::warning('Ismeretlen termék webshopból, ws_product_id: ' . $item['product_id']);
                    // Csak a rendelési tételt rögzítjük
                    WebshopOrderItem::create([
                        'order_id' => $order->id,
                        'sku' => $item['sku'],
                        'shop_product_id' => $item['product_id'],
                        'erp_product_id' => null,
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $item['total'],
                    ]);
                    continue;
                }

                // Ha van terméktípus, de nincs konkrét inventory tétel, válasszunk vagy hozzunk létre
                $inventory = Inventory::where('product_id', $product->product_id)
                                    ->where('quantity', '>', 0)
                                    ->orderBy('created_at', 'asc')
                                    ->first();

                if (!$inventory) {
                    // Nincs elérhető készlet – csak naplózzuk
                    Log::error('Nincs elérhető inventory tétel a termékhez', ['product_id' => $product->product_id]);
                    WebshopOrderItem::create([
                        // ... rendelési tétel mentése erp_product_id nélkül vagy a product_id-val
                    ]);
                    continue;
                }
            }

            // 3. Ha van inventory tétel, csökkentsük a mennyiséget a megrendelt darabszámmal
            if ($item['quantity'] > 0) {
                $newQuantity = max(0, $inventory->quantity - $item['quantity']);
                $inventory->quantity = $newQuantity;
                $inventory->save();

                Log::info('Inventory készlet csökkentve', [
                    'inventory_id' => $inventory->inventory_id,
                    'ws_product_id' => $inventory->ws_product_id,
                    'eredeti_mennyiseg' => $inventory->getOriginal('quantity'),
                    'uj_mennyiseg' => $newQuantity,
                    'rendelt_mennyiseg' => $item['quantity'],
                ]);

                if ($newQuantity == 0) {
                    $inventory->status = 7; // eladva
                    $inventory->save();
                }
            }

            // 4. Webshop rendelési tétel mentése
            WebshopOrderItem::create([
                'order_id' => $order->id,
                'sku' => $item['sku'],
                'shop_product_id' => $item['product_id'],
                'erp_product_id' => $inventory->product_id ?? null,
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }
        Log::info('WebshopOrderItem ok: ');
        // Létrehozzuk a rendelés metaadatait
        foreach ($data['meta_data'] as $meta) {
             $metaValue = $meta['value'];
            // Ha az érték tömb vagy objektum, alakítsuk JSON stringgé
            if (is_array($metaValue) || is_object($metaValue)) {
                $metaValue = json_encode($metaValue, JSON_UNESCAPED_UNICODE);
            }
            WebshopOrderMeta::create([
                'order_id' => $order->id,
                'meta_key' => $meta['key'],
                'meta_value' => $metaValue,
            ]);
        }

        // Mentjük a vevő és szállítási adatokat
        BillingAddress::create([
            'order_id' => $order->id,
            'customer_id' => $customer_id,
            'first_name' => $data['billing']['first_name'],
            'last_name' => $data['billing']['last_name'],
            'company' => $data['billing']['company'] ?: null,
            'address_1' => $data['billing']['address_1'],
            'address_2' => $data['billing']['address_2'] ?: null,
            'city' => $data['billing']['city'],
            'state' => $data['billing']['state'],
            'postcode' => $data['billing']['postcode'],
            'country' => $data['billing']['country'],
            'email' => $data['billing']['email'],
            'phone' => $data['billing']['phone'],
        ]);

        ShippingAddress::create([
            'order_id' => $order->id,
            'customer_id' => $customer_id,
            'first_name' => $data['shipping']['first_name'],
            'last_name' => $data['shipping']['last_name'],
            'company' => $data['shipping']['company'] ?: null,
            'address_1' => $data['shipping']['address_1'],
            'address_2' => $data['shipping']['address_2'] ?: null,
            'city' => $data['shipping']['city'],
            'state' => $data['shipping']['state'],
            'postcode' => $data['shipping']['postcode'],
            'country' => $data['shipping']['country'],
        ]);

        Log::info('Billing and shipping addresses saved successfully for order: ', ['order_id' => $order->id]);

    }
        
}
