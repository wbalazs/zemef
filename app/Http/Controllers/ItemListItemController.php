<?php
// app/Http/Controllers/ItemListItemController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemListItem\ItemListItem;
use App\Models\ItemListItem\DataTransferObjects\ItemListItemData;
use App\Services\ItemList\CreateItemListItem;
use App\Models\ItemList\ItemList;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\Warehouse;
use App\Models\Business;
use App\Models\Inventory;

class ItemListItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ItemListItem::query();
        
        // Filter by item_list_id if provided
        if ($request->has('item_list_id')) {
            $query->where('item_list_id', $request->item_list_id);
        }
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $items = $query->with(['itemList', 'product', 'mainProduct'])->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_list_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'is_unique' => 'sometimes|boolean|string',
                'description' => 'nullable|string',
                'factory_number' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'quantity' => 'nullable|integer|min:1',
                'oneprice' => 'nullable|numeric|min:0',
                'price' => 'nullable|numeric|min:0',
                'storage_id' => 'required|integer|exists:warehouses,warehouse_id',
                'business_id' => 'required|integer',
            ]);

            $itemList = ItemList::findOrFail($validated['item_list_id']);
            $itemList->incImportCount($request->get('factory_number'));

            $warehouse = Warehouse::find($validated['storage_id']);
            if (!$warehouse) {
                $warehouse = Business::find(auth()->user()->employee?->active_business_id)->warehouses->first();
            }
            if (!$warehouse) {
                return response()->json(['success' => false, 'message' => 'Warehouse not found'], 404);
            }

            // Prepare data for service
            $data = [
                'item_list_id' => (int)$validated['item_list_id'],
                'name' => $validated['name'],
                'description' => $validated['description'] ?? $validated['name'],
                'quantity' => isset($validated['quantity']) ? (int)$validated['quantity'] : 1,
                'oneprice' => isset($validated['oneprice']) ? (int)$validated['oneprice'] : null,
                'price' => isset($validated['price']) ? (int)$validated['price'] : null,
                'factory_number' => $validated['factory_number'] ?? null,
                'weight' => isset($validated['weight']) ? (float)$validated['weight'] : null,
                'storage_id' => $warehouse->warehouse_id,
                'is_unique' => filter_var($validated['is_unique'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];

            // Call the refactored service
            $result = CreateItemListItem::From($data);

            if ($result) {
                // Retrieve newly created inventory items
                $createdItems = Inventory::where('source_type', 'item_list')
                    ->where('source_header_id', $validated['item_list_id'])
                    ->where('name', $validated['name'])
                    ->latest()
                    ->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Item list item created successfully',
                    'data' => $createdItems
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create item list item'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $item = ItemListItem::with(['itemList', 'product', 'mainProduct', 'investigationUser'])
            ->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item list item not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $item = ItemListItem::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item list item not found'
                ], 404);
            }

            // Validate request
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'nullable|integer|min:1',
                'oneprice' => 'nullable|integer|min:0',
                'price' => 'nullable|integer|min:0',
                'factory_number' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'investigation' => 'nullable|boolean',
                'investigated_at' => 'nullable|date',
                'investigation_user_id' => 'nullable|integer|exists:users,id',
                'hallmark' => 'nullable|boolean',
                'repair' => 'nullable|boolean',
                'is_fake' => 'nullable|boolean',
                'comment' => 'nullable|string',
                'inv_comment' => 'nullable|string',
                'status' => 'nullable|boolean'
            ]);

            $validated['updated_at'] = Carbon::now();

            // Update the item
            $item->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item list item updated successfully',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $item = ItemListItem::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item list item not found'
                ], 404);
            }

            // Soft delete or hard delete? Using hard delete for now
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item list item deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get items by item list ID
     */
    public function getByItemList(int $itemListId): JsonResponse
    {
        $items = ItemListItem::where('item_list_id', $itemListId)
            ->with(['product', 'mainProduct'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Update investigation data
     */
    public function updateInvestigation(Request $request, int $id): JsonResponse
    {
        try {
            $item = ItemListItem::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item list item not found'
                ], 404);
            }

            $validated = $request->validate([
                'investigation' => 'sometimes|boolean',
                'investigated_at' => 'sometimes|date',
                'investigation_user_id' => 'sometimes|integer|exists:users,id',
                'hallmark' => 'sometimes|boolean',
                'repair' => 'sometimes|boolean',
                'is_fake' => 'sometimes|boolean',
                'inv_comment' => 'nullable|string'
            ]);

            $validated['updated_at'] = Carbon::now();
            
            // If investigation is being set to true and no investigated_at, set it now
            if (isset($validated['investigation']) && $validated['investigation'] && !$item->investigated_at) {
                $validated['investigated_at'] = Carbon::now();
            }

            $item->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Investigation data updated successfully',
                'data' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}