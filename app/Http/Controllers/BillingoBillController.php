<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BillingoBill;
use App\Services\Billingo\BillingoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Http;

class BillingoBillController extends Controller
{
    protected $billingoService;

    public function __construct(BillingoService $billingoService)
    {
        $this->billingoService = $billingoService;
        $this->middleware('auth');
    }

    /**
     * Display a listing of bills
     */
    public function index(Request $request)
    {
        //$this->authorize('viewAny', BillingoBill::class);

        $query = BillingoBill::query();

        // Apply filters
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->has('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('paid') && $request->paid !== 'all') {
            $query->where('paid', $request->paid === 'paid');
        }

        if ($request->has('date_from')) {
            $query->whereDate('fulfillment_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('fulfillment_date', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhere('partner_name', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        // Order by
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $bills = $query->paginate(25);

        // Statistics
        $stats = [
            'total' => BillingoBill::count(),
            'outgoing' => BillingoBill::where('type', 'outgoing')->count(),
            'incoming' => BillingoBill::where('type', 'incoming')->count(),
            'paid' => BillingoBill::where('paid', true)->count(),
            'unpaid' => BillingoBill::where('paid', false)->count(),
            'overdue' => BillingoBill::overdue()->count(),
            'total_amount' => BillingoBill::sum('gross_total'),
        ];

        return view('billingo-bills.index', compact('bills', 'stats'));
    }

    /**
     * Show the form for creating a new bill
     */
    public function create()
    {
        //$this->authorize('create', BillingoBill::class);

        $billTypes = [
            'outgoing' => 'Kimenő számla',
            'incoming' => 'Bejövő számla',
            'draft' => 'Piszkozat',
            'proforma' => 'Proforma számla',
            'advance' => 'Előlegszámla',
        ];

        $paymentMethods = [
            'cash' => 'Készpénz',
            'bank_transfer' => 'Átutalás',
            'credit_card' => 'Bankkártya',
            'cash_on_delivery' => 'Utánvét',
            'other' => 'Egyéb',
        ];

        $vatRates = [
            '0%' => '0% (ÁFA mentes)',
            '5%' => '5% (kedvezményes)',
            '18%' => '18% (alacsony)',
            '27%' => '27% (normál)',
        ];

        $units = [
            'db' => 'Darab',
            'kg' => 'Kilogramm',
            'm' => 'Méter',
            'm2' => 'Négyzetméter',
            'h' => 'Óra',
            'nap' => 'Nap',
            'hó' => 'Hónap',
            'év' => 'Év',
        ];

        return view('billingo-bills.create', compact('billTypes', 'paymentMethods', 'vatRates', 'units'));
    }

    public function store(Request $request)
    {
        //$this->authorize('create', BillingoBill::class);

        $validated = $request->validate([
            'type' => 'required|in:outgoing,incoming,draft,proforma,advance',
            
            // Partner data validation
            'partner_name' => 'required|string|max:255',
            'partner_tax_number' => 'nullable|string|max:50',
            'partner_email' => 'nullable|email',
            'partner_phone' => 'nullable|string|max:20',
            'partner_address' => 'required|string',
            'partner_city' => 'required|string',
            'partner_zip_code' => 'required|string',
            'partner_is_company' => 'nullable|boolean',
            
            // Bill data validation
            'fulfillment_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:fulfillment_date',
            'payment_method' => 'required|string',
            'currency' => 'required|in:HUF,EUR,USD',
            'comment' => 'nullable|string|max:500',
            
            // Items validation
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.vat' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Prepare partner data for Billingo
            $partnerData = [
                'name' => $validated['partner_name'],
                'tax_number' => $validated['partner_tax_number'] ?? '',
                'email' => $validated['partner_email'] ?? '',
                'phone' => $validated['partner_phone'] ?? '',
                'address' => $validated['partner_address'],
                'city' => $validated['partner_city'],
                'zip_code' => $validated['partner_zip_code'],
                'is_company' => $validated['partner_is_company'] ?? false,
            ];

            // Prepare items data
            $billingoItems = [];
            $totalNet = 0;
            
            foreach ($validated['items'] as $item) {
                $netPrice = floatval($item['unit_price']);
                $quantity = floatval($item['quantity']);
                $itemNetTotal = $netPrice * $quantity;
                $totalNet += $itemNetTotal;
                
                $billingoItems[] = [
                    'product_name' => $item['name'],
                    'price_net' => $netPrice,
                    'quantity' => $quantity,
                    'unit' => $item['unit'],
                    'vat' => str_replace('%', '', $item['vat']),
                ];
            }

            // Prepare bill data
            $billData = [
                'fulfillment_date' => $validated['fulfillment_date'],
                'due_date' => $validated['due_date'],
                'payment_method' => $validated['payment_method'],
                'currency' => $validated['currency'],
                'comment' => $validated['comment'] ?? '',
            ];

            // Determine which Billingo service method to use
            $billingoResult = null;
            
            if ($validated['type'] === 'incoming') {
                $billingoResult = $this->billingoService->createBill($billData, $billingoItems, $partnerData);
            } else {
                $billingoResult = $this->billingoService->createInvoice($billData, $billingoItems, $partnerData);
            }

            if (!$billingoResult) {
                throw new \Exception('Failed to create bill in Billingo');
            }

            // Calculate totals for local database
            $vatRates = [];
            foreach ($validated['items'] as $item) {
                $vatRate = str_replace('%', '', $item['vat']);
                $netPrice = floatval($item['unit_price']);
                $quantity = floatval($item['quantity']);
                $itemNetTotal = $netPrice * $quantity;
                
                if (!isset($vatRates[$vatRate])) {
                    $vatRates[$vatRate] = 0;
                }
                $vatRates[$vatRate] += $itemNetTotal;
            }

            $grossTotal = 0;
            foreach ($vatRates as $vatRate => $netAmount) {
                $vatAmount = $netAmount * ($vatRate / 100);
                $grossTotal += $netAmount + $vatAmount;
            }

            // Create local bill record with additional partner data
            $bill = BillingoBill::create([
                'billingo_id' => $billingoResult['id'],
                'bill_number' => $billingoResult['invoice_number'] ?? 'DRAFT-' . time(),
                'type' => $validated['type'],
                'partner_name' => $validated['partner_name'],
                'partner_id' => null, // You might want to store customer_id here
                'partner_tax_number' => $validated['partner_tax_number'] ?? null,
                'partner_email' => $validated['partner_email'] ?? null,
                'partner_phone' => $validated['partner_phone'] ?? null,
                'partner_address' => $validated['partner_address'],
                'partner_city' => $validated['partner_city'],
                'partner_zip_code' => $validated['partner_zip_code'],
                'partner_is_company' => $validated['partner_is_company'] ?? false,
                'net_total' => $totalNet,
                'gross_total' => $grossTotal,
                'currency' => $validated['currency'],
                'fulfillment_date' => $validated['fulfillment_date'],
                'due_date' => $validated['due_date'],
                'payment_status' => 'not_paid',
                'payment_method' => $validated['payment_method'],
                'electronic' => false,
                'paid' => false,
                'comment' => $validated['comment'] ?? '',
                'billingo_data' => $billingoResult,
                'public_url' => $billingoResult['public_url'] ?? null,
                'download_url' => $billingoResult['download_url'] ?? null,
            ]);

            // Save bill items
            foreach ($validated['items'] as $itemData) {
                $netPrice = floatval($itemData['unit_price']);
                $quantity = floatval($itemData['quantity']);
                $vatRate = str_replace('%', '', $itemData['vat']);
                
                $itemNetTotal = $netPrice * $quantity;
                $vatAmount = $itemNetTotal * ($vatRate / 100);
                $itemGrossTotal = $itemNetTotal + $vatAmount;
                
                $bill->items()->create([
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? '',
                    'unit_price' => $netPrice,
                    'unit_price_type' => 'net',
                    'quantity' => $quantity,
                    'unit' => $itemData['unit'],
                    'vat' => $itemData['vat'],
                    'vat_amount' => $vatAmount,
                    'net_total' => $itemNetTotal,
                    'gross_total' => $itemGrossTotal,
                ]);
            }

            DB::commit();

            // Download PDF if available
            if (isset($billingoResult['download_url'])) {
                try {
                    $bill->downloadPdf();
                } catch (\Exception $e) {
                    Log::warning('Failed to download PDF after bill creation: ' . $e->getMessage());
                }
            }

            return redirect()->route('billingo-bills.index')
                ->with('success', 'Számla sikeresen létrehozva és szinkronizálva a Billingóval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bill creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Számla létrehozása sikertelen: ' . $e->getMessage());
        }
    }
    /**
     * Store a newly created bill
     */
    public function storeold(Request $request)
    {
        //$this->authorize('create', BillingoBill::class);

        $validated = $request->validate([
            'type' => 'required|in:outgoing,incoming,draft,proforma,advance',
            'partner_name' => 'required|string|max:255',
            'partner_tax_number' => 'nullable|string|max:50',
            'partner_email' => 'nullable|email',
            'partner_address' => 'required|string',
            'partner_city' => 'required|string',
            'partner_zip_code' => 'required|string',
            'fulfillment_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:fulfillment_date',
            'payment_method' => 'required|string',
            'currency' => 'required|in:HUF,EUR,USD',
            'comment' => 'nullable|string|max:500',
            
            // Items validation
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.vat' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Prepare partner data
            $partnerData = [
                'name' => $validated['partner_name'],
                'tax_number' => $validated['partner_tax_number'] ?? '',
                'email' => $validated['partner_email'] ?? '',
                'address' => $validated['partner_address'],
                'city' => $validated['partner_city'],
                'zip_code' => $validated['partner_zip_code'],
            ];

            // Prepare items data for Billingo
            $billingoItems = [];
            $totalNet = 0;
            
            foreach ($validated['items'] as $item) {
                $netPrice = floatval($item['unit_price']);
                $quantity = floatval($item['quantity']);
                $itemNetTotal = $netPrice * $quantity;
                $totalNet += $itemNetTotal;
                
                $billingoItems[] = [
                    'product_name' => $item['name'],
                    'description' => $item['description'] ?? '',
                    'price_net' => $netPrice,
                    'quantity' => $quantity,
                    'unit' => $item['unit'],
                    'vat' => str_replace('%', '', $item['vat']),
                ];
            }

            // Prepare bill data for Billingo
            $billData = [
                'fulfillment_date' => $validated['fulfillment_date'],
                'due_date' => $validated['due_date'],
                'payment_method' => $validated['payment_method'],
                'currency' => $validated['currency'],
                'comment' => $validated['comment'] ?? '',
            ];

            // Determine which Billingo service method to use
            $billingoResult = null;
            
            if ($validated['type'] === 'incoming') {
                // Create incoming bill in Billingo
                $billingoResult = $this->billingoService->createBill($billData, $billingoItems, $partnerData);
            } else {
                // Create outgoing invoice in Billingo (using existing method)
                $billingoResult = $this->billingoService->createInvoice($billData, $billingoItems, $partnerData);
            }

            if (!$billingoResult) {
                throw new \Exception('Failed to create bill in Billingo');
            }

            // Calculate totals for local database
            $vatRates = [];
            foreach ($validated['items'] as $item) {
                $vatRate = str_replace('%', '', $item['vat']);
                $netPrice = floatval($item['unit_price']);
                $quantity = floatval($item['quantity']);
                $itemNetTotal = $netPrice * $quantity;
                
                if (!isset($vatRates[$vatRate])) {
                    $vatRates[$vatRate] = 0;
                }
                $vatRates[$vatRate] += $itemNetTotal;
            }

            $grossTotal = 0;
            foreach ($vatRates as $vatRate => $netAmount) {
                $vatAmount = $netAmount * ($vatRate / 100);
                $grossTotal += $netAmount + $vatAmount;
            }

            // Create local bill record
            $bill = BillingoBill::create([
                'billingo_id' => $billingoResult['id'],
                'bill_number' => $billingoResult['invoice_number'] ?? 'DRAFT-' . time(),
                'type' => $validated['type'],
                'partner_name' => $validated['partner_name'],
                'net_total' => $totalNet,
                'gross_total' => $grossTotal,
                'currency' => $validated['currency'],
                'fulfillment_date' => $validated['fulfillment_date'],
                'due_date' => $validated['due_date'],
                'payment_status' => 'not_paid',
                'payment_method' => $validated['payment_method'],
                'electronic' => false,
                'paid' => false,
                'comment' => $validated['comment'] ?? '',
                'billingo_data' => $billingoResult,
                'public_url' => $billingoResult['public_url'] ?? null,
                'download_url' => $billingoResult['download_url'] ?? null,
            ]);

            // Save bill items
            foreach ($validated['items'] as $itemData) {
                $netPrice = floatval($itemData['unit_price']);
                $quantity = floatval($itemData['quantity']);
                $vatRate = str_replace('%', '', $itemData['vat']);
                
                $itemNetTotal = $netPrice * $quantity;
                $vatAmount = $itemNetTotal * ($vatRate / 100);
                $itemGrossTotal = $itemNetTotal + $vatAmount;
                
                $bill->items()->create([
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? '',
                    'unit_price' => $netPrice,
                    'unit_price_type' => 'net',
                    'quantity' => $quantity,
                    'unit' => $itemData['unit'],
                    'vat' => $itemData['vat'],
                    'vat_amount' => $vatAmount,
                    'net_total' => $itemNetTotal,
                    'gross_total' => $itemGrossTotal,
                ]);
            }

            DB::commit();

            // Download PDF if available
            if (isset($billingoResult['download_url'])) {
                try {
                    $bill->downloadPdf();
                } catch (\Exception $e) {
                    Log::warning('Failed to download PDF after bill creation: ' . $e->getMessage());
                }
            }

            return redirect()->route('billingo-bills.index')
                ->with('success', 'Számla sikeresen létrehozva és szinkronizálva a Billingóval.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bill creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Számla létrehozása sikertelen: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified bill
     */
    public function show(BillingoBill $billingoBill)
    {
        //$this->authorize('view', $billingoBill);

        // Load items with the bill
        $billingoBill->load('items');
        
        // Try to get fresh data from Billingo
        try {
            $freshData = $this->billingoService->getBill($billingoBill->billingo_id);
            if ($freshData) {
                // Update local record with fresh data
                $billingoBill->update([
                    'payment_status' => $freshData['payment_status'] ?? $billingoBill->payment_status,
                    'paid' => $freshData['paid'] ?? $billingoBill->paid,
                    'paid_date' => $freshData['paid_date'] ?? $billingoBill->paid_date,
                    'billingo_data' => $freshData,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to fetch fresh bill data: ' . $e->getMessage());
        }

        return view('billingo-bills.show', compact('billingoBill'));
    }

    /**
     * Download PDF
     */
    public function downloadPdf(BillingoBill $billingoBill)
    {
        //$this->authorize('view', $billingoBill);

        try {
            if (!$billingoBill->pdf_path && $billingoBill->billingo_id) {
                $billingoBill->downloadPdf();
            }

            if ($billingoBill->pdf_path && file_exists(public_path($billingoBill->pdf_path))) {
                return response()->download(public_path($billingoBill->pdf_path));
            }

            // Fallback to direct Billingo download
            if ($billingoBill->billingo_id) {
                $response = Http::withHeaders([
                    'X-API-KEY' => config('services.billingo.api_key'),
                    'Accept' => 'application/pdf',
                ])->get("https://api.billingo.hu/v3/documents/{$billingoBill->billingo_id}/download");

                if ($response->successful()) {
                    $filename = "szamla-{$billingoBill->bill_number}.pdf";
                    return response($response->body(), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    ]);
                }
            }

            return redirect()->back()->with('error', 'PDF nem elérhető.');
            
        } catch (\Exception $e) {
            Log::error('PDF download failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'PDF letöltése sikertelen.');
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, BillingoBill $billingoBill)
    {
        //$this->authorize('update', $billingoBill);

        $validated = $request->validate([
            'paid' => 'required|boolean',
            'paid_date' => 'nullable|date',
        ]);

        try {
            // Update in Billingo
            $success = $this->billingoService->updateBillPaymentStatus(
                $billingoBill->billingo_id,
                $validated['paid'],
                $validated['paid_date'] ?? null
            );

            if ($success) {
                // Update local record
                $billingoBill->update([
                    'paid' => $validated['paid'],
                    'payment_status' => $validated['paid'] ? 'paid' : 'not_paid',
                    'paid_date' => $validated['paid'] ? ($validated['paid_date'] ?? now()) : null,
                ]);

                return redirect()->back()->with('success', 'Fizetési státusz frissítve.');
            }

            return redirect()->back()->with('error', 'Fizetési státusz frissítése sikertelen a Billingóban.');
            
        } catch (\Exception $e) {
            Log::error('Payment status update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hiba történt: ' . $e->getMessage());
        }
    }

    /**
     * Sync single bill with Billingo
     */
    public function syncPost(BillingoBill $billingoBill)
    {
        //$this->authorize('update', $billingoBill);

        try {
            if (!$billingoBill->billingo_id) {
                return redirect()->back()->with('error', 'Nincs Billingo ID a szinkronizáláshoz.');
            }

            $freshData = $this->billingoService->getBill($billingoBill->billingo_id);
            
            if ($freshData) {
                $billingoBill->update([
                    'payment_status' => $freshData['payment_status'] ?? $billingoBill->payment_status,
                    'paid' => $freshData['paid'] ?? $billingoBill->paid,
                    'paid_date' => $freshData['paid_date'] ?? $billingoBill->paid_date,
                    'billingo_data' => $freshData,
                    'updated_at' => now(),
                ]);

                // Download PDF if not exists
                if (!$billingoBill->pdf_path) {
                    $billingoBill->downloadPdf();
                }

                return redirect()->back()->with('success', 'Számla sikeresen szinkronizálva.');
            }

            return redirect()->back()->with('error', 'Nem sikerült lekérni az adatokat a Billingótól.');
            
        } catch (\Exception $e) {
            Log::error('Bill sync failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Szinkronizálás sikertelen: ' . $e->getMessage());
        }
    }

    /**
     * Quick actions (mark as paid/unpaid)
     */
    public function quickAction(Request $request, BillingoBill $billingoBill)
    {
        //$this->authorize('update', $billingoBill);

        $action = $request->input('action');
        
        switch ($action) {
            case 'mark_paid':
                return $this->updatePaymentStatus(new Request([
                    'paid' => true,
                    'paid_date' => now()->format('Y-m-d'),
                ]), $billingoBill);
                
            case 'mark_unpaid':
                return $this->updatePaymentStatus(new Request([
                    'paid' => false,
                ]), $billingoBill);
                
            case 'download_pdf':
                return $this->downloadPdf($billingoBill);
                
            default:
                return redirect()->back()->with('error', 'Ismeretlen művelet.');
        }
    }
    /**
     * Search customers for autocomplete
     */
    public function sync(Request $request, BillingoBill $billingoBill)
    {
        //$this->authorize('update', $billingoBill);

        // If it's GET, show confirmation page
        /*if ($request->method() === 'GET') {
            return view('billingo-bills.confirm-sync', compact('billingoBill'));
        }
        */
        // If it's POST, perform the sync (your existing code)
        try {
            if (!$billingoBill->billingo_id) {
                return redirect()->back()->with('error', 'Nincs Billingo ID a szinkronizáláshoz.');
            }

            $freshData = $this->billingoService->getBill($billingoBill->billingo_id);
            
            if ($freshData) {
                $billingoBill->update([
                    'payment_status' => $freshData['payment_status'] ?? $billingoBill->payment_status,
                    'paid' => $freshData['paid'] ?? $billingoBill->paid,
                    'paid_date' => $freshData['paid_date'] ?? $billingoBill->paid_date,
                    'billingo_data' => $freshData,
                    'updated_at' => now(),
                ]);

                // Download PDF if not exists
                if (!$billingoBill->pdf_path) {
                    $billingoBill->downloadPdf();
                }

                return redirect()->back()->with('success', 'Számla sikeresen szinkronizálva.');
            }

            return redirect()->back()->with('error', 'Nem sikerült lekérni az adatokat a Billingótól.');
            
        } catch (\Exception $e) {
            Log::error('Bill sync failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Szinkronizálás sikertelen: ' . $e->getMessage());
        }
    }

    public function searchCustomers(Request $request)
    {
        $search = $request->get('q');

        $customers = Customer::where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('tax_number', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->with(['addresses' => function($query) {
                $query->orderBy('address_id', 'desc')->take(1);
            }])
            ->limit(10)
            ->get();
        
        $formattedCustomers = $customers->map(function($customer) {
            $primaryAddress = $customer->addresses->first();
            
            return [
                'id' => $customer->customer_id,
                'name' => $customer->name,
                'tax_number' => $customer->tax_number,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $primaryAddress ? $primaryAddress->address : '',
                'city' => $primaryAddress ? $primaryAddress->city : '',
                'zip_code' => $primaryAddress ? $primaryAddress->zip : '',
                'is_company' => $customer->is_company,
                'full_text' => $customer->name . ' - ' . ($customer->tax_number ?? '') . ' - ' . ($primaryAddress ? $primaryAddress->city : ''),
            ];
        });
        
        return response()->json($formattedCustomers);
    }
}