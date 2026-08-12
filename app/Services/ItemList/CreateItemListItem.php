<?php
// app/Services/ItemList/CreateItemListItem.php

namespace App\Services\ItemList;

use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ItemList\ItemList;
use Carbon\Carbon;
use Auth;

class CreateItemListItem
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function From(array $data): bool
    {
        return (new self($data))->handle();
    }

    public function handle(): bool
    {
        return $this->save();
    }

    protected function save(): bool
    {
        $itemList = ItemList::findOrFail($this->data['item_list_id']);
        $storage_id = $this->data['storage_id'];
        $is_unique = $this->data['is_unique'];
        $quantity = $this->data['quantity'] ?? 1;

        // Find or create product type
        $product = Product::where('name', $this->data['name'])->first();
        if (!$product) {
            $product = $this->createProduct();
        }

        if ($is_unique) {
            // Create individual inventory records for each piece
            for ($i = 0; $i < $quantity; $i++) {
                $this->createInventoryItem($itemList, $product, $storage_id, 1);
            }
        } else {
            // Single inventory record with total quantity
            $this->createInventoryItem($itemList, $product, $storage_id, $quantity);
        }

        return true;
    }

    protected function createProduct(): Product
    {
        $product = new Product();
        $product->name = $this->data['name'];
        $product->short_description = $this->data['description'];
        $product->long_description = $this->data['description'];
        $product->status = 3; // Active
        $product->is_unique = $this->data['is_unique'] ? 1 : 0;
        $product->weight = $this->data['weight'] ?? 0;
        $product->real_price = $this->data['oneprice'] ?? 0;
        $product->created_at = Carbon::now();
        $product->updated_at = Carbon::now();
        $product->save();

        if (!empty($this->data['factory_number'])) {
            $product->code = $this->data['factory_number'];
            $product->save();
        }

        return $product;
    }

    protected function createInventoryItem(ItemList $itemList, Product $product, int $storage_id, int $quantity): void
    {
        $inventory = new Inventory();
        $inventory->product_id = $product->product_id;
        $inventory->code = $inventory->generateInventoryCode($product->product_id); // új
        $inventory->name = $product->name;
        $inventory->description = $this->data['description'];
        $inventory->factory_number = $this->data['factory_number'] ?? null;
        $inventory->weight = $this->data['weight'] ?? 0;
        $inventory->quantity = $quantity;
        $inventory->oneprice = $this->data['oneprice'];
        $inventory->price = $this->data['price'] ?? ($this->data['oneprice'] * $quantity);
        $inventory->status = 2; // függőben (még nem fizikailag átvéve)
        $inventory->storage_id = $storage_id;
        $inventory->source_type = 'item_list';
        $inventory->source_header_id = $itemList->id;
        $inventory->warehouse_to = $storage_id;
        $inventory->created_at = Carbon::now();
        $inventory->updated_at = Carbon::now();
        $inventory->save();
    }
}