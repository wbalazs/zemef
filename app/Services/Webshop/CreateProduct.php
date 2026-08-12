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
use App\Models\CustomerAddress;
use App\Models\OrderStatus;
use App\Models\Address\Zip;
use App\Models\Address\City;
use Illuminate\Support\Facades\Log;
use DateTime;
use function trans;
use DB;

class CreateProduct
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
            //
            $this->updateStockQuantity($data['product_id'], $data['quantity']);
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

   public function updateStockQuantity($productId, $quantity)
    {
        $url =  env('WC_API_URL') . '/products/' . $productId;

        $data = json_encode([
            //'stock_quantity' => 1,
            //'stock_status' => 'instock',
            //'bundle_stock_status' => 'instock',
            'stock_quantity' => 0,
            'stock_status' => 'outofstock',
            'bundle_stock_status' => 'outofstock',
        ]);
        
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_USERPWD => env('WC_CONSUMER_KEY') . ':' . env('WC_CONSUMER_SECRET'),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => 'Laravel/WooCommerce-API'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            Log::info("Stock updated successfully for product ID: " . $productId);
        } else {
            Log::info("Error updating stock (HTTP $httpCode): " . ($error ?: $response));
        }
    }
        
}
