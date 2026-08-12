<?php

namespace App\Services\StockEntry;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Employee;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\ItemList\ItemList;
use App\Services\StockEntry\getNextSerial;
use App\Models\ZImage;
use Intervention\Image\ImageManagerStatic as Image;
use Auth;
use File;

class SaveStockEntryItemArray
{

    /** @var Array The provided parameters. */
    protected $data;
    
    /**
     *  Create an instance of the AssignmentData service.
     *
     *  @return void
     */
    public function __construct(Array $data)
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
        $this->save($this->data);

        return true;
    }

     /**
     *  Create and return an string.
     *
     *  @param  array  $data

     *  @return string
     */
    public static function From(Array $data): bool
    {
        return (new self($data))->handle();
    }

   public function save(Array $data)
    {
        $storage = Warehouse::where('name', $data['shipping_class'])->first();
        if(!$storage) {
            $storage = Warehouse::create([
                'name' => $data['shipping_class'],
                'is_central' => 0,
                'status' => 1,
                'is_store' => 1,
                'address' => 'Importált raktár',
            ]);
        }
        $storage_id = $storage->warehouse_id;
        $business_id = $storage->businesses?->first()?->business_id;
        $customer_id = config('zemef.import_customer_id');
        $employee_id = Auth::user() ? Auth::user()->employee->employee_id : config('zemef.import_employee_id') ?? 23;
        $currency_id = config('zemef.import_currency_id');

        $serial = (new getNextSerial())->get();
        // Formátum: XX/EEEE/00001 - ahol XX - bolti azonosító
        $ticket_number = 'EBEV/'. $business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        //csak egy Entry és sok items!
        $stock_entry = StockEntry::create(
            [
                'ticket_number' => $ticket_number,
                'business_id' => $business_id,
                'customer_id' => $customer_id,
                'employee_id' => $employee_id,
                'currency_id' => $currency_id,
                'storage_id' => $storage_id,
                'fulfillment_date' => date('Y-m-d H:i:s'),
            ]
        );

        $is_unique = config('zemef.import_unique');
        //Egyedi?
        if ($is_unique) {
            //meg kell nézni, hogy van-e már ilyen nevű termék, ha nincs, akkor létre kell hozni egy fő terméket, és ahány darab van, annyi terméket létre kell hozni külön-külön, és mindegyiknek a main_product_id-je a fő termék id-je lesz
            $mainProduct = Product::where('name', $data['sei_name'])->first();

            if(!$mainProduct){
                $mainProduct = new Product();
                $mainProduct->ws_product_id = $data['ws_product_id'];
                $mainProduct->sku = $data['sku'];
                $mainProduct->name = $data['sei_name'];
                $mainProduct->short_description = $data['sei_description'];
                $mainProduct->long_description = $data['sei_description'];
                $mainProduct->status = 3;
                $mainProduct->storage_id = $storage_id;
                $mainProduct->weight = 0;
                $mainProduct->save();
            }
                        
            $this->saveProductImage($mainProduct, $data);
            //ahány darab annyiszor mentjük - mert egyedi
            for($i = 0;$i < $data['sei_quantity']; $i++)
            {
                $product = new Product();
                $product->main_product_id = $mainProduct->product_id;
                $product->ws_product_id = $data['ws_product_id'];
                $product->name = $mainProduct->name;
                $product->sku = $data['sku'];
                $product->short_description = $data['sei_description'];
                $product->long_description = $data['sei_description'];
                $product->status = 1;
                $product->is_unique = 0;
                $product->storage_id = $storage_id;
                $product->weight = $data['sei_weight'] ? $data['sei_weight'] : 0;
                $product->real_price = $data['sei_oneprice'];
                $product->save();

                //PTI INSERTS
                $stock_entry_item = new StockEntryItem();
                $stock_entry_item->stock_entry_id = $stock_entry->stock_entry_id;
                $stock_entry_item->name = $product->name;
                $stock_entry_item->description = $data['sei_description'];
                $stock_entry_item->quantity = 1;
                $stock_entry_item->weight = $data['sei_weight'];
                $stock_entry_item->factory_number = $data['sei_factory_number'];
                $stock_entry_item->oneprice = $data['sei_oneprice'];
                $stock_entry_item->price = $data['sei_oneprice']*1;
                $stock_entry_item->comment = $data['sei_comment'];
                $stock_entry_item->product_id = $product->product_id;
                $stock_entry_item->main_product_id = $mainProduct->product_id;
                $stock_entry_item->warehouse_to = Warehouse::where('is_central', '1')->first()->warehouse_id;
                $stock_entry_item->save();

                $this->saveProductImage($product, $data);
            }
        } else {
            $product = new Product();
            $product->main_product_id = 0;
            $product->name = $data['sei_name'];
            $product->short_description = $data['sei_description'];
            $product->status = 3;
            $product->is_unique = 0;
            $product->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
            $product->weight = $data['sei_weight'] ? $data['sei_weight'] : 0;
            $product->real_price = $data['sei_oneprice'];
            $product->save();
        
            //PTI INSERTS
            $stock_entry_item = new StockEntryItem();
            $stock_entry_item->stock_entry_id = $item_list->stock_entry_id;
            $stock_entry_item->name = $data['sei_name'];
            $stock_entry_item->description = $data['sei_description'];
            $stock_entry_item->quantity = $data['sei_quantity'];
            $stock_entry_item->weight = $data['sei_weight'];
            $stock_entry_item->factory_number = $data['sei_factory_number'];
            $stock_entry_item->oneprice = $data['sei_oneprice'];
            $stock_entry_item->price = $data['sei_oneprice']*$data['sei_quantity'];
            $stock_entry_item->product_id = $product->product_id;
            $stock_entry_item->main_product_id = 0;
            $stock_entry_item->warehouse_to = Warehouse::where('is_central', '1')->first()->warehouse_id;
            $stock_entry_item->save();

            $this->saveProductImage($product, $data);
        }
        
    }
    
    private function saveProductImage(Product $product, $data) {
        //termék képek
        foreach ($data['images'] as $imageData) {
            $imageUrl = $imageData['src'];
            $imageName = basename($imageUrl); // Extracts '391.jpg' from the URL

            $image = new ZImage();
            $image->resource_id = $product->product_id;
            $image->image_path = $product->product_id .'/'. $imageName;
            $image->image_date = null;
            $image->is_primary = 1;
            $image->created_at = date('Y-m-d H:i:s');
            $image->module = 'product';
            $image->save();
            
            $authenticatedUrl = $imageUrl . '?consumer_key=' . env('WC_CONSUMER_KEY') . '&consumer_secret=' . env('WC_CONSUMER_SECRET');

            $path = public_path('/document_storage/product_images/' . $product->product_id . '/');
            
            // Create directory if it doesn't exist
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

             // Initialize cURL session
            $ch = curl_init($authenticatedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            
            // Execute the request
            $imageData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($httpCode === 200 && $imageData !== false) {
                // Save the image using Intervention Image
                $image = Image::make($imageData);
                $image->save($path . $imageName);
                
                echo "Letöltve: " . $imageName . " - ";
            } else {
                echo "Failed to download: " . $imageName . " (HTTP Code: " . $httpCode . ") - ";
                
                // Check for cURL errors
                if (curl_error($ch)) {
                    echo "cURL Error: " . curl_error($ch) . " - ";
                }
            }
            
            // Close cURL session
            curl_close($ch);
        }
    }
}
