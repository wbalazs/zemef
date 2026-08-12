<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VatReportController extends Controller
{
    /**
     * ÁFA kimutatások megjelenítése
     */
    public function index(Request $request)
    {
        // Alapértelmezett dátumtartomány (aktuális hónap)
        $defaultStart = now()->startOfMonth()->format('Y-m-d');
        $defaultEnd = now()->endOfMonth()->format('Y-m-d');
        
        $startDate = $request->get('start_date', $defaultStart);
        $endDate = $request->get('end_date', $defaultEnd);
        
        // Értékesítések lekérése a temp_sales és temp_sale_items táblákból
        $sales = DB::table('temp_sales')
            ->join('temp_sale_items', 'temp_sales.id', '=', 'temp_sale_items.temp_sale_id')
            ->whereBetween('temp_sales.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            //->where('temp_sales.is_printed', 1) // Csak a nyomtatott/lezárt tételek
            ->select(
                'temp_sales.*',
                'temp_sale_items.id as sale_item_id',
                'temp_sale_items.product_id',
                'temp_sale_items.product_name',
                'temp_sale_items.quantity',
                'temp_sale_items.price_net',
                'temp_sale_items.price_gross',
                'temp_sale_items.vat as vat_rate'
            )
            ->get();
        
        // Tételek feldolgozása
        $globalItems = [];
        $individualItems = [];
        $itemsWithoutPurchase = []; // Nincs beszerzési előzmény
        
        $vatSummary = [
            'global' => ['net' => 0, 'vat' => 0, 'gross' => 0],
            'individual' => ['net' => 0, 'vat' => 0, 'gross' => 0],
            'total' => ['net' => 0, 'vat' => 0, 'gross' => 0, 'vat_to_pay' => 0]
        ];
        
        foreach ($sales as $saleItem) {
            // Beszerzési ár lekérése a stock_entry_items táblából
            $stockItem = $this->findStockEntryItem($saleItem->product_id, $saleItem->created_at);
            
            if (!$stockItem) {
                $itemsWithoutPurchase[] = [
                    'sale_id' => $saleItem->id,
                    'product_name' => $saleItem->product_name,
                    'product_id' => $saleItem->product_id,
                    'sales_price' => $saleItem->price_gross,
                    'sales_date' => $saleItem->created_at,
                    'message' => 'Nincs vásárlási előzmény'
                ];
                continue;
            }
            
            // Értékek kiszámítása
            $purchasePrice = $stockItem->oneprice ?? 0;
            $purchaseWeight = $stockItem->weight ?? 0;
            $salesPriceNet = $saleItem->price_net * $saleItem->quantity;
            $salesPriceGross = $saleItem->price_gross * $saleItem->quantity;
            
            // ÁFA kulcs feldolgozása (pl. "27" vagy "27%")
            $vatRate = (int) filter_var($saleItem->vat_rate, FILTER_SANITIZE_NUMBER_INT);
            $vatAmount = $salesPriceNet * ($vatRate / 100);
            
            // Gramm árak számítása
            $salesPricePerGram = $purchaseWeight > 0 ? $salesPriceGross / $purchaseWeight : 0;
            $purchasePricePerGram = $purchaseWeight > 0 ? $purchasePrice / $purchaseWeight : 0;
            
            // Különbözet
            $priceDifference = $salesPriceGross - $purchasePrice;
            
            // Termék kód lekérése
            $productCode = $this->getProductCode($saleItem->product_id);
            
            $itemData = [
                'product_code' => $productCode,
                'product_name' => $saleItem->product_name,
                'weight' => $purchaseWeight,
                'sales_price' => $salesPriceGross,
                'purchase_price' => $purchasePrice,
                'sales_price_per_gram' => $salesPricePerGram,
                'purchase_price_per_gram' => $purchasePricePerGram,
                'price_difference' => $priceDifference,
                'vat_rate' => $vatRate . '%',
                'vat_amount' => $vatAmount,
                'original_price' => $salesPriceGross,
                'discount_value' => 0, // Itt lehetne számolni a kedvezményeket
                'price_after_discount' => $salesPriceGross,
                'stock_entry_id' => $stockItem->stock_entry_id ?? null,
                'stock_entry_item_id' => $stockItem->stock_entry_item_id ?? null,
                'sale_id' => $saleItem->id,
                'sale_date' => $saleItem->created_at
            ];
            
            // Beszerzési ár alapján besorolás
            if ($purchasePrice <= 50000) {
                $globalItems[] = $itemData;
                $vatSummary['global']['net'] += $salesPriceNet;
                $vatSummary['global']['vat'] += $vatAmount;
                $vatSummary['global']['gross'] += $salesPriceGross;
            } else {
                $individualItems[] = $itemData;
                $vatSummary['individual']['net'] += $salesPriceNet;
                $vatSummary['individual']['vat'] += $vatAmount;
                $vatSummary['individual']['gross'] += $salesPriceGross;
            }
            
            $vatSummary['total']['net'] += $salesPriceNet;
            $vatSummary['total']['vat'] += $vatAmount;
            $vatSummary['total']['gross'] += $salesPriceGross;
        }
        
        // ÁFA fizetendő (csak szemléltetés - a bejövő ÁFA-t is számolni kellene)
        $vatSummary['total']['vat_to_pay'] = $vatSummary['total']['vat'];
        
        return view('vat-reports.index', compact(
            'globalItems',
            'individualItems',
            'itemsWithoutPurchase',
            'vatSummary',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Beszerzési tétel keresése product_id alapján
     */
    private function findStockEntryItem($productId, $saleDate)
    {
        if (!$productId) {
            return null;
        }
        
        return DB::table('stock_entry_items')
            ->where('product_id', $productId)
            ->where('created_at', '<=', $saleDate)
            ->orderBy('created_at', 'desc')
            ->first();
    }
    
    /**
     * Termékkód lekérése
     */
    private function getProductCode($productId)
    {
        if (!$productId) {
            return 'N/A';
        }
        
        $product = DB::table('products')
            ->where('product_id', $productId)
            ->select('code')
            ->first();
            
        return $product ? $product->code : 'N/A';
    }
    
    /**
     * Részletes kimutatás
     */
    public function detailed(Request $request)
    {
        $query = DB::table('temp_sales')
            ->join('temp_sale_items', 'temp_sales.id', '=', 'temp_sale_items.temp_sale_id')
            ->leftJoin('products', 'temp_sale_items.product_id', '=', 'products.product_id')
            ->where('temp_sales.is_printed', 1);
        
        // Szűrők alkalmazása
        if ($request->has('start_date')) {
            $query->whereDate('temp_sales.created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('temp_sales.created_at', '<=', $request->end_date);
        }
        
        if ($request->has('vat_rate') && $request->vat_rate) {
            $vatRate = str_replace('%', '', $request->vat_rate);
            $query->where('temp_sale_items.vat', 'LIKE', "%{$vatRate}%");
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('temp_sale_items.product_name', 'LIKE', "%{$search}%")
                  ->orWhere('products.code', 'LIKE', "%{$search}%")
                  ->orWhere('temp_sales.customer_name', 'LIKE', "%{$search}%");
            });
        }
        
        $items = $query->select(
                'temp_sales.*',
                'temp_sale_items.id as item_id',
                'temp_sale_items.product_name',
                'temp_sale_items.quantity',
                'temp_sale_items.price_net',
                'temp_sale_items.price_gross',
                'temp_sale_items.vat',
                'products.code as product_code'
            )
            ->orderBy('temp_sales.created_at', 'desc')
            ->paginate(50);
        
        // Összesítés
        $summary = DB::table('temp_sales')
            ->join('temp_sale_items', 'temp_sales.id', '=', 'temp_sale_items.temp_sale_id')
            ->where('temp_sales.is_printed', 1)
            ->whereBetween('temp_sales.created_at', [
                $request->get('start_date', now()->startOfMonth()) . ' 00:00:00',
                $request->get('end_date', now()->endOfMonth()) . ' 23:59:59'
            ])
            ->select(
                DB::raw('COUNT(DISTINCT temp_sales.id) as bill_count'),
                DB::raw('SUM(temp_sale_items.price_net * temp_sale_items.quantity) as total_net'),
                DB::raw('SUM(temp_sale_items.price_gross * temp_sale_items.quantity) as total_gross')
            )
            ->first();
        
        // ÁFA összeg számítása
        $summary->total_vat = $summary->total_gross - $summary->total_net;
        
        return view('vat-reports.detailed', compact('items', 'summary'));
    }
    
    /**
     * Export CSV-be
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $sales = DB::table('temp_sales')
            ->join('temp_sale_items', 'temp_sales.id', '=', 'temp_sale_items.temp_sale_id')
            ->leftJoin('products', 'temp_sale_items.product_id', '=', 'products.product_id')
            ->where('temp_sales.is_printed', 1)
            ->whereBetween('temp_sales.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(
                'temp_sales.id as sale_id',
                'temp_sales.created_at as sale_date',
                'temp_sales.customer_name',
                'temp_sale_items.product_name',
                'temp_sale_items.quantity',
                'temp_sale_items.price_net',
                'temp_sale_items.price_gross',
                'temp_sale_items.vat',
                'products.code as product_code'
            )
            ->orderBy('temp_sales.created_at')
            ->get();
        
        $filename = 'afa-kimutatas-' . $startDate . '-tol-' . $endDate . '-ig.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // BOM a UTF-8 helyes kezeléséhez Excelben
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Fejléc
            fputcsv($file, [
                'Dátum',
                'Számla ID',
                'Vásárló',
                'Termékkód',
                'Termék',
                'Mennyiség',
                'Nettó egységár',
                'Nettó összeg',
                'ÁFA kulcs',
                'ÁFA összeg',
                'Bruttó összeg'
            ], ';');
            
            foreach ($sales as $item) {
                $netTotal = $item->price_net * $item->quantity;
                $grossTotal = $item->price_gross * $item->quantity;
                $vatTotal = $grossTotal - $netTotal;
                
                fputcsv($file, [
                    Carbon::parse($item->sale_date)->format('Y.m.d'),
                    $item->sale_id,
                    $item->customer_name,
                    $item->product_code ?? '',
                    $item->product_name,
                    $item->quantity,
                    number_format($item->price_net, 0, ',', ''),
                    number_format($netTotal, 0, ',', ''),
                    $item->vat,
                    number_format($vatTotal, 0, ',', ''),
                    number_format($grossTotal, 0, ',', '')
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}