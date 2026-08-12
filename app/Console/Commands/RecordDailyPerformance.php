<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Models\Employee;
use App\Models\TempSale;
use App\Models\PurchaseTicket;
use App\Models\BusinessDailyPerformance;
use App\Models\EmployeeDailyPerformance;
use Carbon\Carbon;

class RecordDailyPerformance extends Command
{
    protected $signature = 'performance:record-daily {date?}';
    protected $description = 'Record daily performance for businesses and employees';

    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::yesterday();
        
        $this->info("Recording performance for date: " . $date->format('Y-m-d'));

        $businesses = Business::all();
        
        foreach ($businesses as $business) {
            // Bolt napi forgalma
            $dailySales = TempSale::where('business_id', $business->id)
                ->whereDate('created_at', $date)
                ->sum('final_total_gross');

            BusinessDailyPerformance::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'performance_date' => $date->format('Y-m-d')
                ],
                [
                    'total_sales' => $dailySales
                ]
            );

            // Alkalmazottak napi teljesítménye
            $employees = Employee::whereHas('businesses', function ($q) use ($business) {
                $q->where('business_id', $business->id);
            })->get();

            foreach ($employees as $employee) {
                $employeeSales = TempSale::where('employee_id', $employee->id)
                    ->where('business_id', $business->id)
                    ->whereDate('created_at', $date)
                    ->sum('final_total_gross');

                EmployeeDailyPerformance::updateOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'business_id' => $business->id,
                        'performance_date' => $date->format('Y-m-d')
                    ],
                    [
                        'total_sales' => $employeeSales
                    ]
                );
            }
        }

        $this->info('Daily performance recorded successfully!');
    }
}