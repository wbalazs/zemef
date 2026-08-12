<?php

namespace App\Services;

use App\Models\CommissionCampaign;
use App\Models\CommissionRule;
use App\Models\EmployeeCommission;
use App\Models\Business;
use App\Models\Employee;
use App\Models\TempSale;
use App\Models\PurchaseTicket;
use App\Models\ItemList;
use App\Models\WebshopOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionCalculator
{
    /**
     * Számolt jutalékokat egy adott időszakra
     */
    public function calculateForPeriod($startDate, $endDate, $businessId = null)
{
    $campaigns = CommissionCampaign::where('status', 'active')
        ->where('start_date', '<=', $endDate)
        ->where('end_date', '>=', $startDate)
        ->when($businessId, function ($query) use ($businessId) {
            $query->whereHas('businesses', function ($q) use ($businessId) {
                // Specify the table name to avoid ambiguity
                $q->where('commission_campaign_business.business_id', $businessId);
            });
        })
        ->get();

    foreach ($campaigns as $campaign) {
        $this->calculateCampaignCommissions($campaign, $startDate, $endDate);
    }
    
    return count($campaigns);
}

    /**
     * Kampány jutalékok számolása
     */
    protected function calculateCampaignCommissions($campaign, $startDate, $endDate)
    {
        $businessIds = $campaign->businesses->pluck('business_id')->toArray();
        
        if (empty($businessIds)) {
            Log::warning('Campaign has no businesses assigned', ['campaign_id' => $campaign->id]);
            return 0;
        }
        
        $totalCommissions = 0;
        
        switch ($campaign->campaign_type) {
            case 'sales':
                $totalCommissions += $this->calculateSalesCommissions($campaign, $businessIds, $startDate, $endDate);
                break;
            case 'gold_purchase':
                $totalCommissions += $this->calculateGoldPurchaseCommissions($campaign, $businessIds, $startDate, $endDate);
                break;
            case 'expired_ticket':
                $totalCommissions += $this->calculateExpiredTicketCommissions($campaign, $businessIds, $startDate, $endDate);
                break;
            case 'pawn':
                $totalCommissions += $this->calculatePawnCommissions($campaign, $businessIds, $startDate, $endDate);
                break;
            case 'other':
                $totalCommissions += $this->calculateOtherCommissions($campaign, $businessIds, $startDate, $endDate);
                break;
        }
        
        return $totalCommissions;
    }

    /**
     * Eladások utáni jutalék számolás - TOVÁBBFEJLESZTETT VERZIÓ
     */
    protected function calculateSalesCommissions($campaign, $businessIds, $startDate, $endDate)
{
    // Alap jutalék szabályok
    $basicRules = $campaign->commissionRules()
        ->where('rule_type', 'basic')
        ->orderBy('priority')
        ->get();

    // Cél jutalék szabályok
    $targetRules = $campaign->commissionRules()
        ->where('rule_type', 'target')
        ->orderBy('threshold_min')
        ->get();

    foreach ($businessIds as $businessId) {
        // Bolt szintű teljesítmény
        $businessPerformance = $this->getBusinessPerformance($businessId, $startDate, $endDate);
        
        // Alkalmazottak teljesítménye - FIXED with correct pivot table and column names
        $employees = Employee::whereHas('businesses', function ($q) use ($businessId) {
            $q->where('business_employee.business_business_id', $businessId);
        })->get();

        foreach ($employees as $employee) {
            $employeePerformance = $this->getEmployeePerformance($employee->employee_id, $businessId, $startDate, $endDate);
            
            // Alap jutalék számolás
            $this->calculateBasicCommission(
                $campaign,
                $employee,
                $businessId,
                $employeePerformance,
                $basicRules
            );

            // Cél jutalék számolás (ha elérte a célt)
            $this->calculateTargetCommission(
                $campaign,
                $employee,
                $businessId,
                $employeePerformance,
                $targetRules
            );
        }
    }
}
    protected function calculateTargetCommission($campaign, $employee, $businessId, $performance, $rules)
    {
        foreach ($rules as $rule) {
            if ($performance >= $rule->threshold_min) {
                // Csak a threshold feletti részre számoljuk
                $excessAmount = $performance - $rule->threshold_min;
                $commissionAmount = $excessAmount * ($rule->commission_value / 100);
                
                if ($commissionAmount > 0) {
                    EmployeeCommission::create([
                        'employee_id' => $employee->employee_id, // Use employee_id, not id
                        'business_id' => $businessId,
                        'commission_campaign_id' => $campaign->id,
                        'commission_rule_id' => $rule->id,
                        'source_type' => 'sales',
                        'amount' => $commissionAmount,
                        'base_amount' => $excessAmount,
                        'commission_date' => now(),
                        'status' => 'calculated'
                    ]);
                }
            }
        }
    }
    /**
     * Törtarany felvásárlás jutalék számolás - TOVÁBBFEJLESZTETT VERZIÓ
     */
    protected function calculateGoldPurchaseCommissions($campaign, $businessIds, $startDate, $endDate)
    {
        $totalCommissions = 0;
        
        $purchaseTickets = PurchaseTicket::with('items')
            ->whereIn('business_id', $businessIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('items', function ($q) {
                $q->where('investigation', 4); // Tört arany felvásárlás
            })
            ->get();

        foreach ($purchaseTickets as $ticket) {
            foreach ($ticket->items as $item) {
                if ($item->investigation == 4 && $item->weight > 0) {
                    // 200 Ft/gramm jutalék (vagy a kampányban megadott érték)
                    $ratePerGram = 200; // Alapértelmezett
                    
                    // Ha van külön szabály a kampányban
                    $goldRule = $campaign->commissionRules()
                        ->where('commission_type', 'per_gram')
                        ->first();
                        
                    if ($goldRule) {
                        $ratePerGram = $goldRule->commission_value;
                    }
                    
                    $commissionAmount = $item->weight * $ratePerGram;
                    
                    EmployeeCommission::create([
                        'employee_id' => $ticket->employee_id,
                        'business_id' => $ticket->business_id,
                        'commission_campaign_id' => $campaign->id,
                        'source_type' => 'gold_purchase',
                        'source_id' => $ticket->id,
                        'amount' => $commissionAmount,
                        'base_amount' => $item->price,
                        'quantity' => $item->weight,
                        'commission_date' => now(),
                        'status' => 'calculated'
                    ]);
                    
                    $totalCommissions += $commissionAmount;
                }
            }
        }
        
        return $totalCommissions;
    }

    /**
     * Lejárt jegy jutalék számolás - TOVÁBBFEJLESZTETT VERZIÓ
     */
    protected function calculateExpiredTicketCommissions($campaign, $businessIds, $startDate, $endDate)
    {
        $totalCommissions = 0;
        
        $itemLists = ItemList::whereIn('store', $businessIds)
            ->where('tipus', 'expired_ticket')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($itemLists as $itemList) {
            $json = $itemList->json;
            $rows = $json['rows'] ?? [];
            
            foreach ($rows as $index => $row) {
                // Itt kell beállítani, hogy melyik mezők tartalmazzák az adatokat
                // A te rendszeredben ez lehet row15 és row16 vagy más mezők
                $purchasePrice = $row['row15'] ?? $row['rowX'] ?? 0; // Átvételi ár
                $calculatedPrice = $row['row16'] ?? $row['rowY'] ?? 0; // Kalkulált ár
                
                if ($calculatedPrice > $purchasePrice) {
                    $difference = $calculatedPrice - $purchasePrice;
                    
                    // Alapértelmezett 10%, de lehet a kampányban megadva
                    $percentage = 10; 
                    
                    $rule = $campaign->commissionRules()
                        ->where('commission_type', 'difference_percentage')
                        ->first();
                        
                    if ($rule) {
                        $percentage = $rule->commission_value;
                    }
                    
                    $commissionAmount = $difference * ($percentage / 100);
                    
                    EmployeeCommission::create([
                        'employee_id' => $itemList->user_id,
                        'business_id' => $itemList->store,
                        'commission_campaign_id' => $campaign->id,
                        'source_type' => 'expired_ticket',
                        'source_id' => $itemList->id,
                        'amount' => $commissionAmount,
                        'base_amount' => $difference,
                        'commission_date' => now(),
                        'status' => 'calculated'
                    ]);
                    
                    $totalCommissions += $commissionAmount;
                }
            }
        }
        
        return $totalCommissions;
    }

    /**
     * Zálog jutalék számolás - ÚJ
     */
    protected function calculatePawnCommissions($campaign, $businessIds, $startDate, $endDate)
    {
        // Itt jön a zálog jutalék számítás logikája
        // Ez kapcsolódik a napi záráshoz
        return 0;
    }

    /**
     * Egyéb jutalék számolás (web) - ÚJ
     */
    protected function calculateOtherCommissions($campaign, $businessIds, $startDate, $endDate)
    {
        $totalCommissions = 0;
        
        // Webes rendelések jutaléka
        $webOrders = WebshopOrder::whereIn('business_id', $businessIds)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();
            
        foreach ($webOrders as $order) {
            // Itt jön a webes jutalék számítás
            // Pl. a rendelés összegének x%-a
        }
        
        return $totalCommissions;
    }

    /**
     * Alap jutalék számolás
     */
    protected function calculateBasicCommission($campaign, $employee, $businessId, $performance, $rules)
    {
        foreach ($rules as $rule) {
            $commissionAmount = 0;
            
            if ($rule->commission_type == 'percentage') {
                $commissionAmount = $performance * ($rule->commission_value / 100);
            } elseif ($rule->commission_type == 'fixed_amount') {
                $commissionAmount = $rule->commission_value;
            }

            if ($commissionAmount > 0) {
                EmployeeCommission::create([
                    'employee_id' => $employee->employee_id, // Use employee_id, not id
                    'business_id' => $businessId,
                    'commission_campaign_id' => $campaign->id,
                    'commission_rule_id' => $rule->id,
                    'source_type' => 'sales',
                    'amount' => $commissionAmount,
                    'base_amount' => $performance,
                    'commission_date' => now(),
                    'status' => 'calculated'
                ]);
            }
        }
    }

    /**
     * Havi cél jutalék számolás
     */
    protected function calculateMonthlyTargetCommission($campaign, $employee, $businessId, $performance, $rules, $startDate, $endDate)
    {
        $totalCommission = 0;
        
        foreach ($rules as $rule) {
            if ($performance >= $rule->threshold_min) {
                // Csak a threshold feletti részre számoljuk
                $excessAmount = $performance - $rule->threshold_min;
                
                // Ha van maximum küszöb, csak addig számoljuk
                if ($rule->threshold_max && $performance > $rule->threshold_max) {
                    $excessAmount = $rule->threshold_max - $rule->threshold_min;
                }
                
                $commissionAmount = $excessAmount * ($rule->commission_value / 100);
                
                if ($commissionAmount > 0) {
                    EmployeeCommission::create([
                        'employee_id' => $employee->employee_id,
                        'business_id' => $businessId,
                        'commission_campaign_id' => $campaign->id,
                        'commission_rule_id' => $rule->id,
                        'source_type' => 'sales',
                        'amount' => $commissionAmount,
                        'base_amount' => $excessAmount,
                        'commission_date' => now(),
                        'status' => 'calculated'
                    ]);
                    
                    $totalCommission += $commissionAmount;
                }
            }
        }
        
        return $totalCommission;
    }

    /**
     * Heti cél jutalék számolás
     */
    protected function calculateWeeklyTargetCommissions($campaign, $employee, $businessId, $weeklyRules, $startDate, $endDate)
    {
        $totalCommission = 0;
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Hetekre bontás
        $weeks = [];
        $current = clone $start;
        
        while ($current <= $end) {
            $weekNumber = $current->weekOfMonth;
            $weekStart = $current->copy()->startOfWeek();
            $weekEnd = $current->copy()->endOfWeek();
            
            if (!isset($weeks[$weekNumber])) {
                $weeks[$weekNumber] = [
                    'start' => $weekStart,
                    'end' => $weekEnd,
                    'performance' => 0
                ];
            }
            
            $current->addWeek();
        }
        
        // Heti teljesítmények összegyűjtése
        foreach ($weeks as $weekNumber => &$week) {
            $week['performance'] = TempSale::where('employee_id', $employee->employee_id)
                ->where('business_id', $businessId)
                ->whereBetween('created_at', [$week['start'], $week['end']])
                ->sum('final_total_gross');
        }
        
        // Heti célok ellenőrzése
        foreach ($weeklyRules as $weekNumber => $rules) {
            if (isset($weeks[$weekNumber])) {
                $weekPerformance = $weeks[$weekNumber]['performance'];
                
                foreach ($rules as $rule) {
                    if ($weekPerformance >= $rule->threshold_min) {
                        $excessAmount = $weekPerformance - $rule->threshold_min;
                        
                        if ($rule->threshold_max && $weekPerformance > $rule->threshold_max) {
                            $excessAmount = $rule->threshold_max - $rule->threshold_min;
                        }
                        
                        $commissionAmount = $excessAmount * ($rule->commission_value / 100);
                        
                        if ($commissionAmount > 0) {
                            EmployeeCommission::create([
                                'employee_id' => $employee->employee_id,
                                'business_id' => $businessId,
                                'commission_campaign_id' => $campaign->id,
                                'commission_rule_id' => $rule->id,
                                'source_type' => 'sales',
                                'amount' => $commissionAmount,
                                'base_amount' => $excessAmount,
                                'commission_date' => now(),
                                'status' => 'calculated'
                            ]);
                            
                            $totalCommission += $commissionAmount;
                        }
                    }
                }
            }
        }
        
        return $totalCommission;
    }

    /**
     * Bolt teljesítmény lekérése
     */
    protected function getBusinessPerformance($businessId, $startDate, $endDate)
    {
        return TempSale::where('business_id', $businessId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('final_total_gross');
    }

    /**
     * Alkalmazott teljesítmény lekérése
     */
    protected function getEmployeePerformance($employeeId, $businessId, $startDate, $endDate)
    {
        return TempSale::where('employee_id', $employeeId)
            ->where('business_id', $businessId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('final_total_gross');
    }

    /**
     * Jutalék kifizetése - TOVÁBBFEJLESZTETT VERZIÓ
     */
    public function payCommissions($employeeId, $businessId, $month, $paymentMethod = 'cash', $note = null)
    {
        try {
            $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

            $commissions = EmployeeCommission::where('employee_id', $employeeId)
                ->where('business_id', $businessId)
                ->whereBetween('commission_date', [$startDate, $endDate])
                ->where('status', 'calculated')
                ->get();

            if ($commissions->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Nincs kifizethető jutalék az adott időszakban.',
                    'amount' => 0
                ];
            }

            $totalAmount = $commissions->sum('amount');

            DB::transaction(function () use ($commissions, $paymentMethod, $note) {
                foreach ($commissions as $commission) {
                    $commission->update([
                        'status' => 'paid',
                        'paid_date' => now(),
                        'payment_method' => $paymentMethod,
                        'note' => $note ?: $commission->note
                    ]);
                }
            });

            // Log the payment
            Log::info('Commission paid', [
                'employee_id' => $employeeId,
                'business_id' => $businessId,
                'month' => $month,
                'amount' => $totalAmount,
                'payment_method' => $paymentMethod
            ]);

            return [
                'success' => true,
                'message' => 'Jutalék sikeresen kifizetve.',
                'amount' => $totalAmount,
                'count' => $commissions->count()
            ];

        } catch (\Exception $e) {
            Log::error('Commission payment error: ' . $e->getMessage(), [
                'employee_id' => $employeeId,
                'business_id' => $businessId,
                'month' => $month
            ]);

            return [
                'success' => false,
                'message' => 'Hiba történt a kifizetés során: ' . $e->getMessage(),
                'amount' => 0
            ];
        }
    }

    /**
     * Jutalékok összesítése alkalmazottanként - ÚJ
     */
    public function getEmployeeCommissionSummary($employeeId, $year, $month = null)
    {
        $query = EmployeeCommission::where('employee_id', $employeeId)
            ->whereYear('commission_date', $year);
            
        if ($month) {
            $query->whereMonth('commission_date', $month);
        }
        
        $commissions = $query->get();
        
        return [
            'total_calculated' => $commissions->where('status', 'calculated')->sum('amount'),
            'total_paid' => $commissions->where('status', 'paid')->sum('amount'),
            'by_type' => $commissions->groupBy('source_type')->map(function ($items) {
                return $items->sum('amount');
            }),
            'by_month' => $commissions->groupBy(function ($item) {
                return $item->commission_date->format('Y-m');
            })->map(function ($items) {
                return $items->sum('amount');
            })
        ];
    }

    /**
     * Bolt teljesítmény riport - ÚJ
     */
    public function getBusinessPerformanceReport($businessId, $startDate, $endDate)
    {
        $employees = Employee::whereHas('businesses', function ($q) use ($businessId) {
            $q->where('business_id', $businessId);
        })->get();
        
        $report = [];
        
        foreach ($employees as $employee) {
            $performance = $this->getEmployeePerformance($employee->employee_id, $businessId, $startDate, $endDate);
            $commissions = EmployeeCommission::where('employee_id', $employee->employee_id)
                ->where('business_id', $businessId)
                ->whereBetween('commission_date', [$startDate, $endDate])
                ->sum('amount');
                
            $report[] = [
                'employee_id' => $employee->employee_id,
                'employee_name' => $employee->lastname . ' ' . $employee->firstname,
                'performance' => $performance,
                'commission' => $commissions,
                'percentage' => $performance > 0 ? round(($commissions / $performance) * 100, 2) : 0
            ];
        }
        
        return $report;
    }
}