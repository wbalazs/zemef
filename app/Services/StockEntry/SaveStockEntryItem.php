<?php

namespace App\Services\StockEntry;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Employee;
use App\Models\StockEntry;
use App\Models\StockEntryItem;
use App\Models\ItemList\ItemList;

class SaveStockEntryItem
{

    /** @var Request The provided parameters. */
    protected $request;
    
    /**
     *  Create an instance of the AssignmentData service.
     *
     *  @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;

    }
    
    /**
     *  Create users,profiles records and return the associated user.
     *
     *  @return self
     */
    public function handle()
    {
        $this->save($this->request);

        return true;
    }

     /**
     *  Create and return an string.
     *
     *  @param  array  $data

     *  @return string
     */
    public static function From(Request $request): bool
    {
        return (new self($request))->handle();
    }

   public function save(Request $request)
    {
        $item_list = ItemList::find($request->get('item_list_id'));
        
        $stock_entry = StockEntry::find($item_list->stock_entry_id)?->update(['fulfillment_date' =>date('Y-m-d H:i:s')]);
        $is_unique = filter_var($request->sei_is_unique, FILTER_VALIDATE_BOOLEAN);
        //Egyedi?
        if ($is_unique) {

            if(is_numeric($request->sei_name)){
                $mainProduct = Product::findOrFail($request->sei_name);
            }else{
                $mainProduct = new Product();
                $mainProduct->name = $request->sei_name;
                $mainProduct->short_description = $request->sei_description;
                $mainProduct->status = 3;
                $mainProduct->is_unique = 1;
                $mainProduct->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
                $mainProduct->weight = 0;
                $mainProduct->save();
            }
            //ahány darab annyiszor mentjük - mert egyedi
            for($i = 0;$i < $request->sei_quantity; $i++)
            {
                $product = new Product();
                $product->main_product_id = $mainProduct->product_id;
                $product->name = $mainProduct->name;
                $product->short_description = $request->sei_description;
                $product->status = 3;
                $product->is_unique = 1;
                $product->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
                $product->weight = $request->sei_weight ? $request->sei_weight : 0;
                $product->real_price = $request->sei_oneprice;
                $product->save();

                //PTI INSERTS
                $stock_entry_item = new StockEntryItem();
                $stock_entry_item->stock_entry_id = $item_list->json['stock_entry_id'];
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
                $stock_entry_item->save();
            }
        } else {
            $product = new Product();
            $product->main_product_id = 0;
            $product->name = $request->sei_name;
            $product->short_description = $request->sei_description;
            $product->status = 3;
            $product->is_unique = 0;
            $product->storage_id = Warehouse::where('is_central', '1')->first()->warehouse_id;
            $product->weight = $request->sei_weight ? $request->sei_weight : 0;
            $product->real_price = $request->sei_oneprice;
            $product->save();
        
            //PTI INSERTS
            $stock_entry_item = new StockEntryItem();
            $stock_entry_item->stock_entry_id = $item_list->json['stock_entry_id'];
            $stock_entry_item->name = $request->sei_name;
            $stock_entry_item->description = $request->sei_description;
            $stock_entry_item->quantity = $request->sei_quantity;
            $stock_entry_item->weight = $request->sei_weight;
            $stock_entry_item->factory_number = $request->sei_factory_number;
            $stock_entry_item->oneprice = $request->sei_oneprice;
            $stock_entry_item->price = $request->sei_oneprice*$request->sei_quantity;
            $stock_entry_item->product_id = $product->product_id;
            $stock_entry_item->main_product_id = 0;
            $stock_entry_item->warehouse_to = Warehouse::where('is_central', '1')->first()->warehouse_id;
            $stock_entry_item->save();
        }
    }
        
}
