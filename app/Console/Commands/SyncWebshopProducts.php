<?php

namespace App\Console\Commands;

use \Exception;
use Illuminate\Console\Command;
use App\Services\Webshop\StoreWebshopProduct;

class SyncWebshopProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete data that is more than 3 days older';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $page = 1;
        $allProducts = [];
         
        try {
            do {
                $jsonArray = SyncWebshopProducts::get_woocommerce_products($page);
                   foreach($jsonArray as $product) {
                        $this->info("Termék feldolgozása: " . $product['sku']);
                        $storage = \App\Models\Warehouse::where('name', $product['shipping_class'])->first();
                        if ($storage) {
                            $prod = \App\Models\Product::where('sku', trim($product['sku']))->first();
                            if ($prod) {
                                $this->info("Termék megvan: " . $prod->name);
                                $prod->storage_id = $storage->warehouse_id;
                                $prod->save();  
                            }
                        }
                        $this->info($storage ? "Raktár létezik: " . $storage->name : "Nincs raktár: " . $product['shipping_class']);
                    }
                //StoreWebshopProduct::From($jsonArray);
                
                $page++;

                sleep(1); // Be polite to the API server
                
            } while (count($jsonArray)); // Continue until last page

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public static function get_woocommerce_products($page = 1) {
        
        $url =  env('WC_API_URL') 
        . '/products?consumer_key=' . env('WC_CONSUMER_KEY') 
        . '&consumer_secret=' .env('WC_CONSUMER_SECRET')
        . '&page=' . $page . '&per_page=100';
         // Build the curl command
        $cmd = "curl --location --silent " . escapeshellarg($url) . 
           " --header " . escapeshellarg('Cookie: shop_per_page=100');
    
        // Execute the command
        exec($cmd, $output, $return_var);
        
        if ($return_var !== 0) {
            throw new Exception("Curl command failed with error code: $return_var");
        }
        
        $json = implode("\n", $output);
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON decode error: " . json_last_error_msg());
        }

        return $data;
    }
}
