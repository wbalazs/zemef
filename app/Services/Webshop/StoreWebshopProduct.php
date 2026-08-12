<?php

namespace App\Services\Webshop;

use App\Models\InternalRelationTransit\InternalRelationTransit;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\StockEntry\SaveStockEntryItemArray;
use Illuminate\Support\Facades\Log;
use DateTime;
use function trans;
use DB;

class StoreWebshopProduct
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
        //attributes['name'] == 'product_id' options
        if (Product::where('sku', trim($data['sku']))->first()) {
            return false;
        }
        $description = trim(strip_tags($data['description']));
        $new_description = str_replace("&nbsp;", '', $description);
        SaveStockEntryItemArray::From(
        [
            'ws_product_id' => $data['id'],
            'item_list_id' => null,
            'sei_name' => $data['name'],
            'sku' => trim($data['sku']),
            'sei_description' => $new_description,
            'sei_weight' => strlen(trim($data['weight'])) ?? null,
            'sei_oneprice' => $data['price'],
            'sei_factory_number' => '',//csak kényszerlista
            'sei_comment' => '',
            'sei_quantity' => $data['stock_quantity'],
            'shipping_class' => $data['shipping_class'],
            'images' => $data['images'],
        ]);

        Log::info('Import product: ', ['sku' =>  trim($data['sku'])]);

        
    }
        
}
