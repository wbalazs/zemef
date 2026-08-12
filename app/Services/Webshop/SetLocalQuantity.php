<?php

namespace App\Services\Webshop;

use App\Models\InternalRelationTransit\InternalRelationTransit;
use App\Models\Route\Route;
use App\Models\WebshopOrder;
use App\Models\StockEntryItem;
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

class SetLocalQuantity
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
        $this->updateLocalQuantity($this->data['product_id'], $this->data['quantity']);

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

   public function updateLocalQuantity($productId, $quantity)
    {
        $stock_entry_item = StockEntryItem::where('product_id', $productId)->first();
            if ($stock_entry_item) {
                Log::info('stock_entry_item: ', ['stock_entry_item' => $stock_entry_item->id]);

                $stock_entry_item->quantity = ($stock_entry_item->quantity - 1);
                $stock_entry_item->save();
            }
    }
        
}
