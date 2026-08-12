<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessMarking;
use Illuminate\Http\Request;

class BusinessMarkingController extends Controller
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'business_marking_id' => ['nullable', 'integer', 'exists:business_markings,id'],
            'business_id' => ['required', 'integer', 'exists:businesses,business_id'],
            'name' => ['required', 'string', 'max:255'],
            'product_status_id' => ['required', 'integer', 'exists:master_product_statuses,product_status_id'],
            'status' => ['nullable', 'boolean'],
            'default' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['business_marking_id'])) {
            $businessMarking = BusinessMarking::findOrFail($validated['business_marking_id']);
        } else {
            $businessMarking = new BusinessMarking();
        }

        $isDefault = $request->boolean('default');

        if ($isDefault) {
            BusinessMarking::where('business_id', $validated['business_id'])
                ->when($businessMarking->exists, function ($query) use ($businessMarking) {
                    $query->where('id', '!=', $businessMarking->id);
                })
                ->update(['default' => 0]);
        }

        $businessMarking->business_id = $validated['business_id'];
        $businessMarking->name = $validated['name'];
        $businessMarking->product_status_id = $validated['product_status_id'];
        $businessMarking->status = $request->boolean('status') ? 1 : 0;
        $businessMarking->default = $isDefault ? 1 : 0;
        $businessMarking->save();

        return response()->json([
            'success' => true,
            'id' => $businessMarking->id,
        ]);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:business_markings,id'],
        ]);

        BusinessMarking::where('id', $validated['id'])->delete();

        return response()->json(['success' => true]);
    }

    public function load(Request $request)
    {
        $validated = $request->validate([
            'business_id' => ['required', 'integer', 'exists:businesses,business_id'],
        ]);

        $business = Business::with(['business_markings.productStatus.vatType'])
            ->where('business_id', $validated['business_id'])
            ->firstOrFail();

        $html = '';

        foreach ($business->business_markings as $marking) {
            $statusName = optional($marking->productStatus)->name;
            $price = optional($marking->productStatus)->price;
            $vatName = optional(optional($marking->productStatus)->vatType)->name;

            $html .= "<tr class='grey_background business_marking_row_{$marking->id}' "
                . "data-id='" . e($marking->id) . "' "
                . "data-name='" . e($marking->name) . "' "
                . "data-product_status_id='" . e($marking->product_status_id) . "' "
                . "data-default='" . ($marking->default ? '1' : '0') . "' "
                . "data-status='" . ($marking->status ? '1' : '0') . "'"
                . ">"
                . "<td class='business_marking_name'>" . e($marking->name) . "</td>"
                . "<td class='business_marking_product_status'>" . e($statusName) . "</td>"
                . "<td class='business_marking_price'>" . e($price) . "</td>"
                . "<td class='business_marking_vat'>" . e($vatName) . "</td>"
                . "<td class='business_marking_default'>" . ($marking->default ? 'Igen' : 'Nem') . "</td>"
                . "<td class='business_marking_status'>" . ($marking->status ? 'Igen' : 'Nem') . "</td>"
                . "<td>"
                . "<div style='display: inline-block;' class='doc_wrapper'>"
                . "<button type='button' class='btn btn-success edit_business_marking' data-id='" . e($marking->id) . "'><i class='fa fa-edit'></i></button>"
                . "</div>"
                . "<div style='display: inline-block;' class='doc_wrapper'>"
                . "<button type='button' class='btn btn-danger delete_business_marking' data-id='" . e($marking->id) . "'><i class='fa fa-trash'></i></button>"
                . "</div>"
                . "</td>"
                . "</tr>";
        }

        return $html;
    }
}
