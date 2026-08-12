<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Inventory;

class Reseed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reseeds the image folders';

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
     * @return int
     */
    public function handle()
    {
        $inventories = Inventory::whereNull('sku')->get();
foreach ($inventories as $inv) {
    $productCode = $inv->product->code ?? 'INV';
    $inv->sku = $productCode . '-' . $inv->inventory_id;
    $inv->save();
}
$inventories = Inventory::whereNull('ws_product_id')->get();
foreach ($inventories as $inv) {
    $product = $inv->product;
    if ($product && $product->ws_product_id) {
        $inv->ws_product_id = $product->ws_product_id;
        $inv->save();
    }
}
        $this->info('Reseed completed successfully.');
        return 0;
    }
}
