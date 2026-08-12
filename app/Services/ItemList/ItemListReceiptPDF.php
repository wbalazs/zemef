<?php
// app/Services/ItemList/ItemListReceiptPDF.php

namespace App\Services\ItemList;

use App\Models\ItemList\ItemList;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Customer;

class ItemListReceiptPDF
{
    protected ItemList $itemList;
    
    public function __construct(ItemList $itemList)
    {
        $this->itemList = $itemList->load('items');
    }
    
    public function generate()
    {
        // Adatok kinyerése a JSON-ból
        $jsonData = $this->itemList->json;
        
        // Business adatok
        $businessData = $jsonData['business'] ?? [];
        $businessId = $businessData['business_id'] ?? null;
        $businessName = $businessData['name'] ?? '';
        
        // Store/Warehouse adatok
        $storeData = $jsonData['store'] ?? [];
        $storeId = $storeData['store_id'] ?? null;
        $storeName = $storeData['name'] ?? '';
        
        // Customer adatok
        $customerData = $jsonData['customer'] ?? [];
        $customerId = config('zemef.import_customer_id');
        $customerName = $customerData['name'] ?? '';
        
        // Employee adatok
        $employeeData = $jsonData['employee'] ?? [];
        $employeeId = $employeeData['employee_id'] ?? null;
        $employeeName = $employeeData['name'] ?? '';
        
        // Teljes customer objektum lekérése a címek miatt
        $customer = null;
        if ($customerId) {
            $customer = Customer::with('addresses')->find($customerId);
        }
        
        $data = [
            'itemList' => $this->itemList,
            'items' => $this->itemList->items,
            'jsonData' => $jsonData,
            'business' => [
                'id' => $businessId,
                'name' => $businessName,
                // Itt lehet lekérni a business további adatait, ha van Business modell
            ],
            'store' => [
                'id' => $storeId,
                'name' => $storeName
            ],
            'customer' => $customer,
            'customerData' => $customerData,
            'employeeName' => $employeeName,
            'userName' => $jsonData['creater']['name'] ?? Auth::user()->name ?? '',
            'date' => Carbon::parse($this->itemList->created_at),
            'totalAmount' => $this->itemList->items->sum('price'),
            'totalWeight' => $this->itemList->items->sum('weight'),
            'totalQuantity' => $this->itemList->items->count(),
            'documentNumber' => $this->itemList->id . '/' . date('Y', strtotime($this->itemList->created_at)),
        ];
        
        $pdf = Pdf::loadView('pdf.item-list-receipt', $data);
        $pdf->setPaper('A4');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        return $pdf;
    }
}