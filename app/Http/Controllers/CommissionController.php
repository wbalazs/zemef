<?php

namespace App\Http\Controllers;

use App\Models\CommissionCampaign;
use App\Models\CommissionRule;
use App\Models\EmployeeCommission;
use App\Models\Business;
use App\Models\Employee;
use App\Services\CommissionCalculator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommissionController extends Controller
{
    protected $calculator;

    public function __construct(CommissionCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function index(Request $request)
    {
        $businesses = Business::all();
        $campaigns = CommissionCampaign::with('businesses')
            ->when($request->business_id, function ($query) use ($request) {
                $query->whereHas('businesses', function ($q) use ($request) {
                    $q->where('business_id', $request->business_id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('commissions.index', compact('campaigns', 'businesses'));
    }

    public function create()
    {
        $businesses = Business::all();
        return view('commissions.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'campaign_type' => 'required|in:sales,gold_purchase,expired_ticket,pawn,other',
            'businesses' => 'required|array',
            'businesses.*' => 'exists:businesses,business_id',
            'rules' => 'required|array',
            'rules.*.rule_type' => 'required|in:basic,target',
            'rules.*.commission_type' => 'required|in:percentage,fixed_amount,per_gram,difference_percentage',
            'rules.*.threshold_min' => 'nullable|numeric',
            'rules.*.commission_value' => 'required|numeric',
            'rules.*.period_type' => 'nullable|in:weekly,monthly',
            'rules.*.period_week' => 'nullable|integer|between:1,4'
        ]);

        $campaign = CommissionCampaign::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'campaign_type' => $validated['campaign_type']
        ]);

        $campaign->businesses()->attach($validated['businesses']);

        foreach ($validated['rules'] as $rule) {
            $campaign->commissionRules()->create($rule);
        }

        return redirect()->route('commissions.index')
            ->with('success', 'Jutalék kampány sikeresen létrehozva!');
    }

    public function show(CommissionCampaign $commission)
    {
        $commission->load('businesses', 'commissionRules');
        return view('commissions.show', compact('commission'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:commission_campaigns,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'business_id' => 'nullable|exists:businesses,id'
        ]);

        $campaign = CommissionCampaign::find($request->campaign_id);
        
        $this->calculator->calculateForPeriod(
            $request->start_date,
            $request->end_date,
            $request->business_id
        );

        return redirect()->back()
            ->with('success', 'Jutalékok sikeresen kiszámolva!');
    }

    public function performance(Request $request)
    {
        $businesses = Business::all();
        $employees = Employee::all();

        $date = $request->get('date', Carbon::today());

        $businessPerformance = null;
        $employeePerformance = null;

        if ($request->business_id) {
            $businessPerformance = \DB::table('business_daily_performance')
                ->where('business_id', $request->business_id)
                ->whereMonth('performance_date', $date->month)
                ->orderBy('performance_date')
                ->get();
        }

        if ($request->employee_id && $request->business_id) {
            $employeePerformance = \DB::table('employee_daily_performance')
                ->where('employee_id', $request->employee_id)
                ->where('business_id', $request->business_id)
                ->whereMonth('performance_date', $date->month)
                ->orderBy('performance_date')
                ->get();
        }

        return view('commissions.performance', compact(
            'businesses', 'employees', 'businessPerformance', 'employeePerformance', 'date'
        ));
    }

    public function pay(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'business_id' => 'required|exists:businesses,id',
            'month' => 'required|date_format:Y-m',
            'payment_method' => 'required|in:cash,bank_transfer'
        ]);

        $totalPaid = $this->calculator->payCommissions(
            $request->employee_id,
            $request->business_id,
            $request->month,
            $request->payment_method
        );

        return redirect()->back()
            ->with('success', "Jutalékok sikeresen kifizetve! Összeg: {$totalPaid} Ft");
    }
    public function report(Request $request)
    {
        $businesses = Business::all();
        
        if ($request->has(['start_date', 'end_date'])) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $businessId = $request->business_id;
            
            $query = EmployeeCommission::with(['employee', 'business', 'campaign'])
                ->whereBetween('commission_date', [$startDate, $endDate])
                ->where('status', 'calculated');
                
            if ($businessId) {
                $query->where('business_id', $businessId);
            }
            
            $commissions = $query->get();
            
            // Bolt összesítés
            $businessSummary = $commissions->groupBy('business.name')
                ->map(function ($items) {
                    return $items->sum('amount');
                })
                ->sort()
                ->reverse()
                ->take(10)
                ->toArray();
                
            // Alkalmazott összesítés
            $employeeSummary = $commissions->groupBy(function ($item) {
                    return $item->employee->lastname . ' ' . $item->employee->firstname;
                })
                ->map(function ($items) {
                    return $items->sum('amount');
                })
                ->sort()
                ->reverse()
                ->take(10)
                ->toArray();
            
            $reportData = [
                'total_businesses' => $businessSummary ? count($businessSummary) : 0,
                'total_employees' => $employeeSummary ? count($employeeSummary) : 0,
                'total_commission' => $commissions->sum('amount'),
                'avg_commission' => $commissions->count() > 0 ? $commissions->sum('amount') / $commissions->count() : 0,
                'business_summary' => $businessSummary,
                'employee_summary' => $employeeSummary,
                'details' => $commissions
            ];
            
            return view('commissions.report', compact('businesses', 'reportData'));
        }
        
        return view('commissions.report', compact('businesses'));
    }
        /**
     * Remove the specified category from storage
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CommissionCampaign $commission)
    {

        $commission->delete();

        return redirect()->route('commissions.index')->withStatus('A kampány sikeresen törölve.');
    }
}