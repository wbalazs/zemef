<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Billingo\BillingoService;
use App\Models\BillingoBill;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncBillingoBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billingo:sync 
                            {--type=all : Sync type (all, outgoing, incoming, unpaid, overdue)}
                            {--date= : Date range in format YYYY-MM or YYYY-MM-DD}
                            {--start-date= : Start date for sync (YYYY-MM-DD)}
                            {--end-date= : End date for sync (YYYY-MM-DD)}
                            {--force : Force re-download of existing bills}
                            {--download-pdf : Download PDFs for synced bills}
                            {--limit=100 : Maximum number of bills to sync per type}
                            {--days=30 : Sync bills from last X days}
                            {--update-status : Only update payment status of existing bills}
                            {--skip-errors : Continue even if some bills fail to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Billingo bills (incoming and outgoing) with local database';

    /**
     * Billingo service instance
     */
    protected BillingoService $billingoService;

    /**
     * Statistics array
     */
    protected array $stats = [
        'outgoing_created' => 0,
        'outgoing_updated' => 0,
        'incoming_created' => 0,
        'incoming_updated' => 0,
        'errors' => 0,
        'pdf_downloaded' => 0,
        'pdf_failed' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->billingoService = app(BillingoService::class);
        
        $this->info('Starting Billingo bill synchronization...');
        $this->info('===============================');
        
        $startTime = microtime(true);
        
        try {
            $this->validateOptions();
            
            if ($this->option('update-status')) {
                $this->updatePaymentStatusOnly();
            } else {
                $this->syncBills();
            }
            
            $this->displayStatistics($startTime);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Sync failed: ' . $e->getMessage());
            Log::error('Billingo sync failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return Command::FAILURE;
        }
    }
    
    /**
     * Validate command options
     */
    protected function validateOptions(): void
    {
        $date = $this->option('date');
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');
        
        if ($date && ($startDate || $endDate)) {
            throw new \InvalidArgumentException('Cannot use --date with --start-date or --end-date');
        }
        
        if ($startDate && !$this->validateDate($startDate)) {
            throw new \InvalidArgumentException('Invalid start date format. Use YYYY-MM-DD');
        }
        
        if ($endDate && !$this->validateDate($endDate)) {
            throw new \InvalidArgumentException('Invalid end date format. Use YYYY-MM-DD');
        }
        
        if ($date && !$this->validateDate($date) && !$this->validateMonth($date)) {
            throw new \InvalidArgumentException('Invalid date format. Use YYYY-MM or YYYY-MM-DD');
        }
    }
    
    /**
     * Main sync method
     */
    protected function syncBills(): void
    {
        $syncType = $this->option('type');
        $filters = $this->prepareFilters();
        
        $this->info("Sync filters:");
        $this->table(
            ['Filter', 'Value'],
            [
                ['Type', $syncType],
                ['Start Date', $filters['start_date']],
                ['End Date', $filters['end_date']],
                ['Limit', $this->option('limit')],
                ['Force', $this->option('force') ? 'Yes' : 'No'],
                ['Download PDF', $this->option('download-pdf') ? 'Yes' : 'No'],
            ]
        );
        
        $this->newLine();
        
        if ($syncType === 'all' || $syncType === 'outgoing') {
            $this->syncOutgoingBills($filters);
        }
        
        if ($syncType === 'all' || $syncType === 'incoming') {
            $this->syncIncomingBills($filters);
        }
        
        if ($syncType === 'unpaid') {
            $this->syncUnpaidBills($filters);
        }
        
        if ($syncType === 'overdue') {
            $this->syncOverdueBills($filters);
        }
        
        // Download PDFs if requested
        if ($this->option('download-pdf')) {
            $this->downloadMissingPdfs();
        }
    }
    
    /**
     * Sync outgoing bills
     */
    protected function syncOutgoingBills(array $filters): void
    {
        $this->info('Syncing outgoing bills...');
        
        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Fetching bills...');
        
        try {
            $outgoingBills = $this->billingoService->downloadOutgoingBills($filters);
            
            if (empty($outgoingBills['bills'])) {
                $this->info('No outgoing bills found for the given filters.');
                return;
            }
            
            $progressBar->setMaxSteps(count($outgoingBills['bills']));
            
            foreach ($outgoingBills['bills'] as $bill) {
                $progressBar->setMessage("Processing: {$bill['bill_number']}");
                
                try {
                    $result = $this->processBill($bill, 'outgoing');
                    
                    if ($result === 'created') {
                        $this->stats['outgoing_created']++;
                        $this->comment(" ✓ Created: {$bill['bill_number']}");
                    } elseif ($result === 'updated') {
                        $this->stats['outgoing_updated']++;
                        $this->line(" ↻ Updated: {$bill['bill_number']}");
                    }
                    
                } catch (\Exception $e) {
                    $this->stats['errors']++;
                    $this->error(" ✗ Error: {$bill['bill_number']} - " . $e->getMessage());
                    
                    if (!$this->option('skip-errors')) {
                        throw $e;
                    }
                }
                
                $progressBar->advance();
            }
            
            $progressBar->setMessage('Complete!');
            $progressBar->finish();
            $this->newLine();
            
        } catch (\Exception $e) {
            if (isset($progressBar)) {
                $progressBar->finish();
            }
            throw $e;
        }
    }
    
    /**
     * Sync incoming bills
     */
    protected function syncIncomingBills(array $filters): void
    {
        $this->info('Syncing incoming bills...');
        
        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $progressBar->setMessage('Fetching bills...');
        
        try {
            $incomingBills = $this->billingoService->downloadIncomingBills($filters);
            
            if (empty($incomingBills['bills'])) {
                $this->info('No incoming bills found for the given filters.');
                return;
            }
            
            $progressBar->setMaxSteps(count($incomingBills['bills']));
            
            foreach ($incomingBills['bills'] as $bill) {
                $progressBar->setMessage("Processing: {$bill['bill_number']}");
                
                try {
                    $result = $this->processBill($bill, 'incoming');
                    
                    if ($result === 'created') {
                        $this->stats['incoming_created']++;
                        $this->comment(" ✓ Created: {$bill['bill_number']}");
                    } elseif ($result === 'updated') {
                        $this->stats['incoming_updated']++;
                        $this->line(" ↻ Updated: {$bill['bill_number']}");
                    }
                    
                } catch (\Exception $e) {
                    $this->stats['errors']++;
                    $this->error(" ✗ Error: {$bill['bill_number']} - " . $e->getMessage());
                    
                    if (!$this->option('skip-errors')) {
                        throw $e;
                    }
                }
                
                $progressBar->advance();
            }
            
            $progressBar->setMessage('Complete!');
            $progressBar->finish();
            $this->newLine();
            
        } catch (\Exception $e) {
            if (isset($progressBar)) {
                $progressBar->finish();
            }
            throw $e;
        }
    }
    
    /**
     * Sync unpaid bills
     */
    protected function syncUnpaidBills(array $filters): void
    {
        $this->info('Syncing unpaid bills...');
        
        // First get all unpaid bills from local database
        $unpaidBills = BillingoBill::where('paid', false)
            ->whereNotNull('billingo_id')
            ->where('created_at', '>=', $filters['start_date'])
            ->limit($this->option('limit'))
            ->get();
        
        if ($unpaidBills->isEmpty()) {
            $this->info('No unpaid bills found in local database.');
            return;
        }
        
        $progressBar = $this->output->createProgressBar(count($unpaidBills));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        
        foreach ($unpaidBills as $bill) {
            $progressBar->setMessage("Updating: {$bill->bill_number}");
            
            try {
                // Fetch fresh data from Billingo
                $freshBill = $this->billingoService->getBill($bill->billingo_id);
                
                if ($freshBill) {
                    $bill->update([
                        'payment_status' => $freshBill['payment_status'] ?? $bill->payment_status,
                        'paid' => $freshBill['paid'] ?? $bill->paid,
                        'paid_date' => $freshBill['paid_date'] ?? $bill->paid_date,
                        'billingo_data' => $freshBill,
                        'updated_at' => now(),
                    ]);
                    
                    $this->stats['outgoing_updated']++;
                    $this->line(" ↻ Updated: {$bill->bill_number}");
                }
                
            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->error(" ✗ Error: {$bill->bill_number} - " . $e->getMessage());
                
                if (!$this->option('skip-errors')) {
                    throw $e;
                }
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
    }
    
    /**
     * Sync overdue bills
     */
    protected function syncOverdueBills(array $filters): void
    {
        $this->info('Syncing overdue bills...');
        
        $overdueBills = BillingoBill::where('due_date', '<', now())
            ->where('paid', false)
            ->where('payment_status', '!=', 'paid')
            ->where('created_at', '>=', $filters['start_date'])
            ->limit($this->option('limit'))
            ->get();
        
        if ($overdueBills->isEmpty()) {
            $this->info('No overdue bills found in local database.');
            return;
        }
        
        $this->table(
            ['Bill Number', 'Due Date', 'Amount', 'Partner', 'Days Overdue'],
            $overdueBills->map(function ($bill) {
                $daysOverdue = now()->diffInDays(Carbon::parse($bill->due_date));
                return [
                    $bill->bill_number,
                    $bill->due_date,
                    number_format($bill->gross_total, 0, ',', ' ') . ' ' . $bill->currency,
                    $bill->partner_name,
                    $daysOverdue,
                ];
            })
        );
        
        if ($this->confirm('Do you want to update these overdue bills from Billingo?', true)) {
            $progressBar = $this->output->createProgressBar(count($overdueBills));
            
            foreach ($overdueBills as $bill) {
                try {
                    $freshBill = $this->billingoService->getBill($bill->billingo_id);
                    
                    if ($freshBill) {
                        $bill->update([
                            'payment_status' => $freshBill['payment_status'] ?? $bill->payment_status,
                            'paid' => $freshBill['paid'] ?? $bill->paid,
                            'billingo_data' => $freshBill,
                            'updated_at' => now(),
                        ]);
                        
                        $this->stats['outgoing_updated']++;
                    }
                    
                } catch (\Exception $e) {
                    $this->stats['errors']++;
                    $this->error("Error updating {$bill->bill_number}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine();
        }
    }
    
    /**
     * Process individual bill
     */
    protected function processBill(array $billData, string $type): string
    {
        $billingoId = $billData['id'];
        $forceUpdate = $this->option('force');
        
        // Check if bill already exists
        $existingBill = BillingoBill::where('billingo_id', $billingoId)->first();
        
        if ($existingBill) {
            if (!$forceUpdate && $existingBill->updated_at > now()->subHour()) {
                return 'skipped'; // Recently updated, skip
            }
            
            // Update existing bill
            $existingBill->update([
                'bill_number' => $billData['bill_number'] ?? $existingBill->bill_number,
                'payment_status' => $billData['payment_status'] ?? $existingBill->payment_status,
                'paid' => $billData['paid'] ?? $existingBill->paid,
                'paid_date' => $billData['paid_date'] ?? $existingBill->paid_date,
                'billingo_data' => $billData,
                'updated_at' => now(),
            ]);
            
            return 'updated';
        }
        
        // Create new bill
        BillingoBill::create([
            'billingo_id' => $billingoId,
            'bill_number' => $billData['bill_number'] ?? null,
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
            'paid_date' => $billData['paid_date'] ?? null,
            'comment' => $billData['comment'] ?? '',
            'billingo_data' => $billData,
            'public_url' => $billData['public_url'] ?? null,
            'created_at' => $billData['created_at'] ?? now(),
        ]);
        
        return 'created';
    }
    
    /**
     * Download missing PDFs
     */
    protected function downloadMissingPdfs(): void
    {
        $this->info('Downloading missing PDFs...');
        
        $billsWithoutPdf = BillingoBill::whereNull('pdf_path')
            ->where('created_at', '>=', now()->subDays($this->option('days')))
            ->limit(50) // Limit to prevent timeout
            ->get();
        
        if ($billsWithoutPdf->isEmpty()) {
            $this->info('No bills without PDF found.');
            return;
        }
        
        $progressBar = $this->output->createProgressBar(count($billsWithoutPdf));
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        
        foreach ($billsWithoutPdf as $bill) {
            $progressBar->setMessage("Downloading: {$bill->bill_number}");
            
            try {
                if ($bill->downloadPdf()) {
                    $this->stats['pdf_downloaded']++;
                    $this->comment(" ✓ PDF: {$bill->bill_number}");
                } else {
                    $this->stats['pdf_failed']++;
                    $this->error(" ✗ PDF failed: {$bill->bill_number}");
                }
            } catch (\Exception $e) {
                $this->stats['pdf_failed']++;
                $this->error(" ✗ PDF error: {$bill->bill_number} - " . $e->getMessage());
                
                if (!$this->option('skip-errors')) {
                    throw $e;
                }
            }
            
            $progressBar->advance();
            sleep(1); // Rate limiting
        }
        
        $progressBar->finish();
        $this->newLine();
    }
    
    /**
     * Update payment status only
     */
    protected function updatePaymentStatusOnly(): void
    {
        $this->info('Updating payment status only...');
        
        $billsToUpdate = BillingoBill::where('paid', false)
            ->whereNotNull('billingo_id')
            ->limit($this->option('limit'))
            ->get();
        
        $progressBar = $this->output->createProgressBar(count($billsToUpdate));
        
        foreach ($billsToUpdate as $bill) {
            try {
                $freshBill = $this->billingoService->getBill($bill->billingo_id);
                
                if ($freshBill) {
                    $oldStatus = $bill->payment_status;
                    $newStatus = $freshBill['payment_status'] ?? $oldStatus;
                    
                    if ($oldStatus !== $newStatus) {
                        $bill->update([
                            'payment_status' => $newStatus,
                            'paid' => $freshBill['paid'] ?? $bill->paid,
                            'paid_date' => $freshBill['paid_date'] ?? $bill->paid_date,
                            'updated_at' => now(),
                        ]);
                        
                        $this->info("Status updated: {$bill->bill_number} ({$oldStatus} → {$newStatus})");
                        $this->stats['outgoing_updated']++;
                    }
                }
            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->error("Error: {$bill->bill_number} - " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
    }
    
    /**
     * Prepare filters for API calls
     */
    protected function prepareFilters(): array
    {
        $filters = [
            'per_page' => $this->option('limit'),
        ];
        
        // Handle date options
        if ($this->option('date')) {
            $date = $this->option('date');
            
            if (strlen($date) === 7) { // YYYY-MM format
                $filters['start_date'] = $date . '-01';
                $filters['end_date'] = Carbon::parse($filters['start_date'])->endOfMonth()->format('Y-m-d');
            } else { // YYYY-MM-DD format
                $filters['start_date'] = $date;
                $filters['end_date'] = $date;
            }
        } elseif ($this->option('start-date') || $this->option('end-date')) {
            $filters['start_date'] = $this->option('start-date') ?? now()->subDays($this->option('days'))->format('Y-m-d');
            $filters['end_date'] = $this->option('end-date') ?? now()->format('Y-m-d');
        } else {
            $filters['start_date'] = now()->subDays($this->option('days'))->format('Y-m-d');
            $filters['end_date'] = now()->format('Y-m-d');
        }
        
        return $filters;
    }
    
    /**
     * Display sync statistics
     */
    protected function displayStatistics(float $startTime): void
    {
        $executionTime = round(microtime(true) - $startTime, 2);
        
        $this->info('Synchronization Complete!');
        $this->info('========================');
        
        $this->table(
            ['Statistic', 'Count'],
            [
                ['Outgoing Bills Created', $this->stats['outgoing_created']],
                ['Outgoing Bills Updated', $this->stats['outgoing_updated']],
                ['Incoming Bills Created', $this->stats['incoming_created']],
                ['Incoming Bills Updated', $this->stats['incoming_updated']],
                ['PDFs Downloaded', $this->stats['pdf_downloaded']],
                ['PDFs Failed', $this->stats['pdf_failed']],
                ['Errors', $this->stats['errors']],
                ['Execution Time', $executionTime . 's'],
            ]
        );
        
        // Log statistics
        Log::info('Billingo sync completed', array_merge($this->stats, [
            'execution_time' => $executionTime,
            'options' => $this->options(),
        ]));
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     */
    protected function validateDate(string $date): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
    }
    
    /**
     * Validate month format (YYYY-MM)
     */
    protected function validateMonth(string $month): bool
    {
        return (bool) preg_match('/^\d{4}-\d{2}$/', $month);
    }
}