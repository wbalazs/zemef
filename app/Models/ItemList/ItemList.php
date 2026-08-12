<?php

namespace App\Models\ItemList;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ItemListItem\ItemListItem;
use App\Models\Inventory;
use App\Models\Document;
use App\Models\Business;
use App\User;
use Illuminate\Support\Facades\Log;

class ItemList extends Model
{
    protected $table = 'item_lists';
    protected $fillable = ['user_id', 'tipus', 'json', 'status', 'stock_entry_id', 'document_id', 'business_id', 'storage_id'];
    protected $casts = ['json' => 'array'];
    protected $appends = ['store', 'amount', 'creater', 'customer', 'import'];

    // Accessors
    public function getStoreAttribute()
    {
        return [
            'store_id' => $this->json['store']['store_id'] ?? null,
            'name' => $this->json['store']['name'] ?? null,
        ];
    }

    public function getAmountAttribute()
    {
        return $this->json['amount'] ?? null;
    }

    public function getCreaterAttribute()
    {
        return [
            'user_id' => $this->json['creater']['user_id'] ?? null,
            'name' => $this->json['creater']['name'] ?? null,
        ];
    }

    public function getCustomerAttribute()
    {
        return [
            'customer_id' => $this->json['customer']['customer_id'] ?? null,
            'name' => $this->json['customer']['name'] ?? null,
        ];
    }

    // NEW: Import accessor
    public function getImportAttribute()
    {
        return [
            'status' => $this->json['import']['status'] ?? null,
            'rows' => $this->json['import']['rows'] ?? [],
        ];
    }

    public function getAllItemListRows()
    {
        try {
            // Get all item lists with their JSON data
            $itemLists = ItemList::where('status',0)->get();
            
            // Initialize an array to hold all rows
            $allRows = [];
            
            // Extract rows from each item list
            foreach ($itemLists as $itemList) {
                // Check if the JSON data has the expected structure
                if (isset($itemList->json['import']['rows']) && is_array($itemList->json['import']['rows'])) {
                    // Add the item list ID to each row for reference
                    $rowsWithSource = array_map(function($row) use ($itemList) {
                        if ($row['status'] == 0){
                            $row['source_item_list_id'] = $itemList->id;
                            $row['warehouse_id'] = 60;
                            $row['warehouse_name'] = 'Zemef Kft Központi raktár';
                            return $row;
                        }
                    }, $itemList->json['import']['rows']);
                    
                    // Merge rows into the main array
                    $allRows = array_merge($allRows, $rowsWithSource);
                }
            }
            
            // Optional: Filter out duplicates based on row1 if needed
            // $uniqueRows = collect($allRows)->unique('row1')->values()->all();
            
            return [
                'success' => true,
                'rows' => $allRows,
                'count' => count($allRows)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error fetching all item list rows: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching rows: ' . $e->getMessage(),
                'rows' => []
            ], 500);
        }
    }

    /**
     * Get all rows that match a field value
     *
     * @param string $fieldName
     * @param string $searchValue
     * @return array
     */
    public function findAllByFieldValue($fieldName, $searchValue)
    {
        if (!isset($this->json['import']['rows']) || !is_array($this->json['import']['rows'])) {
            return [];
        }
        
        $results = [];
        
        foreach ($this->json['import']['rows'] as $row) {
            if (isset($row[$fieldName]) && str_contains($row[$fieldName], $searchValue)) {
                $results[] = $row;
            }
        }
        
        return $results;
    }
    /**
     * Find rows that match a specific field value
     * Supports exact match or partial match
     *
     * @param string $fieldName
     * @param string $searchValue
     * @param bool $exactMatch
     * @return array
     */
    public function findByRowValue($fieldName, $searchValue, $exactMatch = false)
    {
        $results = null;
        
        // Check if the JSON structure exists
        if (!isset($this->json['import']['rows']) || !is_array($this->json['import']['rows'])) {
            return $results;
        }
        
        foreach ($this->json['import']['rows'] as $row) {
            if (isset($row[$fieldName])) {
                $fieldValue = $row[$fieldName];
                
                // Check for match based on exact or partial search
                if ($exactMatch) {
                    if ($fieldValue === $searchValue) {
                        $results = $row;
                    }
                } else {
                    if (str_contains($fieldValue, $searchValue)) {
                        $results = $row;
                    }
                }
            } else {
                
            }
        }
        
        return $results;
    }
    // Mutators
    public function setStoreAttribute(array $value)
    {
        $this->mergeJsonAttribute('store', [
            'store_id' => $value['store_id'],
            'name' => $value['name']
        ]);
    }

    public function setAmountAttribute($value)
    {
        $this->mergeJsonAttribute('amount', $value);
    }

    public function setCreaterAttribute(array $value)
    {
        $this->mergeJsonAttribute('creater', [
            'user_id' => $value['user_id'],
            'name' => $value['name']
        ]);
    }

    public function setCustomerAttribute(array $value)
    {
        $this->mergeJsonAttribute('customer', [
            'customer_id' => $value['customer_id'],
            'name' => $value['name']
        ]);
    }

    // NEW: Import mutator
    public function setImportAttribute(array $value)
    {
        $this->mergeJsonAttribute('import', [
            'status' => $value['status'] ?? null,
            'rows' => $value['rows'] ?? []
        ]);
    }

    // Helper to merge JSON attributes
    protected function mergeJsonAttribute($key, $value)
    {
        $json = $this->json ?? [];
        $json[$key] = $value;
        $this->attributes['json'] = json_encode($json);
    }

    // Custom create method
    public static function createWithJson(array $attributes)
    {
        $model = new static;
        $model->fill($attributes);
        $model->json = $model->prepareJsonData($attributes);
        $model->save();
        return $model;
    }

    // Custom update method
    public function updateWithJson(array $attributes)
    {
        $this->fill($attributes);
        $this->json = $this->prepareJsonData($attributes);
        return $this->save();
    }

    // Prepare JSON data from attributes
    protected function prepareJsonData(array $attributes)
    {
        return [
            'store' => [
                'status' => $attributes['store']['status'] ?? null,
                'store_id' => $attributes['store']['store_id'] ?? null,
                'name' => $attributes['store']['name'] ?? null,
            ],
            'amount' => $attributes['amount'] ?? null,
            'creater' => [
                'user_id' => $attributes['creater']['user_id'] ?? null,
                'name' => $attributes['creater']['name'] ?? null,
            ],
            'customer' => [
                'customer_id' => $attributes['customer']['customer_id'] ?? null,
                'name' => $attributes['customer']['name'] ?? null,
            ],
             // NEW: Import data structure
            'import' => [
                'stock_entry_id' => null,
                'status' => $attributes['import']['status'] ?? null,
                'rows' => $this->prepareImportRows($attributes['import']['rows'] ?? [])
            ]
        ];
    }

    // NEW: Prepare import rows data
    // Add to the existing model
    protected function prepareImportRows(array $rows)
    {
        return array_map(function($row) {
            return [
                'status' => $row['status'] ?? null,
                'import_count' => $row['import_count'] ?? null,
                'row1' => $row['row1'] ?? null,
                'row2' => $row['row2'] ?? null,
                'row3' => $row['row3'] ?? null,
                'row4' => $row['row4'] ?? null,
                'row5' => $row['row5'] ?? null,
                'row6' => $row['row6'] ?? null,
                'row7' => $row['row7'] ?? null,
                'row8' => $row['row8'] ?? null,
                'row9' => $row['row9'] ?? null,
                'row10' => $row['row10'] ?? null,
                'row11' => $row['row11'] ?? null,
                'row12' => $row['row12'] ?? null,
                'row13' => $row['row13'] ?? null,
                'row14' => $row['row14'] ?? null,
                'row15' => $row['row15'] ?? null,
                'row16' => $row['row16'] ?? null,
            ];
        }, $rows);
    }

    // Soft delete (if needed)
    public function softDelete()
    {
        $this->update(['status' => 'deleted']);
        return $this->delete();
    }

    public function incImportCount($row1){
        
        $rows = $this->import['rows'];
        
        foreach ($rows as &$row) {
            if ($row['row1'] === $row1) {
                $row['import_count'] = (($row['import_count'] ?? 0) + 1);
                break; // Exit after finding the first match
            }
        }

        $this->import = [
            'rows' => $rows
        ];

        $this->save();

        return true;
    }
        /**
     * @deprecated Ez a kapcsolat a régi tételtáblát használja (item_list_items). Használja helyette az inventoryItems() kapcsolatot.
     */
    public function item_list_items()
    {
        return $this->hasMany(ItemListItem::class, 'item_list_id', 'id');
    }

    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'source_header_id', 'id')
                    ->where('source_type', 'item_list');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'resource_id', 'id')
            ->where('document_module', 'item_list_doc')
            ->orderBy('created_at', 'DESC');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }
}
