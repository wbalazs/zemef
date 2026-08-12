<?php

namespace App\Observers;

use App\Models\Business;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BusinessObserver
{
    public function created(Business $business)
    {
        $startYear = now()->year;
        $endYear = $startYear + 20;
        $dates = collect();

        for ($year = $startYear; $year <= $endYear; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++) {
                    $date = Carbon::createFromDate($year, $month, $day);

                    $isWorkday = !$date->isWeekend(); 

                    $dates->push([
                        'business_id' => $business->business_id,
                        'day' => $date->toDateString(),
                        'is_workday' => $isWorkday,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Tömeges beszúrás
        \DB::table('business_calendars')->insert($dates->toArray());
    }
}
