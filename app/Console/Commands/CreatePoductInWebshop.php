<?php

namespace App\Console\Commands;

use \Exception;
use Illuminate\Console\Command;
use App\Services\Webshop\CreateProduct;

class CreatePoductInWebshop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:product_in_webshop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
                CreatePoductInWebshop::create_woocommerce_products($jsonArray);

                sleep(1); // Be polite to the API server
                
            } while (count($jsonArray)); // Continue until last page

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public static function create_woocommerce_products($page = 1) {
        $product_data = [
            'name' => 'Test Product',
            'type' => 'simple',
            'regular_price' => '19.99',
            'description' => 'This is a test product created via API.',
            'short_description' => 'Test product.',
            'categories' => [
                ['id' => 9] // Category ID (replace with yours)
            ],
            'images' => [
                ['src' => 'https://example.com/image.jpg'] // Image URL
            ]
        ];

        $data = json_encode($product_data);

        $url =  env('WC_API_URL') . '/products';
        //$url .= '&_fields=id,name,stock_quantity,slug,sku,total_sales,price,regular_price,sale_price';
        $url .= '&consumer_key=' . env('WC_CONSUMER_KEY') . '&consumer_secret=' .env('WC_CONSUMER_SECRET'); 

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url . '?consumer_key=' . env('WC_CONSUMER_KEY') . '&consumer_secret=' . env('WC_CONSUMER_SECRET'),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // Enable for production
        ]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON decode error: " . json_last_error_msg());
        }

        return $data;
    }
}
