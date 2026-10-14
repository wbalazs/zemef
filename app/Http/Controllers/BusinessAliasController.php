<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessAlias;
use Illuminate\Http\Request;

class BusinessAliasController extends Controller
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'business_alias_id' => ['nullable', 'integer', 'exists:business_aliases,id'],
            'business_id' => ['required', 'integer', 'exists:businesses,business_id'],
            'name' => ['required', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,warehouse_id'],
            'aktiv' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['business_alias_id'])) {
            $businessAlias = BusinessAlias::findOrFail($validated['business_alias_id']);
        } else {
            $businessAlias = new BusinessAlias();
        }

        $businessAlias->business_id = $validated['business_id'];
        $businessAlias->name = $validated['name'];
        $businessAlias->warehouse_id = $validated['warehouse_id'] ?? null;
        $businessAlias->aktiv = $request->boolean('aktiv') ? 1 : 0;
        $businessAlias->save();

        return response()->json([
            'success' => true,
            'id' => $businessAlias->id,
        ]);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:business_aliases,id'],
        ]);

        BusinessAlias::where('id', $validated['id'])->delete();

        return response()->json(['success' => true]);
    }

    public function load(Request $request)
    {
        $validated = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,business_id'],
        ]);

        $business = Business::with(['business_aliases.warehouse'])
            ->where('business_id', $validated['business_id'])
            ->firstOrFail();

        $html = '';

        foreach ($business->business_aliases as $alias) {
            $warehouseName = optional($alias->warehouse)->name;

            $html .= "<tr class='grey_background business_alias_row_{$alias->id}' "
                . "data-id='" . e($alias->id) . "' "
                . "data-name='" . e($alias->name) . "' "
                . "data-warehouse_id='" . e($alias->warehouse_id) . "' "
                . "data-aktiv='" . ($alias->aktiv ? '1' : '0') . "'"
                . ">"
                . "<td class='business_alias_name'>" . e($alias->name) . "</td>"
                . "<td class='business_alias_warehouse'>" . e($warehouseName) . "</td>"
                . "<td class='business_alias_aktiv'>" . ($alias->aktiv ? 'Igen' : 'Nem') . "</td>"
                . "<td>"
                . "<div style='display: inline-block;' class='doc_wrapper'>"
                . "<button type='button' class='btn btn-success edit_business_alias' data-id='" . e($alias->id) . "'><i class='fa fa-edit'></i></button>"
                . "</div>"
                . "<div style='display: inline-block;' class='doc_wrapper'>"
                . "<button type='button' class='btn btn-danger delete_business_alias' data-id='" . e($alias->id) . "'><i class='fa fa-trash'></i></button>"
                . "</div>"
                . "</td>"
                . "</tr>";
        }

        return $html;
    }
}
