<?php

namespace App\Services\Billingo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BillingoService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.billingo.hu/v3';
    protected $blockId = 307981;

    public function __construct()
    {
        $this->apiKey = env('BILLINGO_API_KEY', '23b1bc5a-f6d5-11f0-859c-06e1fe7801c9');
    }

    /**  23b1bc5a-f6d5-11f0-859c-06e1fe7801c9
     * UTF-8 string cleaning
     */
    protected function cleanString($string): string
    {
        if (!is_string($string)) {
            return '';
        }
        
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        $string = preg_replace('/[^\x20-\x7E\x{00A0}-\x{00FF}\x{0100}-\x{017F}\x{0180}-\x{024F}]/u', '', $string);
        $string = trim($string);
        
        return $string ?: '';
    }

    /**
     * Prepare partner data
     */
    protected function preparePartnerData(array $customerData): array
    {
        $name = $this->cleanString($customerData['name'] ?? '');
        
        if (empty($name)) {
            $name = 'Vásárló ' . date('md-His');
        }
        
        $email = $this->cleanString($customerData['email'] ?? '');
        $emails = [];
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emails[] = $email;
        }

        return [
            'name' => $name,
            'address' => [
                'country_code' => 'HU',
                'post_code' => $customerData['zip_code'] ?? '1000',
                'city' => $this->cleanString($customerData['city'] ?? 'Budapest'),
                'address' => $this->cleanString($customerData['address'] ?? ''),
            ],
            'emails' => $emails,
            'phone' => $customerData['phone'] ?? '',
            'taxcode' => $customerData['tax_number'] ?? '',
            'tax_type' => 'individual',
            'payment_days' => 8,
        ];
    }

    /**
     * CREATE INVOICE WITH PARTNER OBJECT (Primary method)
     */
    public function createInvoice(array $saleData, array $items, array $customerData = null): ?array
    {
        try {
            // 1. Prepare partner data
            $partnerData = $this->preparePartnerData($customerData ?? []);
            
            $partnerId = $this->createPartner($customerData ?? []);
            
            if ($partnerId) {
                $partnerData['id'] = $partnerId;
            }   

            Log::info('Prepared partner data:', [
                'id' => $partnerData['id'],
                'name' => $partnerData['name'],
                'has_email' => !empty($partnerData['emails'])
            ]);

            // 2. Format items
            $invoiceItems = [];
            
            foreach ($items as $index => $item) {
                $netPrice = floatval($item['price_net'] ?? 0);
                $quantity = intval($item['quantity'] ?? 1);
                
                $invoiceItems[] = [
                    'name' => $this->cleanString($item['product_name'] ?? 'Termék ' . ($index + 1)),
                    'unit_price' => $netPrice,
                    'unit_price_type' => 'net',
                    'quantity' => $quantity,
                    'unit' => $item['unit'] ?? 'db',
                    'vat' => $this->getVatString($item['vat'] ?? 27),
                    'comment' => $this->cleanString(substr($item['description'] ?? '', 0, 100)),
                    'entitlement' => $item['entitlement'] ?? '',
                ];
            }

            // 3. Add discounts
            $this->addDiscounts($invoiceItems, $saleData, $items);

            // 4. Invoice data with partner object
            $invoiceData = [
                'partner_id' => $partnerData['id'] ?? null,
                'block_id' => $this->blockId,
                'type' => 'invoice',
                'fulfillment_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+8 days')),
                'payment_method' => $this->mapPaymentMethod($saleData['payment_method'] ?? 'cash'),
                'language' => 'hu',
                'currency' => 'HUF',
                'conversion_rate' => 1,
                'electronic_invoice' => false,
                'paid' => false,
                'items' => $invoiceItems,
                'comment' => $this->cleanString($saleData['comment'] ?? 'Vásárlás'),
                'settings' => [
                    'mediated_service' => false,
                    'without_financial_fulfillment' => false,
                ],
            ];

            Log::info('Invoice with partner object:', [
                'partner_id' => $invoiceData['partner_id'],
                'partner_name' => $invoiceData['partner']['name'] ?? 'N/A'
            ]);

            // 5. API call
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/documents', $invoiceData);
            
            if ($response->successful()) {
                $invoice = $response->json();
                
                Log::info('Invoice created with partner object', [
                    'invoice_id' => $invoice['id'],
                    'invoice_number' => $invoice['invoice_number'] ?? 'N/A'
                ]);
                
                return $this->formatInvoiceResponse($invoice);
            } else {
                Log::error('Invoice with partner object failed:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
            
        } catch (\Exception $e) {
            Log::error('Invoice with partner object exception: ' . $e->getMessage());
            
            // Fallback to partner_id method
            //return $this->createInvoiceWithPartnerId($saleData, $items, $customerData);
        }

        return null;
    }

    /**
     * CREATE INVOICE WITH PARTNER ID (Fallback method)
     */
    public function createInvoiceWithPartnerId(array $saleData, array $items, array $customerData = null): ?array
    {
        try {
            // 1. First create a partner
            $partnerId = $this->createPartner($customerData ?? []);
            
            if (!$partnerId) {
                Log::error('Partner creation failed');
                return null;
            }

            Log::info('Partner created with ID:', ['partner_id' => $partnerId]);

            // 2. Format items
            $invoiceItems = [];
            
            foreach ($items as $index => $item) {
                $netPrice = floatval($item['price_net'] ?? 0);
                $quantity = intval($item['quantity'] ?? 1);
                
                $invoiceItems[] = [
                    'name' => $this->cleanString($item['product_name'] ?? 'Termék ' . ($index + 1)),
                    'unit_price' => $netPrice,
                    'unit_price_type' => 'net',
                    'quantity' => $quantity,
                    'unit' => $item['unit'] ?? 'db',
                    'vat' => $this->getVatString($item['vat'] ?? 27),
                    'comment' => $this->cleanString(substr($item['description'] ?? '', 0, 100)),
                    'entitlement' => $item['entitlement'] ?? '',
                ];
            }

            // 3. Add discounts
            $this->addDiscounts($invoiceItems, $saleData, $items);

            // 4. Invoice data with partner_id
            $invoiceData = [
                'partner_id' => $partnerId,
                'block_id' => $this->blockId,
                'type' => 'invoice',
                'fulfillment_date' => date('Y-m-d'),
                'due_date' => date('Y-m-d', strtotime('+8 days')),
                'payment_method' => $this->mapPaymentMethod($saleData['payment_method'] ?? 'cash'),
                'language' => 'hu',
                'currency' => 'HUF',
                'conversion_rate' => 1,
                'electronic_invoice' => false,
                'paid' => false,
                'items' => $invoiceItems,
                'comment' => $this->cleanString($saleData['comment'] ?? 'Vásárlás'),
                'settings' => [
                    'mediated_service' => false,
                    'without_financial_fulfillment' => false,
                ],
            ];

            // 5. API call
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/documents', $invoiceData);
            
            if ($response->successful()) {
                $invoice = $response->json();
                
                Log::info('Invoice created with partner_id', [
                    'invoice_id' => $invoice['id'],
                    'partner_id' => $partnerId
                ]);
                
                $response = $this->formatInvoiceResponse($invoice);
                $response['partner_id'] = $partnerId;
                
                return $response;
            } else {
                Log::error('Invoice with partner_id failed:', [
                    'status' => $response->status()
                ]);
                return null;
            }
            
        } catch (\Exception $e) {
            Log::error('Invoice with partner_id exception: ' . $e->getMessage());
            return null;
        }
    }
     
    /**
     * CREATE PARTNER
     */
    public function createPartner(array $customerData, string $type = 'customer'): ?int
    {
        // Modify your existing createPartner to accept type parameter
        if ($type === 'supplier') {
            return $this->createSupplierPartner($customerData);
        }

        try {
            $partnerData = $this->preparePartnerData($customerData);
            
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/partners', $partnerData);
            
            if ($response->successful()) {
                $partner = $response->json();
                $partnerId = $partner['id'] ?? null;
                
                Log::info('Partner created:', ['partner_id' => $partnerId]);
                return $partnerId;
            }

            if ($response->status() === 422) {
                $minimalPartnerData = $partnerData;
                $minimalPartnerData['emails'] = [];
                $minimalPartnerData['phone'] = '';
                $minimalPartnerData['taxcode'] = '';

                $retryResponse = Http::withHeaders([
                    'X-API-KEY' => $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->timeout(30)->post($this->baseUrl . '/partners', $minimalPartnerData);

                if ($retryResponse->successful()) {
                    $partner = $retryResponse->json();
                    $partnerId = $partner['id'] ?? null;
                    Log::info('Partner created on retry with minimal payload:', ['partner_id' => $partnerId]);
                    return $partnerId;
                }

                Log::error('Partner creation retry failed:', [
                    'status' => $retryResponse->status(),
                    'body' => $retryResponse->body(),
                    'payload' => $minimalPartnerData,
                ]);
            }
            
            Log::error('Partner creation failed:', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $partnerData,
            ]);
            
            return $this->createDefaultPartner();
            
        } catch (\Exception $e) {
            Log::error('Partner creation exception: ' . $e->getMessage());
            return $this->createDefaultPartner();
        }
    }

    /**
     * Create default partner
     */
    protected function createDefaultPartner(): ?int
    {
        try {
            $defaultPartnerData = [
                'name' => 'Vásárló ' . date('md-His'),
                'address' => [
                    'country_code' => 'HU',
                    'post_code' => '1000',
                    'city' => 'Budapest',
                    'address' => 'Nincs cím megadva',
                ],
                'emails' => [],
                'phone' => '',
                'taxcode' => '',
                'tax_type' => 'individual',
                'payment_days' => 8,
            ];
            
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/partners', $defaultPartnerData);
            
            if ($response->successful()) {
                $partner = $response->json();
                return $partner['id'] ?? null;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Default partner creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add discounts to invoice items
     */
    protected function addDiscounts(array &$invoiceItems, array $saleData, array $originalItems): void
    {
        // Percentage discount
        $discountPercent = floatval($saleData['discount'] ?? 0);
        if ($discountPercent > 0) {
            $totalNet = 0;
            foreach ($originalItems as $item) {
                $totalNet += floatval($item['price_net'] ?? 0) * intval($item['quantity'] ?? 1);
            }
            
            $discountValue = round($totalNet * $discountPercent / 100, 2);
            
            if ($discountValue > 0) {
                $invoiceItems[] = [
                    'name' => 'Ügyfélkedvezmény (' . $discountPercent . '%)',
                    'unit_price' => -$discountValue,
                    'unit_price_type' => 'net',
                    'quantity' => 1,
                    'unit' => 'db',
                    'vat' => '0%',
                    'comment' => '',
                    'entitlement' => 'AAM',
                ];
            }
        }

        // Other discounts
        $discounts = [
            ['key' => 'coupon', 'name' => 'Kupon'],
            ['key' => 'giftcard', 'name' => 'Ajándékkártya'],
            ['key' => 'loyalty', 'name' => 'Hűségpont'],
        ];
        
        foreach ($discounts as $discount) {
            $value = floatval($saleData[$discount['key']] ?? 0);
            if ($value > 0) {
                $invoiceItems[] = [
                    'name' => $discount['name'],
                    'unit_price' => -$value,
                    'unit_price_type' => 'net',
                    'quantity' => 1,
                    'unit' => 'db',
                    'vat' => '0%',
                    'comment' => '',
                    'entitlement' => 'AAM',
                ];
            }
        }
    }

    /**
     * Get VAT string
     */
    protected function getVatString($vatRate): string
    {
        $rawVat = strtoupper(trim((string) $vatRate));
        $validExemptCodes = ['AAM', 'AM', 'TAM', 'ATHK', 'K_AFA', 'EU', 'EUK', 'FAD', 'HO'];
        if (in_array($rawVat, $validExemptCodes, true)) {
            return $rawVat;
        }

        $vatRate = str_replace('%', '', $rawVat);
        
        $validRates = ['0', '5', '18', '27'];
        
        if (in_array($vatRate, $validRates)) {
            return $vatRate . '%';
        }
        
        return '27%';
    }

    /**
     * Map payment method
     */
    protected function mapPaymentMethod(string $method): string
    {
        $map = [
            'cash' => 'cash',
            'card' => 'bankcard',
            'bank_transfer' => 'online_bankcard',
            'credit_card' => 'bankcard',
            'transfer' => 'online_bankcard',
        ];
        return $map[strtolower($method)] ?? 'cash';
    }

    /**
     * Format invoice response
     */
    protected function formatInvoiceResponse(array $invoice): array
    {
        return [
            'id' => $invoice['id'],
            'invoice_number' => $invoice['invoice_number'] ?? '',
            'net_total' => $invoice['net_total'] ?? 0,
            'gross_total' => $invoice['gross_total'] ?? 0,
            'download_url' => $this->getDownloadUrl($invoice['id']),
            'public_url' => $invoice['public_url'] ?? null,
            'fulfillment_date' => $invoice['fulfillment_date'] ?? date('Y-m-d'),
            'due_date' => $invoice['due_date'] ?? date('Y-m-d', strtotime('+8 days')),
        ];
    }

    /**
     * Get PDF download URL
     */
    protected function getDownloadUrl(int $documentId): string
    {
        return $this->baseUrl . '/documents/' . $documentId . '/download';
    }

    /**
     * Get invoice by ID
     */
    public function getInvoice(int $invoiceId): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/documents/' . $invoiceId);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Get invoice failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Download and save Billingo PDF locally
     */
    public function downloadAndSavePdf(int $documentId, int $saleId, string $type): string
    {
        $maxRetries = 5; // Maximum number of retries
        $retryDelay = 2; // Delay between retries in seconds
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info('Attempting to download Billingo PDF', [
                    'attempt' => $attempt,
                    'document_id' => $documentId,
                    'sale_id' => $saleId
                ]);
                
                // Generate filename
                $file_name = 'sale_' . $type . '_' . $saleId . '_' . time() . '_' . $attempt . '.pdf';
                $save_path = public_path("pdf/sales/" . $file_name);
                
                // Ensure directory exists
                $directory = dirname($save_path);
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Download PDF from Billingo
                $response = Http::withHeaders([
                    'X-API-KEY' => $this->apiKey,
                    'Accept' => 'application/pdf',
                ])->timeout(30)->get($this->baseUrl . '/documents/' . $documentId . '/download');
                
                $status = $response->status();
                
                if ($status === 200) {
                    // Save PDF file
                    file_put_contents($save_path, $response->body());
                    
                    // Verify PDF is valid
                    if ($this->isValidPdf($save_path)) {
                        Log::info('Billingo PDF saved successfully', [
                            'document_id' => $documentId,
                            'file_path' => $save_path,
                            'sale_id' => $saleId,
                            'file_size' => filesize($save_path)
                        ]);
                        
                        return asset('pdf/sales/' . $file_name);
                    } else {
                        Log::warning('Invalid PDF downloaded, will retry', [
                            'document_id' => $documentId,
                            'file_path' => $save_path
                        ]);
                        unlink($save_path); // Remove invalid file
                    }
                } 
                elseif ($status === 422) {
                    Log::info('PDF not ready yet, waiting...', [
                        'document_id' => $documentId,
                        'attempt' => $attempt,
                        'max_retries' => $maxRetries
                    ]);
                    
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay); // Wait before retrying
                        continue;
                    } else {
                        throw new \Exception('PDF generation timeout after ' . $maxRetries . ' attempts');
                    }
                }
                else {
                    throw new \Exception('Failed to download PDF. Status: ' . $status . ' - Body: ' . $response->body());
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to download Billingo PDF (attempt ' . $attempt . '): ' . $e->getMessage());
                
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    continue;
                } else {
                    throw new \Exception('Failed to download PDF after ' . $maxRetries . ' attempts: ' . $e->getMessage());
                }
            }
        }
        
        throw new \Exception('Failed to download PDF after all retries');
    }

    /**
     * Check if file is a valid PDF
     */
    private function isValidPdf(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        $fileSize = filesize($filePath);
        if ($fileSize < 100) { // PDF should be at least 100 bytes
            return false;
        }
        
        // Check PDF header
        $handle = fopen($filePath, 'r');
        $header = fread($handle, 5);
        fclose($handle);
        
        return strpos($header, '%PDF-') === 0;
    }
    // End of first BillingoService class
    /**
     * CREATE NEW BILL (Receiving bill/számla bevételezése)
     */
    public function createBill(array $billData, array $items, array $supplierData = null): ?array
    {
        try {
            // 1. Create or get supplier partner
            $partnerId = $this->createPartner($supplierData ?? [], 'supplier');
            
            if (!$partnerId) {
                Log::error('Supplier partner creation failed');
                return null;
            }

            // 2. Format items for bill
            $billItems = [];
            
            foreach ($items as $index => $item) {
                $netPrice = floatval($item['price_net'] ?? 0);
                $quantity = intval($item['quantity'] ?? 1);
                
                $billItems[] = [
                    'name' => $this->cleanString($item['product_name'] ?? 'Termék ' . ($index + 1)),
                    'unit_price' => $netPrice,
                    'unit_price_type' => 'net',
                    'quantity' => $quantity,
                    'unit' => $item['unit'] ?? 'db',
                    'vat' => $this->getVatString($item['vat'] ?? 27),
                    'comment' => $this->cleanString(substr($item['description'] ?? '', 0, 100)),
                    'entitlement' => '',
                ];
            }

            // 3. Prepare bill data (type: 'invoice' for outgoing, 'received' for incoming)
            $billDataPayload = [
                'partner_id' => $partnerId,
                'block_id' => $this->blockId,
                'type' => 'received', // 'received' for incoming bills
                'fulfillment_date' => $billData['fulfillment_date'] ?? date('Y-m-d'),
                'due_date' => $billData['due_date'] ?? date('Y-m-d', strtotime('+30 days')),
                'payment_method' => $this->mapPaymentMethod($billData['payment_method'] ?? 'bank_transfer'),
                'language' => 'hu',
                'currency' => 'HUF',
                'conversion_rate' => 1,
                'electronic_invoice' => $billData['electronic'] ?? false,
                'paid' => $billData['paid'] ?? false,
                'items' => $billItems,
                'comment' => $this->cleanString($billData['comment'] ?? 'Bevételezett számla'),
                'settings' => [
                    'mediated_service' => false,
                    'without_financial_fulfillment' => false,
                ],
            ];

            // 4. API call to create bill
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/documents', $billDataPayload);
            
            if ($response->successful()) {
                $bill = $response->json();
                
                Log::info('Bill created successfully', [
                    'bill_id' => $bill['id'],
                    'bill_number' => $bill['invoice_number'] ?? 'N/A',
                    'type' => 'received'
                ]);
                
                // Save to local database
                $this->saveBillToDatabase($bill, 'received', $supplierData);
                
                return $this->formatBillResponse($bill);
            } else {
                Log::error('Bill creation failed:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
            
        } catch (\Exception $e) {
            Log::error('Bill creation exception: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * DOWNLOAD OUTGOING BILLS (Kimenő számlák)
     */
    public function downloadOutgoingBills(array $filters = []): array
    {
        try {
            $queryParams = http_build_query(array_merge([
                'page' => $filters['page'] ?? 1,
                'per_page' => $filters['per_page'] ?? 25,
                'start_date' => $filters['start_date'] ?? date('Y-m-01'),
                'end_date' => $filters['end_date'] ?? date('Y-m-d'),
                'type' => 'invoice' // Outgoing invoices
            ], $filters));
            
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(60)->get($this->baseUrl . '/documents?' . $queryParams);
            
            if ($response->successful()) {
                $bills = $response->json();
                
                Log::info('Outgoing bills retrieved', [
                    'count' => count($bills['data'] ?? []),
                    'total' => $bills['total'] ?? 0
                ]);
                
                // Process and save each bill
                $processedBills = [];
                foreach (($bills['data'] ?? []) as $bill) {
                    $this->saveBillToDatabase($bill, 'outgoing');
                    $processedBills[] = $this->formatBillResponse($bill);
                }
                
                return [
                    'bills' => $processedBills,
                    'pagination' => [
                        'total' => $bills['total'] ?? 0,
                        'per_page' => $bills['per_page'] ?? 25,
                        'current_page' => $bills['current_page'] ?? 1,
                        'last_page' => $bills['last_page'] ?? 1,
                    ]
                ];
            } else {
                Log::error('Failed to download outgoing bills:', [
                    'status' => $response->status()
                ]);
                return [];
            }
            
        } catch (\Exception $e) {
            Log::error('Download outgoing bills exception: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * DOWNLOAD INCOMING BILLS (Bejövő számlák)
     */
    public function downloadIncomingBills(array $filters = []): array
    {
        try {
            $queryParams = http_build_query(array_merge([
                'page' => $filters['page'] ?? 1,
                'per_page' => $filters['per_page'] ?? 25,
                'start_date' => $filters['start_date'] ?? date('Y-m-01'),
                'end_date' => $filters['end_date'] ?? date('Y-m-d'),
                'type' => 'received' // Incoming bills
            ], $filters));
            
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(60)->get($this->baseUrl . '/documents?' . $queryParams);
            
            if ($response->successful()) {
                $bills = $response->json();
                
                Log::info('Incoming bills retrieved', [
                    'count' => count($bills['data'] ?? []),
                    'total' => $bills['total'] ?? 0
                ]);
                
                // Process and save each bill
                $processedBills = [];
                foreach (($bills['data'] ?? []) as $bill) {
                    $this->saveBillToDatabase($bill, 'incoming');
                    $processedBills[] = $this->formatBillResponse($bill);
                }
                
                return [
                    'bills' => $processedBills,
                    'pagination' => $bills['pagination'] ?? []
                ];
            } else {
                Log::error('Failed to download incoming bills:', [
                    'status' => $response->status()
                ]);
                return [];
            }
            
        } catch (\Exception $e) {
            Log::error('Download incoming bills exception: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * SAVE BILL TO LOCAL DATABASE
     */
    protected function saveBillToDatabase(array $billData, string $type, array $additionalData = []): bool
    {
        try {
            DB::table('billingo_bills')->updateOrInsert(
                ['billingo_id' => $billData['id']],
                [
                    'bill_number' => $billData['invoice_number'] ?? null,
                    'type' => $type,
                    'partner_id' => $billData['partner']['id'] ?? null,
                    'partner_name' => $billData['partner']['name'] ?? '',
                    'net_total' => $billData['net_total'] ?? 0,
                    'gross_total' => $billData['gross_total'] ?? 0,
                    'currency' => $billData['currency'] ?? 'HUF',
                    'fulfillment_date' => $billData['fulfillment_date'] ?? null,
                    'due_date' => $billData['due_date'] ?? null,
                    'payment_status' => $billData['payment_status'] ?? 'not_paid',
                    'payment_method' => $billData['payment_method'] ?? '',
                    'electronic' => $billData['electronic'] ?? false,
                    'paid' => $billData['paid'] ?? false,
                    'comment' => $billData['comment'] ?? '',
                    'billingo_data' => json_encode($billData),
                    'additional_data' => json_encode($additionalData),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            
            Log::info('Bill saved to database', [
                'billingo_id' => $billData['id'],
                'type' => $type,
                'bill_number' => $billData['invoice_number'] ?? 'N/A'
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to save bill to database: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * FORMAT BILL RESPONSE
     */
    protected function formatBillResponse(array $bill): array
    {
        return [
            'id' => $bill['id'],
            'bill_number' => $bill['invoice_number'] ?? '',
            'type' => $bill['type'] ?? '',
            'partner' => $bill['partner'] ?? [],
            'organization' => $bill['organization'] ?? [],
            'net_total' => $bill['net_total'] ?? 0,
            'gross_total' => $bill['gross_total'] ?? 0,
            'currency' => $bill['currency'] ?? 'HUF',
            'fulfillment_date' => $bill['fulfillment_date'] ?? '',
            'due_date' => $bill['due_date'] ?? '',
            'payment_status' => $bill['payment_status'] ?? 'not_paid',
            'payment_method' => $bill['payment_method'] ?? '',
            'electronic' => $bill['electronic'] ?? false,
            'paid' => $bill['paid'] ?? false,
            'comment' => $bill['comment'] ?? '',
            'download_url' => $this->getDownloadUrl($bill['id']),
            'public_url' => $bill['public_url'] ?? null,
            'created_at' => $bill['created_at'] ?? now()->toDateTimeString(),
        ];
    }
    
    /**
     * GET BILL BY ID
     */
    public function getBill(int $billId): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/documents/' . $billId);
            
            if ($response->successful()) {
                $bill = $response->json();
                
                // Save to database
                $type = $bill['type'] === 'received' ? 'incoming' : 'outgoing';
                $this->saveBillToDatabase($bill, $type);
                
                return $this->formatBillResponse($bill);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Get bill failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * UPDATE BILL PAYMENT STATUS
     */
    public function updateBillPaymentStatus(int $billId, bool $paid, string $paidDate = null): bool
    {
        try {
            $updateData = [
                'paid' => $paid,
            ];
            
            if ($paid && $paidDate) {
                $updateData['paid_date'] = $paidDate;
            }
            
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->put($this->baseUrl . '/documents/' . $billId, $updateData);
            
            if ($response->successful()) {
                Log::info('Bill payment status updated', [
                    'bill_id' => $billId,
                    'paid' => $paid
                ]);
                
                // Update local database
                DB::table('billingo_bills')
                    ->where('billingo_id', $billId)
                    ->update([
                        'payment_status' => $paid ? 'paid' : 'not_paid',
                        'paid' => $paid,
                        'updated_at' => now(),
                    ]);
                
                return true;
            } else {
                Log::error('Failed to update bill payment status:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('Update bill payment status exception: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * CREATE SUPPLIER PARTNER (different from customer partner)
     */
    public function createSupplierPartner(array $supplierData): ?int
    {
        try {
            $partnerData = [
                'name' => $this->cleanString($supplierData['name'] ?? 'Szolgáltató ' . date('md-His')),
                'address' => [
                    'country_code' => 'HU',
                    'post_code' => $supplierData['zip_code'] ?? '1000',
                    'city' => $this->cleanString($supplierData['city'] ?? 'Budapest'),
                    'address' => $this->cleanString($supplierData['address'] ?? ''),
                ],
                'emails' => !empty($supplierData['email']) ? 
                           [$this->cleanString($supplierData['email'])] : [],
                'phone' => $supplierData['phone'] ?? '',
                'taxcode' => $supplierData['tax_number'] ?? '',
                'tax_type' => 'business', // Usually business for suppliers
                'payment_days' => 30, // Longer payment terms for suppliers
                'custom_billing_settings' => [
                    'payment_method' => 'bank_transfer',
                ],
            ];
            
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/partners', $partnerData);
            
            if ($response->successful()) {
                $partner = $response->json();
                $partnerId = $partner['id'] ?? null;
                
                Log::info('Supplier partner created:', ['partner_id' => $partnerId]);
                return $partnerId;
            }
            
            Log::error('Supplier partner creation failed:', [
                'status' => $response->status()
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Supplier partner creation exception: ' . $e->getMessage());
            return null;
        }
    }
    
   
}