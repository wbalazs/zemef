<?php

namespace App\Services\ItemList;

use App\Models\ItemList\ItemList;
use App\Models\StockEntry;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\StockEntry\getNextSerial;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class ItemListImport
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $rows = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
            
            stream_filter_append($handle, 'convert.iconv.ISO-8859-1/UTF-8');

            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // Skip empty rows
                if (empty(array_filter($data))) continue;
                // Process only if we have data
                if (isset($data[0]) && !empty(trim($data[0]))) {
                    $fields = explode('$', $data[0]);
                    
                    // Ensure we have exactly 16 fields per row
                    if (count($fields) < 16) {
                        $fields = array_pad($fields, 16, null);
                    } elseif (count($fields) > 16) {
                        $fields = array_slice($fields, 0, 16);
                    }
                    
                    // Create row data with all 16 columns
                    $rows[] = [
                        'status' => 0, // Default status
                        'row1' => $this->cleanString(trim($fields[0] ?? '')),
                        'row2' => $this->cleanString(trim($fields[1] ?? '')),
                        'row3' => $this->cleanString(trim($fields[2] ?? '')),
                        'row4' => $this->cleanString(trim($fields[3] ?? '')),
                        'row5' => $this->cleanString(trim($fields[4] ?? '')),
                        'row6' => $this->cleanString(trim($fields[5] ?? '')),
                        'row7' => $this->cleanString(trim($fields[6] ?? '')),
                        'row8' => $this->cleanString(trim($fields[7] ?? '')),
                        'row9' => $this->cleanString(trim($fields[8] ?? '')),
                        'row10' => $this->cleanString(trim($fields[9] ?? '')),
                        'row11' => $this->cleanString(trim($fields[10] ?? '')),
                        'row12' => $this->cleanString(trim($fields[11] ?? '')),
                        'row13' => $this->cleanString(trim($fields[12] ?? '')),
                        'row14' => $this->cleanString(trim($fields[13] ?? '')),
                        'row15' => $this->cleanString(trim($fields[14] ?? '')),
                        'row16' => $this->cleanString(trim($fields[15] ?? '')),
                    ];
                }
            }
            fclose($handle);
        }
        // Create or update the ItemList with imported data
        return $this->createOrUpdateItemList($rows);
    }

    protected function createOrUpdateItemList(array $importRows)
    {
        $storage = Warehouse::where('is_central', '1')->first();
        $storage_id = $storage->warehouse_id;
        $business_id = $storage->businesses->first()->business_id;
        $customer_id = config('zemef.import_customer_id');
        $customer_name = config('zemef.import_customer_name');
        $employee_id = Auth::user()->employee?->employee_id;
        $currency_id = config('zemef.import_currency_id');

        $serial = (new getNextSerial())->get();
        // Formátum: XX/EEEE/00001 - ahol XX - bolti azonosító
        $ticket_number = 'EBEV/'. $business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
    
        $customer = Customer::find($customer_id);
        if (!$customer) {
            $customer = Customer::create([
                'customer_id' => $customer_id,
                'name' => $customer_name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Prepare the JSON data structure
        $jsonData = [
            'stock_entry_id' => null,
            'store' => ['store_id' => null, 'name' => 'Imported Data'],
            'amount' => count($importRows),
            'creater' => [
                'user_id' => Auth::user()->id,
                'name' => Auth::user()->name
            ],
            'customer' => ['customer_id' => $customer->id, 'name' => $customer->name],
            'import' => [
                'status' => 'completed',
                'rows' => $importRows
            ]
        ];
        
        // Create ItemList with only fillable fields at top level
        return ItemList::create([
            'user_id' => Auth::user()->id,
            'tipus' => 1,
            'status' => 0,
            'json' => $jsonData  // All other data goes into JSON field
        ]);
    }

    private function cleanString(?string $value): string
{
    if ($value === null) {
        return '';
    }
    
    // Remove invalid UTF-8 characters
    $cleaned = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    
    // Optional: Remove non-printable characters
    return preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $cleaned);
}
}