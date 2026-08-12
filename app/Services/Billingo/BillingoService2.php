<?php

namespace App\Services\Billingo;

use Billingo\API\Connector\HTTP\Request;
use Billingo\API\Connector\Exceptions\BillingoClientException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class BillingoService
{
    protected $client;
    protected $partnerId;

    public function __construct()
    {
        $apiKey = env('BILLINGO_API_KEY', '23b1bc5a-f6d5-11f0-859c-06e1fe7801c9');
        
        // Inicializálás az SDK-val
        $this->client = new Request([
            'public_key' => $apiKey,
            'private_key' => $apiKey, // Ha szükséges, külön private key
        ]);
        
        // Válaszd ki a számlakibocsátó céged ID-ját (egy példa lekérdezéssel)
        $this->partnerId = $this->getMyOrganizationId();
    }

    /**
     * Számla létrehozása Billingo-ban
     */
    public function createInvoice(array $saleData, array $items, array $customerData = null): ?array
    {
        try {
            // 1. Ellenőrizd/hozd létre a partnert (ügyfelet)
            $partnerId = $this->findOrCreatePartner($saleData, $customerData);
            
            // 2. Készítsd elő a számla adatait
            $invoiceData = $this->prepareInvoiceData($partnerId, $saleData, $items);
            
            // 3. Számla létrehozása
            $response = $this->client->post('documents', $invoiceData);
            
            if ($response && isset($response['id'])) {
                return [
                    'id' => $response['id'],
                    'invoice_number' => $response['invoice_number'] ?? '',
                    'net_total' => $response['net_total'] ?? 0,
                    'gross_total' => $response['gross_total'] ?? 0,
                    'download_url' => $this->getDownloadUrl($response['id']),
                    'public_url' => $response['public_url'] ?? null,
                    'fulfillment_date' => $response['fulfillment_date'] ?? null,
                ];
            }
            
            return null;
            
        } catch (BillingoClientException $e) {
            Log::error('Billingo API error: ' . $e->getMessage(), [
                'sale_data' => $saleData,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('General error creating Billingo invoice: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Partner keresése vagy létrehozása
     */
    protected function findOrCreatePartner(array $saleData, ?array $customerData = null): int
    {
        // Ha van customer_id, próbáld megkeresni
        if (!empty($saleData['customer_id'])) {
            try {
                $existingPartner = $this->client->get('partners/' . $saleData['customer_id']);
                if ($existingPartner) {
                    return $saleData['customer_id'];
                }
            } catch (\Exception $e) {
                // Partner nem található, új partnert kell létrehozni
            }
        }
        
        // Új partner létrehozása
        $partnerData = [
            'name' => $saleData['customer_name'] ?? ($customerData['name'] ?? 'Vásárló'),
            'address' => [
                'country_code' => 'HU',
                'post_code' => $customerData['zip_code'] ?? '1000',
                'city' => $customerData['city'] ?? 'Budapest',
                'address' => $saleData['customer_address'] ?? ($customerData['address'] ?? ''),
            ],
            'emails' => [$customerData['email'] ?? ''],
            'taxcode' => $customerData['tax_number'] ?? '',
            'iban' => $customerData['iban'] ?? '',
            'swift' => $customerData['swift'] ?? '',
            'account_number' => $customerData['account_number'] ?? '',
            'phone' => $customerData['phone'] ?? '',
            'general_ledger_number' => $customerData['ledger_number'] ?? '',
            'tax_type' => $customerData['tax_type'] ?? 'individual', // 'individual', 'business', 'non_taxpayer'
        ];
        
        $partner = $this->client->post('partners', $partnerData);
        return $partner['id'];
    }

    /**
     * Számlaadatok előkészítése
     */
    protected function prepareInvoiceData(int $partnerId, array $saleData, array $items): array
    {
        // Termékek formázása
        $invoiceItems = $this->prepareInvoiceItems($items);
        
        // Fizetési mód leképezése
        $paymentMethod = $this->mapPaymentMethod($saleData['payment_method'] ?? 'cash');
        
        // Bizonylat típus meghatározása
        $invoiceType = $this->determineInvoiceType($saleData['sale_type'] ?? 'normal');
        
        // Kedvezmények kezelése
        $discountData = $this->calculateDiscounts($saleData, $invoiceItems);
        
        return [
            'partner_id' => $partnerId,
            'block_id' => 1, // Számla típus blokk ID (1: Normál számla)
            'bank_account_id' => 1, // Bankszámla ID (állítsd be a te bankszámládra)
            'type' => $invoiceType, // 'invoice', 'advance', 'proforma'
            'fulfillment_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'payment_method' => $paymentMethod,
            'language' => 'hu',
            'currency' => 'HUF',
            'conversion_rate' => 1,
            'electronic_invoice' => false,
            'paid' => false,
            'items' => $discountData['items'],
            'comment' => $saleData['comment'] ?? '',
            'settings' => [
                'mediated_service' => false,
                'without_financial_fulfillment' => false,
                'online_payment' => null,
            ],
            'advance_invoice' => [], // Előlegszámla esetén
            'discount' => $discountData['global_discount'],
        ];
    }

    /**
     * Terméksorok előkészítése
     */
    protected function prepareInvoiceItems(array $items): array
    {
        $invoiceItems = [];
        
        foreach ($items as $item) {
            $netPrice = floatval($item['price_net'] ?? 0);
            $grossPrice = floatval($item['price_gross'] ?? 0);
            $quantity = intval($item['quantity'] ?? 1);
            $vat = intval($item['vat'] ?? 27);
            
            $invoiceItems[] = [
                'product_id' => $item['product_id'] ?? null,
                'name' => $item['product_name'] ?? 'Termék',
                'unit_price' => $netPrice,
                'unit_price_type' => 'net',
                'quantity' => $quantity,
                'unit' => 'db',
                'vat' => (string)$vat, // Billingo formátum: "27"
                'comment' => $item['description'] ?? '',
                'entitlement' => 'AAM', // Alanyi adómentesség: "AAM", egyébként üres
            ];
        }
        
        return $invoiceItems;
    }

    /**
     * Kedvezmények kiszámítása és alkalmazása
     */
    protected function calculateDiscounts(array $saleData, array &$items): array
    {
        $globalDiscount = null;
        
        // Százalékos kedvezmény
        if (!empty($saleData['discount']) && floatval($saleData['discount']) > 0) {
            $globalDiscount = [
                'type' => 'percentage',
                'value' => floatval($saleData['discount']),
            ];
        }
        
        // Fix összegű kedvezmények (kupon, ajándékkártya, hűségpont)
        $fixedDiscounts = [
            'coupon' => $saleData['coupon'] ?? 0,
            'giftcard' => $saleData['giftcard'] ?? 0,
            'loyalty' => $saleData['loyalty'] ?? 0,
        ];
        
        $totalFixedDiscount = array_sum($fixedDiscounts);
        
        if ($totalFixedDiscount > 0) {
            // Fix összegű kedvezmény hozzáadása külön sorban
            $items[] = [
                'name' => 'Kedvezmények',
                'unit_price' => -$totalFixedDiscount,
                'unit_price_type' => 'net',
                'quantity' => 1,
                'unit' => 'db',
                'vat' => '0', // Kedvezmény általában 0% ÁFA
                'comment' => 'Kupon: ' . ($fixedDiscounts['coupon'] ?? 0) . ' Ft, ' .
                           'Ajándékkártya: ' . ($fixedDiscounts['giftcard'] ?? 0) . ' Ft, ' .
                           'Hűségpont: ' . ($fixedDiscounts['loyalty'] ?? 0) . ' Ft',
                'entitlement' => '',
            ];
        }
        
        return [
            'items' => $items,
            'global_discount' => $globalDiscount,
        ];
    }

    /**
     * PDF letöltési URL előállítása
     */
    protected function getDownloadUrl(int $documentId): string
    {
        return "https://api.billingo.hu/v3/documents/{$documentId}/download";
    }

    /**
     * PDF bináris tartalmának letöltése
     */
    public function downloadInvoicePdf(int $documentId): ?string
    {
        try {
            $response = $this->client->get("documents/{$documentId}/download");
            return $response ?: null;
        } catch (\Exception $e) {
            Log::error('PDF download failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Szervezet (saját cég) ID-jának lekérése
     */
    protected function getMyOrganizationId(): int
    {
        try {
            $organizations = $this->client->get('organization');
            return $organizations[0]['id'] ?? 1;
        } catch (\Exception $e) {
            Log::warning('Could not fetch organization ID, using default: ' . $e->getMessage());
            return 1; // Alapértelmezett ID
        }
    }

    /**
     * Fizetési mód leképezése
     */
    protected function mapPaymentMethod(string $method): string
    {
        $map = [
            'cash' => 'cash',
            'card' => 'credit_card',
            'bank_transfer' => 'bank_transfer',
            'cash_on_delivery' => 'cash_on_delivery',
            'paypal' => 'paypal',
        ];
        
        return $map[strtolower($method)] ?? 'cash';
    }

    /**
     * Bizonylat típus meghatározása
     */
    protected function determineInvoiceType(string $saleType): string
    {
        $map = [
            'normal' => 'invoice',
            'proforma' => 'proforma',
            'advance' => 'advance',
            'booking' => 'invoice', // Foglalás is normál számlaként
        ];
        
        return $map[strtolower($saleType)] ?? 'invoice';
    }
}