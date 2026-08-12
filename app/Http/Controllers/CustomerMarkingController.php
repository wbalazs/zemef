<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerMarking;
use Illuminate\Http\Request;

class CustomerMarkingController extends Controller
{
    public function save(Request $request)
    {
        $validated = $request->validate([
            'customer_marking_id' => ['nullable', 'integer', 'exists:customer_markings,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,customer_id'],
            'name' => ['required', 'string', 'max:255'],
            'product_status_id' => ['required', 'integer', 'exists:master_product_statuses,product_status_id'],
            'status' => ['nullable', 'boolean'],
            'default' => ['nullable', 'boolean'],
        ]);

        if (!empty($validated['customer_marking_id'])) {
            $customerMarking = CustomerMarking::findOrFail($validated['customer_marking_id']);
        } else {
            $customerMarking = new CustomerMarking();
        }

        $isDefault = $request->boolean('default');

        if ($isDefault) {
            CustomerMarking::where('customer_id', $validated['customer_id'])
                ->when($customerMarking->exists, function ($query) use ($customerMarking) {
                    $query->where('id', '!=', $customerMarking->id);
                })
                ->update(['default' => 0]);
        }

        $customerMarking->customer_id = $validated['customer_id'];
        $customerMarking->name = $validated['name'];
        $customerMarking->product_status_id = $validated['product_status_id'];
        $customerMarking->status = $request->boolean('status') ? 1 : 0;
        $customerMarking->default = $isDefault ? 1 : 0;
        $customerMarking->save();

        return response()->json([
            'success' => true,
            'id' => $customerMarking->id,
        ]);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:customer_markings,id'],
        ]);

        CustomerMarking::where('id', $validated['id'])->delete();

        return response()->json(['success' => true]);
    }

    public function load(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,customer_id'],
        ]);

        $customer = Customer::with(['customer_markings.productStatus.vatType'])
            ->where('customer_id', $validated['customer_id'])
            ->firstOrFail();

        $html = '';

        foreach ($customer->customer_markings as $marking) {
            $statusName = optional($marking->productStatus)->name;
            $price = optional($marking->productStatus)->price;
            $vatName = optional(optional($marking->productStatus)->vatType)->name;

            $html .= "<tr class='grey_background customer_marking_row_{$marking->id}' "
                . "data-id='" . e($marking->id) . "' "
                . "data-name='" . e($marking->name) . "' "
                . "data-product_status_id='" . e($marking->product_status_id) . "' "
                . "data-default='" . ($marking->default ? '1' : '0') . "' "
                . "data-status='" . ($marking->status ? '1' : '0') . "'"
                . ">"
                . "<td class='customer_marking_name'>" . e($marking->name) . "</td>"
                . "<td class='customer_marking_product_status'>" . e($statusName) . "</td>"
                . "<td class='customer_marking_price'>" . e($price) . "</td>"
                . "<td class='customer_marking_vat'>" . e($vatName) . "</td>"
                . "<td class='customer_marking_default'>" . ($marking->default ? 'Igen' : 'Nem') . "</td>"
                . "<td class='customer_marking_status'>" . ($marking->status ? 'Igen' : 'Nem') . "</td>"
                . "<td>"
                . "<div style='display: inline-block;' class='doc_wrapper'>"
                . "<button type='button' class='btn btn-success edit_customer_marking' data-id='" . e($marking->id) . "'><i class='fa fa-edit'></i></button>"
                . "</div>"
                . "<div style='display: inline-block;' class='doc_wrapper'>"
                . "<button type='button' class='btn btn-danger delete_customer_marking' data-id='" . e($marking->id) . "'><i class='fa fa-trash'></i></button>"
                . "</div>"
                . "</td>"
                . "</tr>";
        }

        return $html;
    }
}
