<?php

namespace Tests\Feature;

use App\Http\Controllers\ConsignmentReceiptController;
use App\Models\ConsignmentReceipt;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ConsignmentReceiptCancellationTest extends TestCase
{
    private array $temporaryTables = [];
    private $eventDispatcher;
    private ConsignmentReceiptController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcher = Model::getEventDispatcher();
        Model::unsetEventDispatcher();
        foreach (['consignment_receipts', 'inventory', 'serial_numbers', 'images'] as $table) {
            $definitions = array_map(function ($column) {
                return '`' . $column->Field . '` ' . $column->Type
                    . (str_contains($column->Extra, 'auto_increment') ? ' NOT NULL AUTO_INCREMENT PRIMARY KEY' : ' NULL');
            }, DB::select('SHOW COLUMNS FROM `' . $table . '`'));
            DB::statement('CREATE TEMPORARY TABLE `' . $table . '` (' . implode(', ', $definitions) . ') ENGINE=InnoDB');
            $this->temporaryTables[] = $table;
        }
        $this->controller = (new \ReflectionClass(ConsignmentReceiptController::class))->newInstanceWithoutConstructor();
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryTables as $table) {
            DB::statement('DROP TEMPORARY TABLE `' . $table . '`');
        }
        if ($this->eventDispatcher) Model::setEventDispatcher($this->eventDispatcher);
        parent::tearDown();
    }

    private function fixture(string $suffix, bool $closed = true): array
    {
        $receipt = new ConsignmentReceipt();
        $receipt->forceFill([
            'ticket_number' => 'TEST-' . $suffix, 'customer_id' => 123,
            'business_id' => 1, 'storage_id' => 84, 'employee_id' => 1,
            'currency_id' => 1, 'currency_value' => 1, 'expire_date' => '2027-01-01',
            'invoice_number' => 'INVOICE-' . $suffix, 'contract_number' => 'CONTRACT-' . $suffix,
            'is_hallmark_requested' => 1, 'status' => 2,
            'printed_at' => $closed ? now() : null, 'is_storno' => 0,
            'header_data' => ['customer_is_company' => 1, 'tax_identifier' => '12345678-1-12',
                'sales_condition_3' => 1, 'sales_condition_3_amount' => '50000',
                'sales_condition_hallmark_cost' => '15000'],
        ])->save();
        $item = new Inventory();
        $item->forceFill([
            'product_id' => 1, 'sorszam' => 1, 'code' => 'TEST-' . $suffix . '/1',
            'name' => 'Test item', 'description' => 'Copy all item data',
            'factory_number' => 'FACTORY-' . $suffix, 'weight' => 12.5, 'quantity' => 3,
            'oneprice' => 3000, 'price' => 9000, 'status' => 2, 'storage_id' => 84,
            'source_type' => 'consignment', 'source_header_id' => $receipt->consignment_receipt_id,
            'warehouse_to' => 84, 'termek_tipus_id' => null,
            'item_data' => ['sorszam' => 1, 'hallmarks' => 0, 'webshop' => 1,
                'sell_elsewhere' => 0, 'karat_id' => '1', 'colors' => [1, 2]],
        ])->save();
        return [$receipt, $item];
    }

    private function cancelRequest(ConsignmentReceipt $receipt, bool $copy = false, string $reason = 'Customer changed their mind'): Request
    {
        return Request::create('/', 'POST', [
            'consignment_receipt_id' => $receipt->consignment_receipt_id,
            'storno_reason' => $reason, 'create_new' => $copy ? 1 : 0,
        ]);
    }

    public function test_cancellation_creates_a_complete_editable_replacement(): void
    {
        [$original, $oldItem] = $this->fixture('REPLACE');
        $attributes = $original->fresh()->getAttributes();
        $response = $this->controller->cancelConsignmentReceipt($this->cancelRequest($original, true));
        $this->assertSame(200, $response->getStatusCode());
        $data = $response->getData(true)['data'];
        $copy = ConsignmentReceipt::findOrFail($data['new_consignment_receipt_id']);
        $storno = ConsignmentReceipt::findOrFail($data['consignment_receipt_id']);
        foreach (['customer_id', 'business_id', 'storage_id', 'employee_id', 'currency_id', 'currency_value',
            'expire_date', 'invoice_number', 'contract_number', 'header_data', 'is_hallmark_requested'] as $field) {
            $this->assertEquals($attributes[$field], $copy->getRawOriginal($field), $field);
        }
        $this->assertNull($copy->printed_at);
        $this->assertNull($copy->cancelled_at);
        $this->assertNull($copy->storno_cr_id);
        $this->assertSame(0, (int) $copy->is_storno);
        $this->assertSame(1, (int) $copy->status);
        $this->assertNotEquals($original->ticket_number, $copy->ticket_number);
        $this->assertNotEquals($storno->ticket_number, $copy->ticket_number);
        $copyItem = $copy->inventoryItems()->firstOrFail();
        $this->assertEquals($oldItem->item_data, $copyItem->item_data);
        $this->assertSame(3, (int) $copyItem->quantity);
        $this->assertSame(2, (int) $copyItem->status);
        $this->assertSame($oldItem->factory_number, $copyItem->factory_number);
        $this->assertSame(4, (int) $original->fresh()->status);
        $this->assertSame(0, (int) $oldItem->fresh()->status);
        $this->assertSame(-3, (int) $storno->inventoryItems()->firstOrFail()->quantity);
        $this->assertSame('Customer changed their mind', $storno->storno_reason);
        $this->assertStringContainsString('/' . $copy->consignment_receipt_id . '/edit', $data['redirect_url']);
        $this->assertStringContainsString('/storno', $data['print_url']);
        $this->assertSame(409, $this->controller->cancelConsignmentReceipt($this->cancelRequest($original))->getStatusCode());

        $request = Request::create('/', 'POST', ['consignment_receipt_id' => $copy->consignment_receipt_id, 'inventory_id' => $copyItem->inventory_id]);
        $this->assertSame(200, $this->controller->deleteConsignmentReceiptItem($request)->getStatusCode());
        $this->assertNull($copyItem->fresh());
        $this->assertNotNull($oldItem->fresh());
        $request = Request::create('/', 'POST', ['consignment_receipt_id' => $original->consignment_receipt_id, 'inventory_id' => $oldItem->inventory_id]);
        $this->assertSame(409, $this->controller->deleteConsignmentReceiptItem($request)->getStatusCode());
    }

    public function test_plain_cancellation_and_open_receipt_rejection(): void
    {
        [$closed, $item] = $this->fixture('PLAIN');
        $response = $this->controller->cancelConsignmentReceipt($this->cancelRequest($closed));
        $this->assertNull($response->getData(true)['data']['new_consignment_receipt_id']);
        $this->assertSame(0, (int) $item->fresh()->status);
        [$open] = $this->fixture('OPEN', false);
        $this->assertSame(409, $this->controller->cancelConsignmentReceipt($this->cancelRequest($open))->getStatusCode());
    }

    public function test_a_nonblank_reason_is_required(): void
    {
        [$receipt] = $this->fixture('REASON');
        $this->expectException(ValidationException::class);
        $this->controller->cancelConsignmentReceipt($this->cancelRequest($receipt, false, '  '));
    }

    public function test_failure_rolls_back_cancellation_and_replacement(): void
    {
        [$original, $item] = $this->fixture('ROLLBACK');
        $receiptCount = ConsignmentReceipt::count();
        $itemCount = Inventory::count();
        Model::setEventDispatcher(new Dispatcher($this->app));
        ConsignmentReceipt::saving(function ($receipt) use ($original) {
            if ($receipt->consignment_receipt_id === $original->consignment_receipt_id && (int) $receipt->status === 4) {
                throw new \RuntimeException('Injected failure');
            }
        });
        try {
            $this->controller->cancelConsignmentReceipt($this->cancelRequest($original, true));
            $this->fail('Expected an injected failure.');
        } catch (\RuntimeException $error) {
            $this->assertSame('Injected failure', $error->getMessage());
        }
        $this->assertSame($receiptCount, ConsignmentReceipt::count());
        $this->assertSame($itemCount, Inventory::count());
        $this->assertNull($original->fresh()->cancelled_at);
        $this->assertSame(2, (int) $item->fresh()->status);
    }
}