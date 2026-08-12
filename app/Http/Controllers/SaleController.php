<?php
namespace App\Http\Controllers;

use App\Enums\InventoryStatus;
use App\Models\Reservation;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Models\Employee;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\Package\DiscountPrice;
use App\Models\Package\Package;
use App\Models\Package\PackageItem;
use App\Models\Package\CashOnDelivery;
use App\Models\Package\ShippingService;
use App\Models\TempSale;
use App\Models\Inventory;
use App\Models\ProductType;
use App\Models\ItemList\ItemList;
use Carbon\Carbon;
use App\Http\Controllers\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use App\Services\Webshop\SetWebshopQuantity;
use App\Services\Print\Sam4sPrinterService;
use App\Services\CashRegister\CreateAutoCashTransaction;
use App\Services\CashRegister\DataTransferObjects\AutoCashTransactionData;
use DB;



class SaleController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Sale::class);
        $this->stockController = new StockController();
    }
    
    public function index() {
        //$actual_stock = $this->stockController->get_actual_stock();
        $items = new ItemList();
        $itemlist = $items->getAllItemListRows();

        return view('sale.panel', 
            [
                'customers' => Customer::all(),
                'warehouses' => Warehouse::all(),
                'businesses' => Business::all(),
                'itemlist' => $itemlist['rows'],
                'shippingServices' => ShippingService::active()->get(),
                'stock' => null,
            ]);
    }

    public function createReservation(Request $request) {
        $reservation = new Reservation();
        $reservation->fill($request->all());
        $reservation->save();
        return response()->json(['success' => true, 'message' => 'Reservation created successfully']);
    }

    //Raktárközi bizonylat fejléc elkészítése
    public function create_warehouse_transfer($warehouse_from,$warehouse_to,$employee_id,$business_id)
    {
        $serial = $this->getNextSerialWarehouse_transfer();
        $transfer_number = 'RKOZ/' . $business_id . '/' . date('Y') . '/' . sprintf('%05d', $serial);
        $business = Business::find($business_id);
        $employee = Employee::find($employee_id);
        $warehouse_from_name = Warehouse::find($warehouse_from)->name;
        $warehouse_to_name = Warehouse::find($warehouse_to)->name;

        $warehouseTransfer = new WarehouseTransfer();
        $warehouseTransfer->warehouse_transfer_number = $transfer_number;
        $warehouseTransfer->employee_id = $employee_id;
        $warehouseTransfer->employee_name = $employee->lastname.' '.$employee->firstname ?? null;
        $warehouseTransfer->business_id = $business_id;
        $warehouseTransfer->business_name = $business->name;
        $warehouseTransfer->business_address = $business->zip.' '.$business->city.', '.$business->address.' '.$business->floor.' '.$business->door ?? null;
        $warehouseTransfer->warehouse_from = $warehouse_from;
        $warehouseTransfer->warehouse_from_name = $warehouse_from_name;
        $warehouseTransfer->warehouse_to = $warehouse_to;
        $warehouseTransfer->warehouse_to_name = $warehouse_to_name;
        $warehouseTransfer->save();

        return $warehouseTransfer->warehouse_transfer_id;
    }

    //Raktárközi bizonylat tétel hozzáadása
    public function create_warehouse_transfer_item($warehouse_transfer_id,$explode = false,$employee_id,$product_id,$quantity,$warehouse_from_id,$warehouse_to_id,$note = null)
    {
        $product = Product::find($product_id);
        $transferItem = new WarehouseTransferItem();
        $warehouse_to = Warehouse::find($warehouse_to_id);
        $transferItem->warehouse_transfer_id = $warehouse_transfer_id;
        $transferItem->transfer_items_product_id = $product_id;
        $transferItem->transfer_items_main_product_id = $product->main_product_id;
        $transferItem->transfer_items_product_quantity = 1;
        $transferItem->transfer_items_product_name = $product->name;
        $transferItem->transfer_items_product_description = $product->description;
        $transferItem->transfer_items_product_factory_number = $product->factory_number;
        $transferItem->transfer_items_product_weight = $product->weight;
        $transferItem->transfer_items_product_price = $product->price;
        $transferItem->transfer_items_warehouse_from = $warehouse_from_id;
        $transferItem->transfer_items_warehouse_to = $warehouse_to_id;
        $transferItem->transfer_items_note = $note;

        $transferItem->save();

        return $transferItem->transfer_items_id;
    }
    //eladás
    public function createTempSale(Request $request)
    {
        $data = $request->all();
        $salePackagePayload = (($data['receipt_type'] ?? '') === 'invoice') ? $this->resolveSalePackagePayloadFromRequest($request) : null;
            
        $products = $data['products'] ?? [];

        if (empty($products)) {
            return response()->json(['success' => false, 'message' => 'Nincs termék kiválasztva!']);
        }

        $inventoryIds = collect($products)->pluck('inventory_id')->filter()->map(fn($id) => (int) $id)->unique()->values();
        $inventoryMap = Inventory::with('product')->whereIn('inventory_id', $inventoryIds)->get()->keyBy('inventory_id');

        //total values count
        $totalNet = 0;
        $totalGross = 0;
        foreach ($products as $p) {
            $inventoryId = (int) ($p['inventory_id'] ?? 0);
            $quantity = max(1, (int) ($p['quantity'] ?? 1));
            $inventory = $inventoryMap->get($inventoryId);

            if (!$inventory) {
                return response()->json(['success' => false, 'message' => 'Készlet tétel nem található!']);
            }

            if ((int) $inventory->status !== InventoryStatus::IN_STOCK->value || (int) $inventory->quantity < $quantity) {
                return response()->json(['success' => false, 'message' => 'A kiválasztott tételből nincs elegendő készlet!']);
            }

            $net = floatval($p['price_net'] ?? $inventory->oneprice ?? 0);
            $vat = 27;
            $gross = round($net * (1 + $vat / 100));

            $totalNet += ($net * $quantity);
            $totalGross += ($gross * $quantity);
        }

        $shippingFeeNet = 0;
        $shippingFeeGross = 0;
        if (($data['receipt_type'] ?? '') === 'invoice' && $salePackagePayload) {
            $shippingFeeNet = (float) ($salePackagePayload['shipping_price'] ?? 0);
            $shippingFeeGross = $shippingFeeNet;
        }

        $discountBreakdown = $this->buildDiscountBreakdown($products, $inventoryMap, $request);
        $discountPercent = (float) $discountBreakdown['discount_percent'];
        $discountNet = (int) $discountBreakdown['discount_net'];
        $discountGross = (int) $discountBreakdown['discount_gross'];
        $couponNet = (int) $discountBreakdown['coupon_net'];
        $couponGross = (int) $discountBreakdown['coupon_gross'];
        $giftcard = (int) $discountBreakdown['giftcard_net'];
        $loyalty = (int) $discountBreakdown['loyalty_net'];

        $data['total_net'] = $totalNet + $shippingFeeNet;
        $data['total_gross'] = $totalGross + $shippingFeeGross;
        $data['final_total_net'] = $data['total_net'] - $discountNet - $couponNet - $giftcard - $loyalty;
        $data['final_total_gross'] = $data['total_gross'] - $discountGross - $couponGross - $giftcard - $loyalty;

        $paymentSelection = $this->resolvePaymentSelection($data, (float) $data['final_total_gross']);
        if (!$paymentSelection['valid']) {
            return response()->json([
                'success' => false,
                'message' => $paymentSelection['message'] ?? 'Érvénytelen fizetési adatok.'
            ]);
        }
        $data['payment_method'] = $paymentSelection['primary_method'];
        $data['payment_methods'] = $paymentSelection['payment_methods'];
        $data['split_payments'] = $paymentSelection['split_payments'];

        $customer = Customer::with('addresses')->find($data['customer_id']);
        if ($customer) {
            $customerAddress = $customer->addresses->where('address_type', 'Lakcím')->first() ?: $customer->addresses->first();

            $resolvedName = trim((string) ($customer->name ?? ''));
            if ($resolvedName === '') {
                $resolvedName = trim(((string) ($customer->lastname ?? '')) . ' ' . ((string) ($customer->firstname ?? '')));
            }

            $resolvedZip = $customerAddress->zip ?? ($data['customer_zip'] ?? '');
            $resolvedCity = $customerAddress->city ?? ($data['customer_city'] ?? '');
            $resolvedAddress = $customerAddress->address ?? ($data['customer_address'] ?? '');

            $data['customer_name'] = $resolvedName ?: ($data['customer_name'] ?? '');
            $data['customer_address'] = trim($resolvedZip . ' ' . $resolvedCity . ', ' . $resolvedAddress, ' ,');
            $data['customer_email'] = $customer->email ?? ($data['customer_email'] ?? '');
            $data['customer_phone'] = $customer->phone ?? ($data['customer_phone'] ?? '');
            $data['customer_city'] = $resolvedCity;
            $data['customer_zip'] = $resolvedZip;
            $data['customer_tax_number'] = $customer->tax_number ?? ($data['customer_tax_number'] ?? '');
        }

        $saleData = [
            'customer_id' => $data['customer_id'],
            'customer_name' => $data['customer_name'] ?? '',
            'customer_address' => $data['customer_address'] ?? '',
            'sale_type' => $data['receipt_type'],
            'total_net' => $data['total_net'] ?? 0,
            'total_gross' => $data['total_gross'] ?? 0,
            'discount' => $discountPercent ?? 0,
            'coupon' => $data['coupon'] ?? 0,
            'giftcard' => $data['giftcard'] ?? 0,
            'loyalty' => $data['loyalty'] ?? 0,
            'final_total_net' => $data['final_total_net'] ?? 0,
            'final_total_gross' => $data['final_total_gross'] ?? 0,
            'payment_method' => $data['payment_method'],
            'employee_id' => Auth::user()->employee?->employee_id,
            'employee_name' => auth()->user()->employee?->lastname . ' ' . auth()->user()->employee?->firstname,
            'company_id' => auth()->user()->employee?->active_company_id,
            'business_id' => auth()->user()->employee?->active_business_id,
        ];

        if (!empty($discountBreakdown['customer_card_id'])) {
            $saleData['customer_card_id'] = (int) $discountBreakdown['customer_card_id'];
        }
        if (!empty($discountBreakdown['coupon_id'])) {
            $saleData['discount_price_id'] = (int) $discountBreakdown['coupon_id'];
        }

        // Csak ha foglaló típusú a bizonylat
        if (($data['receipt_type'] ?? '') === 'booking') {
            $saleData['booking_max_date'] = Carbon::now()->addMonths((int)($data['booking_months'] ?? 1))->format('Y-m-d');
            $saleData['booking_price'] = $data['booking_amount'] ?? 0;
        }

        $sale = \App\Models\TempSale::create($saleData);

        if (!empty($discountBreakdown['coupon_id'])) {
            DiscountPrice::where('id', (int) $discountBreakdown['coupon_id'])->increment('used_count');
        }

        foreach ($products as $p) {
            $inventoryId = (int) ($p['inventory_id'] ?? 0);
            $quantity = max(1, (int) ($p['quantity'] ?? 1));
            $inventory = $inventoryMap->get($inventoryId);
            if (!$inventory) {
                continue;
            }

            // inventory készlet levonás
            $inventory->quantity = max(0, (int) $inventory->quantity - $quantity);
            if ((int) $inventory->quantity === 0) {
                $inventory->status = InventoryStatus::SOLD_OUT->value;
            }
            $inventory->save();

            // webshop mennyiség frissítése, ha van kapcsolt termék
            if ($inventory->product && $inventory->product->ws_product_id) {
                SetWebshopQuantity::From([
                    'data' => [
                        'quantity' => (int) $inventory->quantity,
                        'product_id' => $inventory->product->ws_product_id,
                    ]
                ]);
            }

            $itemName = $inventory->name ?: optional($inventory->product)->name ?: ('Tétel #' . $inventory->inventory_id);
            $itemDescription = $inventory->description ?: optional($inventory->product)->short_description;
            $priceNet = floatval($p['price_net'] ?? $inventory->oneprice ?? 0);

            \App\Models\TempSaleItem::create([
                'temp_sale_id' => $sale->id,
                'product_id' => $inventory->product_id,
                'product_name' => $itemName,
                'description' => $itemDescription,
                'image_path' => $p['image_path'] ?? '',
                'quantity' => $quantity,
                'price_net' => $priceNet * (1 - $discountPercent / 100),
                'price_gross' => round($priceNet * 1.27 * (1 - $discountPercent / 100), 2),
                'vat' => '27%',
                'warehouse_id' => $inventory->warehouse_to ?? $inventory->storage_id ?? null,
            ]);
        }

        if ((float) ($paymentSelection['cash_amount'] ?? 0) > 0) {
            try {
                $cashTransactionData = new AutoCashTransactionData(
                    type: 'income',
                    amount: (float) ($paymentSelection['cash_amount'] ?? 0),
                    payer_payee: (string) ($data['customer_name'] ?? ('Vasarlo #' . ($data['customer_id'] ?? 'ismeretlen'))),
                    reason: 'sales',
                    description: 'Automatikus penztar bevetel - TempSale #' . $sale->id
                );

                CreateAutoCashTransaction::from($cashTransactionData)->handle();
            } catch (\Throwable $e) {
                \Log::error('Auto cash income failed for sale', [
                    'temp_sale_id' => $sale->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } 

        // Billingo integráció
        $pdfUrl = $serialPortText = null;
        if ($request->receipt_type === 'invoice') {
            try {
                $billingoService = new \App\Services\Billingo\BillingoService();
                
                // Ügyfél adatok összeállítása
                $customerData = [
                    'name' => !empty($data['customer_name']) ? $data['customer_name'] : 
                            (!empty($saleData['customer_name']) ? $saleData['customer_name'] : 
                            'Vásárló ' . date('md-His')),
                    'email' => $data['customer_email'] ?? '',
                    'phone' => $data['customer_phone'] ?? '',
                    'address' => $data['customer_address'] ?? ($saleData['customer_address'] ?? ''),
                    'city' => $data['customer_city'] ?? 'Budapest',
                    'zip_code' => $data['customer_zip'] ?? '1000',
                    'tax_number' => $data['customer_tax_number'] ?? '',
                ];
                
                // Termékek előkészítése
                $billingoItems = [];
                
                foreach ($products as $p) {
                    $inventoryId = (int) ($p['inventory_id'] ?? 0);
                    $inventory = $inventoryMap->get($inventoryId);
                    if (!$inventory) {
                        continue;
                    }

                    $billingoItems[] = [
                        'product_name' => $inventory->name ?: optional($inventory->product)->name ?: ('Tétel #' . $inventory->inventory_id),
                        'description' => $inventory->description ?: optional($inventory->product)->short_description ?? '',
                        'price_net' => floatval($p['price_net'] ?? $inventory->oneprice ?? 0),
                        'vat' => '27%',
                        'quantity' => max(1, (int) ($p['quantity'] ?? 1)),
                        'unit' => 'db',
                    ];
                }
                
                if (empty($billingoItems)) {
                    throw new \Exception('Nincs termék a számlához');
                }

                if ($salePackagePayload && (float) ($salePackagePayload['shipping_price'] ?? 0) > 0) {
                    $billingoItems[] = [
                        'product_name' => 'Szállítási díj - ' . ($salePackagePayload['shipping_service_name'] ?? 'Szállító'),
                        'description' => 'Csomag feladás díja',
                        'price_net' => (float) $salePackagePayload['shipping_price'],
                        'vat' => '0%',
                        'entitlement' => 'AAM',
                        'quantity' => 1,
                        'unit' => 'db',
                    ];
                }
                if ($discountGross) {
                    $billingoItems[] = [
                        'product_name' => 'Kedvezmény',
                        'description' => '',
                        'price_net' => floatval((-1 * $discountGross)),
                        'vat' => '0%',
                        'entitlement' => 'AAM',
                        'quantity' => 1,
                        'unit' => 'db',
                    ];
                }
                if ($couponGross) {
                    $billingoItems[] = [
                        'product_name' => 'Kupon',
                        'description' => '',
                        'price_net' => floatval((-1 * $couponGross)),
                        'vat' => '0%',
                        'entitlement' => 'AAM',
                        'quantity' => 1,
                        'unit' => 'db',
                    ];
                }
                if ($giftcard) {
                    $billingoItems[] = [
                        'product_name' => 'Ajándékkártya',
                        'description' => '',
                        'price_net' => floatval((-1*$giftcard)),
                        'vat' => '0%',
                        'entitlement' => 'AAM',
                        'quantity' => 1,
                        'unit' => 'db',
                    ];
                }
                if ($loyalty) {
                    $billingoItems[] = [
                        'product_name' => 'Hűség kedvezmény',
                        'description' => '',
                        'price_net' => floatval((-1*$loyalty)),
                        'vat' => '0%',
                        'entitlement' => 'AAM',
                        'quantity' => 1,
                        'unit' => 'db',
                    ];
                }
                
                // Számla adatok
                $billingoSaleData = [
                    'discount' => 0,
                    'coupon' => 0,
                    'giftcard' => 0,
                    'loyalty' => 0,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'comment' => 'Vásárlás - ' . date('Y.m.d H:i') . ($paymentSelection['billingo_comment'] ? (' | ' . $paymentSelection['billingo_comment']) : ''),
                    'discount_vat' => 27,
                ];
                
                // Számla létrehozása - Partner adatokkal automatikusan
                $billingoInvoice = $billingoService->createInvoice($billingoSaleData, $billingoItems, $customerData);
                
                if ($billingoInvoice) {
                    $sale->billingo_invoice_id = $billingoInvoice['id'];
                    $sale->billingo_invoice_number = $billingoInvoice['invoice_number'];
                    $sale->billingo_net_total = $billingoInvoice['net_total'];
                    $sale->billingo_gross_total = $billingoInvoice['gross_total'];
                    $sale->billingo_fulfillment_date = $billingoInvoice['fulfillment_date'];
                    $sale->billingo_due_date = $billingoInvoice['due_date'];
                    
                    // Partner ID most opcionális (mert lehet, hogy nem kaptunk)
                    if (isset($billingoInvoice['partner_id'])) {
                        $sale->billingo_partner_id = $billingoInvoice['partner_id'];
                    }
                    
                    $sale->save();
                    
                    // Download and save PDF locally
                    try {
                        $receiptType = $data['receipt_type'] ?? 'invoice';
                        $pdfUrl = $billingoService->downloadAndSavePdf(
                            $billingoInvoice['id'],
                            $sale->id,
                            $receiptType
                        );
                        
                        \Log::info('✅ Billingo PDF downloaded and saved', [
                            'invoice_number' => $billingoInvoice['invoice_number'],
                            'local_pdf_url' => $pdfUrl
                        ]);
                        
                    } catch (\Exception $pdfException) {
                        \Log::error('Failed to download Billingo PDF, generating local: ' . $pdfException->getMessage());
                        
                        // Fallback to local PDF generation
                        $pdfUrl = $this->generateSalePdf($sale->id, $data['receipt_type']);
                    }
                    
                    \Log::info('✅ BILLINGO SUCCESS WITH PARTNER DATA', [
                        'invoice_number' => $billingoInvoice['invoice_number'],
                        'method' => isset($billingoInvoice['partner_id']) ? 'partner_id' : 'partner_object'
                    ]);
                    
                } else {
                    throw new \Exception('Billingo invoice creation failed');
                }
                
            } catch (\Exception $e) {
                \Log::error('Billingo failed: ' . $e->getMessage());
                
                $pdfUrl = $this->generateSalePdf($sale->id, $data['receipt_type']);
                
                $sale->billingo_invoice_id = null;
                $sale->billingo_invoice_number = 'LOCAL-' . $sale->id;
                $sale->save();
            }
        }

        if (($data['receipt_type'] ?? '') === 'invoice' && $salePackagePayload) {
            $this->createPackageFromPayloadAndProducts($salePackagePayload, $products);
        }

        if (($data['receipt_type'] ?? '') === 'booking') {
            // Foglaló esetén csak PDF generálás
            $pdfUrl = $this->generateSalePdf($sale->id, $data['receipt_type']);
        }

        // Print receipt immediately if requested
        if ($request->receipt_type === 'receipt') {
            try {
                 $serialPortText = $this->generateReceiptText($sale, $products, $data);
            } catch (\Exception $e) {
                \Log::error('Failed to generate receipt text: ' . $e->getMessage());
                // Don't fail the sale if printing fails
            }
        }
        
        return response()->json([
            'success' => true, 
            'serial_port_text' => $serialPortText,
            'receipt_type' => $data['receipt_type'] ?? '',
            'sale_id' => $sale->id,
            'pdf_url' => $pdfUrl,
            'billingo_invoice_number' => $sale->billingo_invoice_number ?? null,
            'billingo_success' => !empty($sale->billingo_invoice_id),
        ]);
        // PDF generálás hívása
        //     $pdfUrl = $this->generateSalePdf($sale->id, $data['receipt_type']);

        //   return response()->json(['success' => true, 'pdf_url' => $pdfUrl]);
    }

    private function resolvePaymentSelection(array $data, float $finalTotalGross): array
    {
        $allowedMethods = ['cash', 'card', 'transfer'];

        $paymentMethods = [];
        if (isset($data['payment_methods']) && is_array($data['payment_methods'])) {
            $paymentMethods = array_values(array_unique(array_filter(array_map(function ($method) use ($allowedMethods) {
                $normalized = strtolower((string) $method);
                return in_array($normalized, $allowedMethods, true) ? $normalized : null;
            }, $data['payment_methods']))));
        }

        if (empty($paymentMethods) && !empty($data['payment_method'])) {
            $fallback = strtolower((string) $data['payment_method']);
            if (in_array($fallback, $allowedMethods, true)) {
                $paymentMethods = [$fallback];
            }
        }

        if (empty($paymentMethods)) {
            return [
                'valid' => false,
                'message' => 'Kérjük, válasszon legalább egy fizetési módot.',
            ];
        }

        $splitPayments = [];
        $cashAmount = 0.0;

        if (count($paymentMethods) > 1) {
            $rawSplit = isset($data['split_payments']) && is_array($data['split_payments']) ? $data['split_payments'] : [];
            $amountByMethod = [];

            foreach ($rawSplit as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $method = strtolower((string) ($row['method'] ?? ''));
                if (!in_array($method, $allowedMethods, true)) {
                    continue;
                }
                $amountByMethod[$method] = max(0, (float) ($row['amount'] ?? 0));
            }

            $sum = 0.0;
            foreach ($paymentMethods as $method) {
                $amount = (float) ($amountByMethod[$method] ?? 0);
                if ($amount <= 0) {
                    return [
                        'valid' => false,
                        'message' => 'Vegyes fizetésnél minden kiválasztott módhoz pozitív összeget kell megadni.',
                    ];
                }
                $splitPayments[] = [
                    'method' => $method,
                    'amount' => $amount,
                ];
                $sum += $amount;
            }

            if (abs($sum - $finalTotalGross) > 1) {
                return [
                    'valid' => false,
                    'message' => 'A megosztott fizetés összegeinek összege nem egyezik a fizetendő végösszeggel.',
                ];
            }
        } else {
            $splitPayments[] = [
                'method' => $paymentMethods[0],
                'amount' => $finalTotalGross,
            ];
        }

        $primaryMethod = strtolower((string) ($data['payment_method'] ?? ''));
        if (!in_array($primaryMethod, $paymentMethods, true)) {
            $sorted = $splitPayments;
            usort($sorted, function ($a, $b) {
                return ((float) $b['amount']) <=> ((float) $a['amount']);
            });
            $primaryMethod = $sorted[0]['method'] ?? $paymentMethods[0];
        }

        foreach ($splitPayments as $row) {
            if (($row['method'] ?? '') === 'cash') {
                $cashAmount = (float) ($row['amount'] ?? 0);
                break;
            }
        }

        return [
            'valid' => true,
            'primary_method' => $primaryMethod,
            'payment_methods' => $paymentMethods,
            'split_payments' => $splitPayments,
            'cash_amount' => $cashAmount,
            'billingo_comment' => $this->buildBillingoPaymentComment($splitPayments),
        ];
    }

    private function buildBillingoPaymentComment(array $splitPayments): string
    {
        if (empty($splitPayments)) {
            return '';
        }

        if (count($splitPayments) === 1) {
            $single = $splitPayments[0];
            return 'Fizetési mód: ' . $this->paymentMethodLabel((string) ($single['method'] ?? ''));
        }

        $parts = [];
        foreach ($splitPayments as $row) {
            $parts[] = number_format((float) ($row['amount'] ?? 0), 0, ',', ' ') . ' Ft ' . $this->paymentMethodLabel((string) ($row['method'] ?? ''));
        }

        return 'Vegyes fizetés: ' . implode(', ', $parts);
    }

    private function paymentMethodLabel(string $method): string
    {
        $labels = [
            'cash' => 'készpénz',
            'card' => 'bankkártya',
            'transfer' => 'átutalás',
        ];

        return $labels[strtolower($method)] ?? $method;
    }

     /**
     * Generate receipt text for serial port according to Sam4s documentation
     */
    protected function generateReceiptText($sale, $products, $saleData)
    {
        $lines = [];
        $vatCode = 'A00';
        
        // Start with header (PTG|1)
        $lines[] = "PTG|1";
        
        // Add sale identification (optional)
        $lines[] = sprintf('\0|sale_id|%d', $sale->id);
        
        // Process each product according to Sam4s format
        // Format: \1|árunév|cikkszám|ár|mennyiség|mennyiségi egység|teljes érték|ÁFA kör|vállalkozó|megjegyzés
        foreach ($products as $product) {
            // Get product name (max 99 chars)
            $productName = 'Termék';
            $productModel = null;
            $inventoryModel = null;
            if (isset($product['inventory_id'])) {
                $inventoryModel = Inventory::with('product')->find($product['inventory_id']);
                if ($inventoryModel) {
                    $productModel = $inventoryModel->product;
                    $productName = substr(($inventoryModel->name ?: optional($productModel)->name ?: 'Termék'), 0, 99);
                }
            }
            
            // Calculate price and total
            $vatRate = 27; // Assuming 27% VAT for all products, adjust if needed
            $price_temp = floatval($product['price_net'] ?? ($inventoryModel->oneprice ?? ($productModel->real_price ?? ($product['price'] ?? 0))));
            $price = round($price_temp * (1 + ($vatRate/100))); // Convert gross to net price
            $quantity = isset($product['quantity']) ? intval($product['quantity']) : 1;
            $total = $price * $quantity;
            
            /*
            // A termék árának kiszámítása
$nettoPrice = isset($product['real_price']) ? intval($product['real_price']) : 0; // 40500
$vatRate = 27; // 27%

// Bruttó ár számítása
$bruttoPrice = round($nettoPrice * (1 + $vatRate/100)); // 40500 * 1.27 = 51435
            */ 
            // Get VAT code (ÁFA kör)
            // Based on Sam4s documentation: A00, B00, C00, D00, E00
            // Assuming 27% VAT -> A00 (you may need to adjust based on your VAT rates)
            $vatCode = 'A00';
            
            // Optional fields
            $sku = ''; // cikkszám (optional)
            $vendor = ''; // vállalkozó (optional)
            $comment = isset($product['comment']) ? substr($product['comment'], 0, 50) : ''; // megjegyzés (max 50 chars)
            
            // Format the product line
            $line = sprintf(
                '\1|%s|%s|%d|%d|db|%d|%s|%s|%s',
                $productName,
                $sku,
                $price,
                $quantity,
                $total,
                $vatCode,
                $vendor,
                $comment
            );
            
            $lines[] = $line;
        }
        
        // Add discount if applicable
        $discountPercent = floatval($saleData['discount'] ?? 0);
        if ($discountPercent > 0) {
            $discountAmount = intval($sale->total_gross * ($discountPercent / 100));
            // Format: \3|ÁFA kör|engedmény érték
            $lines[] = sprintf('\3|%s|%d', $vatCode, $discountAmount);
        }
        
        // Add coupon discount
        $coupon = intval($saleData['coupon'] ?? 0);
        if ($coupon > 0) {
            $couponGross = intval($coupon);
            $lines[] = sprintf('\3|%s|%d', $vatCode, $couponGross);
        }
        
        // Add gift card discount
        $giftcard = intval($saleData['giftcard'] ?? 0);
        if ($giftcard > 0) {
            $giftcardGross = intval($giftcard);
            $lines[] = sprintf('\3|%s|%d', $vatCode, $giftcardGross);
        }
        
        // Add loyalty discount
        $loyalty = intval($saleData['loyalty'] ?? 0);
        if ($loyalty > 0) {
            $loyaltyGross = intval($loyalty);
            $lines[] = sprintf('\3|%s|%d', $vatCode, $loyaltyGross);
        }
        
        // Add payment section
        // Format: \6|fizetőeszköz sorszáma|fizetőeszköz érték
        $paymentMethod = $saleData['payment_method'] ?? 'cash';
        
        // Map payment method to Sam4s payment codes
        $paymentCodes = [
            'cash' => 1,        // készpénz (Ft)
            'card' => 2,        // bankkártya
            'transfer' => 4,    // utalvány
            'voucher' => 16,    // ajándékutalvány
            'loyalty' => 17,    // hűségkártya
            'szep' => 15,       // SZÉP kártya
            'erzsebet' => 14,   // Erzsébet utalvány
        ];
        
        $paymentCode = $paymentCodes[$paymentMethod] ?? 1;
        
        // Calculate final amount to pay
        $finalAmount = (round($sale->final_total_gross / 5) * 5);
        
        $lines[] = sprintf('\6|%d|%d', $paymentCode, $finalAmount);
        
        // Combine all lines
        return implode('', $lines);
    }

    protected function printReceiptFromSale($sale, $products)
    {
        $qrLines = [];
        
        // Start with header
        $qrLines[] = "PTG|1";
        
        // Add identification (optional)
        $qrLines[] = sprintf('\0|sale_id|%d', $sale->id);
        
        // Add each product
        foreach ($products as $index => $product) {
            $vatCode = $this->getVatCode($product['vat_rate']); // Convert your VAT rate to Sam4s codes
            $total = $product['price'] * $product['quantity'];
            
            $qrLines[] = sprintf(
                '\1|%s||%d|%d|db|%d|%s||',
                substr($product['name'], 0, 99), // Max 99 chars
                $product['price'],
                $product['quantity'],
                $total,
                $vatCode
            );
        }
        
        // Add payment (you need to map your payment method to Sam4s codes)
        $paymentCode = $this->getPaymentCode($sale->payment_method);
        $qrLines[] = sprintf('\6|%d|%d', $paymentCode, $sale->total_gross);
        
        // Combine all lines
        $qrString = implode('', $qrLines);
        
        $service = new Sam4sPrinterService();
        // Send to printer
        $service->sendQrToPrinter($qrString);
    }
    
    protected function getVatCode($vatRate)
    {
        // Map your VAT rates to Sam4s codes
        $mapping = [
            27 => 'A00', // 27% VAT
            18 => 'B00', // 18% VAT
            5  => 'C00', // 5% VAT
            0  => 'D00', // 0% VAT
            // Add more mappings as needed
        ];
        
        return $mapping[$vatRate] ?? 'A00';
    }
    
    protected function getPaymentCode($paymentMethod)
    {
        // Map your payment methods to Sam4s codes
        $mapping = [
            'cash' => 1,        // Cash
            'card' => 2,        // Bank card
            'transfer' => 4,    // Transfer voucher
            'voucher' => 16,    // Gift voucher
            // Add more mappings as needed
        ];
        
        return $mapping[$paymentMethod] ?? 1;
    }
    
    public function printReceipt1(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
        ]);
        
        try {
            $sale = Sale::with('items.product')->find($request->sale_id);
            $this->printReceiptFromSale($sale, $sale->items);
            
            return response()->json([
                'success' => true,
                'message' => 'Receipt printed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to print receipt', [
                'sale_id' => $request->sale_id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to print receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create next serial number for warehouse transfer
     * @return int
     * @throws \Exception
     * @throws \Throwable
     **/
    private function getNextSerialReservation()
    {
        $record = DB::table('serial_numbers')
            ->where('module', 'reservation')
            ->where('year', date('Y'))
            ->first();

        $serial = $record ? $record->serial_number : null;

        if (!$serial) {
            DB::table('serial_numbers')
                ->insert(['module' => 'reservation', 'year' => date('Y'), 'serial_number' => '1']);
            $serial = 1;
        }

        $next = ($serial+1);

        DB::table('serial_numbers')
            ->where('module', 'reservation')
            ->where('year', date('Y'))
            ->update(['serial_number' => $next]);

        return $serial;
    }

    public function getSaleSummary(Request $request)
    {
        $productsData = $request->input('products', []);
        $giftcard = (int) $request->input('giftcard', 0);
                
        $leftTable = '';
        $rightTable = '';
        $totalNet = 0;
        $totalGross = 0;

        $inventoryIds = collect($productsData)->pluck('inventory_id')->filter()->map(fn($id) => (int) $id)->unique()->values();
        $inventoryMap = Inventory::with('product')->whereIn('inventory_id', $inventoryIds)->get()->keyBy('inventory_id');

        foreach ($productsData as $p) {
            $inventoryId = (int) ($p['inventory_id'] ?? 0);
            $inventory = $inventoryMap->get($inventoryId);

            if (!$inventory) {
                return;
            }

            $net = floatval($p['price_net'] ?? $inventory->oneprice ?? 0);
            $vat = 27;
            $gross = round($net * (1 + $vat / 100));
            $quantity = max(1, (int) ($p['quantity'] ?? 1));

            $inventoryImage = DB::table('images')
                ->where('module', 'inventory')
                ->where('resource_id', $inventory->inventory_id)
                ->orderByDesc('created_at')
                ->first();

            $primaryImage = null;
            if ($inventory->product) {
                $primaryImage = $inventory->product->product_images()->where('is_primary', 1)->first();
            }
            $imageSrc = $inventoryImage
                ? asset('document_storage/inventory_images/' . $inventoryImage->image_path)
                : ($primaryImage
                    ? asset('document_storage/product_images/' . $primaryImage->image_path)
                    : asset('img/noimage.png'));

            $itemData = $inventory->item_data;
            if (is_string($itemData)) {
                $itemData = json_decode($itemData, true) ?: [];
            }
            if (!is_array($itemData)) {
                $itemData = [];
            }

            $termekTipusId = $itemData['termek_tipus_id']
                ?? $inventory->termek_tipus_id
                ?? $itemData['category_id']
                ?? null;

            $productTypeName = !empty($termekTipusId)
                ? ProductType::getName((int) $termekTipusId)
                : null;

            $displayName = $productTypeName
                ?: ($inventory->name ?: optional($inventory->product)->name ?: ('Tétel #' . $inventory->inventory_id));
            $displayDescription = $inventory->description ?: optional($inventory->product)->short_description;

            $leftTable .= '<tr>
                <td>' . e($displayName) . '</td>
                <td><img src="' . $imageSrc . '" style="max-width:100px;max-height:100px;border:none;"/></td>
                <td>' . $quantity . ' db</td>
                <td>' . e($displayDescription) . '</td>
                <td><button type="button" class="btn btn-link btn-danger btn-sm remove-sale-product-btn" data-inventory-id="' . (int) $inventory->inventory_id . '" title="Tétel törlése"><i class="fa fa-trash"></i></button></td>
            </tr>';

            $rightTable .= '<tr>
                <td>' . e($displayName) . '</td>
                <td>' . $vat . '%</td>
                <td>' . number_format($net, 0, ',', ' ') . ' Ft</td>
                <td>' . number_format($gross * $quantity, 0, ',', ' ') . ' Ft</td>
            </tr>';

            $totalNet += ($net * $quantity);
            $totalGross += ($gross * $quantity);
        }

        $discountBreakdown = $this->buildDiscountBreakdown($productsData, $inventoryMap, $request);
        $discountPercent = (float) $discountBreakdown['discount_percent'];
        $discountNet = (int) $discountBreakdown['discount_net'];
        $discountGross = (int) $discountBreakdown['discount_gross'];
        $couponNet = (int) $discountBreakdown['coupon_net'];
        $couponGross = (int) $discountBreakdown['coupon_gross'];
        $loyalty = (int) $discountBreakdown['loyalty_net'];

        // Összes kedvezmény (nettó és bruttó)
        $totalDiscountNet = $discountNet + $couponNet + $giftcard + $loyalty;
        $totalDiscountGross = $discountGross + $couponGross + $giftcard + $loyalty;

        // Eredeti összeg
        $shippingFee = 0;
        $salePackagePayload = $this->resolveSalePackagePayloadFromRequest($request);
        if ($salePackagePayload) {
            $shippingFee = (float) ($salePackagePayload['shipping_price'] ?? 0);
            if ($shippingFee > 0) {
                $rightTable .= '<tr><td>Szállítási díj - ' . e($salePackagePayload['shipping_service_name'] ?? 'Szállító') . '</td><td>0%</td>
                    <td>' . number_format($shippingFee, 0, ',', ' ') . ' Ft</td>
                    <td>' . number_format($shippingFee, 0, ',', ' ') . ' Ft</td></tr>';
            }
        }

        $rightTable .= '<tr><td>Összesen</td><td></td>
            <td>' . number_format($totalNet + $shippingFee, 0, ',', ' ') . ' Ft</td>
            <td>' . number_format($totalGross + $shippingFee, 0, ',', ' ') . ' Ft</td></tr>';

        // Kedvezmény részletek
        $rightTable .= '<tr><td>Hűségkártya</td><td></td>
            <td>' . number_format(-$loyalty, 0, ',', ' ') . ' Ft</td>
            <td>' . number_format(-$loyalty, 0, ',', ' ') . ' Ft</td></tr>';

        $rightTable .= '<tr><td>Kupon</td><td></td>
            <td>' . number_format(-$couponNet, 0, ',', ' ') . ' Ft</td>
            <td>' . number_format(-$couponGross, 0, ',', ' ') . ' Ft</td></tr>';

        $rightTable .= '<tr><td>Ajándékkártya</td><td></td>
            <td>' . number_format(-$giftcard, 0, ',', ' ') . ' Ft</td>
            <td>' . number_format(-$giftcard, 0, ',', ' ') . ' Ft</td></tr>';

        $rightTable .= '<tr><td>Ügyfélkedvezmény (' . $discountPercent . '%)</td><td></td>
            <td>' . number_format(-$discountNet, 0, ',', ' ') . ' Ft</td>
            <td>' . number_format(-$discountGross, 0, ',', ' ') . ' Ft</td></tr>';

        if (!empty($discountBreakdown['product_item_discount_net'])) {
            $rightTable .= '<tr><td>Ügyfélkártya termék kedvezmény</td><td></td>
                <td>' . number_format(-((int) $discountBreakdown['product_item_discount_net']), 0, ',', ' ') . ' Ft</td>
                <td>' . number_format(-((int) $discountBreakdown['product_item_discount_gross']), 0, ',', ' ') . ' Ft</td></tr>';
        }

        if (!empty($discountBreakdown['coupon_label'])) {
            $rightTable .= '<tr><td>Kupon kód: ' . e((string) $discountBreakdown['coupon_label']) . '</td><td></td><td></td><td></td></tr>';
        }

        $rightTable .= '<tr style="border-top: 2px solid #000; border-bottom: 2px solid #000;"><td>Kedvezmények összesen</td><td></td>
            <td>' . number_format($totalDiscountNet, 0, ',', ' ') . ' Ft</td>
            <td>' . number_format($totalDiscountGross, 0, ',', ' ') . ' Ft</td></tr>';

        $rightTable .= '<tr><td>Fizetendő végösszeg</td><td></td>
            <td>' . number_format(($totalNet + $shippingFee) - $totalDiscountNet, 0, ',', ' ') . ' Ft</td>
            <td>' . number_format(($totalGross + $shippingFee) - $totalDiscountGross, 0, ',', ' ') . ' Ft</td></tr>';

        return response()->json([
            'left_html' => $leftTable,
            'right_html' => $rightTable,
        ]);
    }

    public function getCustomerCards(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
        ]);

        $customerId = (int) $request->input('customer_id');
        $businessId = (int) (auth()->user()->employee?->active_business_id ?? 0);

        $cards = CustomerCard::active()
            ->validForDate()
            ->validForCustomer($customerId)
            ->when($businessId > 0, function ($q) use ($businessId) {
                $q->validForBusiness($businessId);
            })
            ->get()
            ->map(function ($card) {
                return [
                    'id' => $card->id,
                    'name' => $card->name,
                    'code' => $card->code,
                    'type' => $card->type,
                    'value' => (float) $card->value,
                    'status' => $card->status,
                ];
            })->values();

        return response()->json([
            'success' => true,
            'cards' => $cards,
        ]);
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = trim((string) $request->input('code'));
        $coupon = DiscountPrice::active()->where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Nincs érvényes kedvezménykártya ehhez a kódhoz.',
            ]);
        }

        return response()->json([
            'success' => true,
            'coupon' => [
                'id' => $coupon->id,
                'name' => $coupon->name,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
                'min_order_amount' => (float) ($coupon->min_order_amount ?? 0),
                'max_discount_amount' => (float) ($coupon->max_discount_amount ?? 0),
            ],
        ]);
    }

    private function buildDiscountBreakdown(array $products, $inventoryMap, Request $request): array
    {
        $totalNet = 0;
        $totalGross = 0;

        foreach ($products as $p) {
            $inventoryId = (int) ($p['inventory_id'] ?? 0);
            $inventory = $inventoryMap->get($inventoryId);
            if (!$inventory) {
                continue;
            }

            $net = floatval($p['price_net'] ?? $inventory->oneprice ?? 0);
            $quantity = max(1, (int) ($p['quantity'] ?? 1));
            $gross = round($net * 1.27);

            $totalNet += ($net * $quantity);
            $totalGross += ($gross * $quantity);
        }

        $discountPercent = max(0, (float) $request->input('discount', 0));
        $loyaltyNet = max(0, (int) $request->input('loyalty', 0));
        $couponNet = 0;
        $couponGross = 0;
        $couponLabel = null;
        $couponId = null;
        $productItemDiscountNet = 0;
        $productItemDiscountGross = 0;
        $customerCardId = (int) $request->input('customer_card_id', 0);
        $customerCardType = $request->input('customer_card_type');

        if ($customerCardId > 0 && $customerCardType === 'product_item') {
            $card = CustomerCard::with('products')->find($customerCardId);
            if ($card && $card->isValidFor((int) (auth()->user()->employee?->active_business_id ?? 0), (int) $request->input('customer_id', 0))) {
                foreach ($products as $p) {
                    $inventoryId = (int) ($p['inventory_id'] ?? 0);
                    $quantity = max(1, (int) ($p['quantity'] ?? 1));
                    if ($inventoryId <= 0) {
                        continue;
                    }

                    $inventory = $inventoryMap->get($inventoryId);
                    if (!$inventory) {
                        continue;
                    }

                    if ($card->products->contains('inventory_id', $inventoryId)) {
                        $net = floatval($p['price_net'] ?? $inventory->oneprice ?? 0);
                        $gross = round($net * 1.27);
                        $productItemDiscountNet += (int) round($net * $quantity);
                        $productItemDiscountGross += (int) round($gross * $quantity);
                    }
                }
            }
        }

        $couponCode = trim((string) $request->input('coupon_code', ''));
        if ($couponCode !== '') {
            $coupon = DiscountPrice::active()->where('code', $couponCode)->first();
            if ($coupon) {
                $couponId = $coupon->id;
                $couponLabel = $coupon->code;
                $minOrder = (float) ($coupon->min_order_amount ?? 0);
                if ($totalNet >= $minOrder) {
                    if ($coupon->type === 'percentage') {
                        $calc = $totalGross * ((float) $coupon->value / 100);
                        $maxAmount = (float) ($coupon->max_discount_amount ?? 0);
                        if ($maxAmount > 0) {
                            $calc = min($calc, $maxAmount);
                        }
                        $couponGross = (int) round($calc);
                        $couponNet = (int) round($couponGross / 1.27);
                        if ($discountPercent <= 0) {
                            $discountPercent = (float) $coupon->value;
                        }
                    } else {
                        $couponNet = (int) round((float) $coupon->value);
                        $couponGross = $couponNet;
                        if ($loyaltyNet <= 0) {
                            $loyaltyNet = $couponNet;
                        }
                    }
                }
            }
        }

        $discountGross = (int) round($totalGross * ($discountPercent / 100));
        $discountNet = (int) round($discountGross / 1.27);
        $discountNet += $productItemDiscountNet;
        $discountGross += $productItemDiscountGross;

        return [
            'discount_percent' => $discountPercent,
            'discount_net' => $discountNet,
            'discount_gross' => $discountGross,
            'coupon_net' => $couponNet,
            'coupon_gross' => $couponGross,
            'giftcard_net' => max(0, (int) $request->input('giftcard', 0)),
            'loyalty_net' => $loyaltyNet,
            'product_item_discount_net' => $productItemDiscountNet,
            'product_item_discount_gross' => $productItemDiscountGross,
            'coupon_label' => $couponLabel,
            'coupon_id' => $couponId,
            'customer_card_id' => $customerCardId,
        ];
    }

    private function resolveSalePackagePayloadFromRequest(Request $request): ?array
    {
        $payload = $request->input('sale_package_payload');

        if (!is_array($payload)) {
            return null;
        }

        if (empty($payload['shipping_service_id'])) {
            return null;
        }

        return $payload;
    }

    private function createPackageFromPayloadAndProducts(array $payload, array $products): ?Package
    {
        $shippingService = ShippingService::find((int) ($payload['shipping_service_id'] ?? 0));
        if (!$shippingService) {
            return null;
        }

        $package = Package::create([
            'order_id' => null,
            'shipping_service_id' => $shippingService->id,
            'shipping_service' => $shippingService->code,
            'shipping_price' => (float) ($payload['shipping_price'] ?? $shippingService->base_price),
            'tracking_number' => $payload['tracking_number'] ?? null,
            'weight' => $payload['weight'] ?? null,
            'notes' => $payload['notes'] ?? null,
            'status' => 'created',
        ]);

        if (($payload['type'] ?? 'package') === 'cod') {
            CashOnDelivery::create([
                'package_id' => $package->id,
                'amount' => (float) ($payload['cash_on_delivery_amount'] ?? 0),
                'bank_account_number' => (string) ($payload['cash_on_delivery_bank_account'] ?? ''),
                'recipient_name' => (string) ($payload['cash_on_delivery_recipient'] ?? ''),
                'bank_name' => $payload['cash_on_delivery_bank_name'] ?? null,
                'notes' => $payload['cash_on_delivery_notes'] ?? null,
                'status' => 'pending',
            ]);
        }

        $grouped = [];
        foreach ($products as $product) {
            $inventoryId = (int) ($product['inventory_id'] ?? 0);
            if ($inventoryId <= 0) {
                continue;
            }
            $grouped[$inventoryId] = ($grouped[$inventoryId] ?? 0) + max(1, (int) ($product['quantity'] ?? 1));
        }

        foreach ($grouped as $inventoryId => $quantity) {
            PackageItem::updateOrCreate(
                [
                    'package_id' => $package->id,
                    'itemable_type' => Inventory::class,
                    'itemable_id' => $inventoryId,
                ],
                [
                    'quantity' => $quantity,
                    'order_item_id' => null,
                ]
            );
        }

        return $package;
    }

    public function printReceipt(Request $request)
    {
        return $this->generateSalePdf($request->sale_id, 'receipt');
    }

    public function printInvoice(Request $request)
    {
        return $this->generateSalePdf($request->sale_id, 'invoice');
    }

    public function printDeposit(Request $request)
    {
        return $this->generateSalePdf($request->sale_id, 'deposit');
    }


    private function generateSalePdf($sale_id, $type)
    {
        // Töröljük a korábbi PDF-eket
        array_map('unlink', glob(public_path("pdf/sales/*")));

        $file_name = 'sale_' . $type . '_' . $sale_id . '_' . time() . '.pdf';
        $save_path = public_path("pdf/sales/" . $file_name);

        // TEMP tábla alapján lekérjük az adatokat
        $sale = \App\Models\TempSale::find($sale_id);
        if (!$sale) {
            abort(404, 'Értékesítés nem található.');
        }

        $items = \App\Models\TempSaleItem::where('temp_sale_id', $sale_id)->get();

        // Nyomtatási idő mentése első nyomtatáskor
        if (!$sale->printed_at) {
            $sale->printed_at = now();
            $sale->is_printed = 1;
            $sale->save();
        }

        // PDF generálás
        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

        $view = 'pdf.sale_' . $type;
        if ($type === 'booking' && count($items) > 2) {
            $view = 'pdf.sale_booking_2';
        }

        $pdf->loadView($view, [
            'sale' => $sale,
            'items' => $items
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->save($save_path);

        return asset('pdf/sales/' . $file_name);
    }



    


}
