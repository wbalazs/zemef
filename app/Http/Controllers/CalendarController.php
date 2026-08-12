<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Tool;
use App\Models\ToolType;
use App\Models\DocType;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Calendar;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\CalendarRequest;
use Illuminate\Http\Request;
use DB;



class CalendarController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Tool::class);
    }

    
    public function index(Calendar $model)
    {
        $calendars = $model->where('year', '=', date('Y'))->get();
        
        foreach ($calendars as &$day) {
            $day->formattedDate = $this->formatDateToDayName($day->date);
        }

        return view('master.calendars.index', ['calendars' => $calendars]);
    }

    private function formatDateToDayName($date) {
        Carbon::setLocale('hu');
        return Carbon::parse($date)->translatedFormat('Y-m-d l');
    }

    //calendar_update ajax
    public function calendar_update(Request $request)
    {
        $calendar_id = $request->id;
        $is_workday = $request->is_working_day;
        $calendar = Calendar::find($calendar_id);
        $calendar->is_workday = $is_workday;
        $calendar->save();
        return response()->json(['success'=>'A naptár sikeresen frissítve!']);
    }



    public function fillCalendarData()
    {
        // Kezdő dátum (például most)
        $currentDate = Carbon::createfromformat('Y-m-d', '2023-01-01');

        // Végdátum (2070. december 31.)
        $endDate = Carbon::createfromformat('Y-m-d', '2070-01-01');

        while ($currentDate->lte($endDate)) { // Amíg a currentDate kisebb vagy egyenlő az endDate-tel

            Calendar::create([
                'year' => $currentDate->year,
                'date' => $currentDate->toDateString(),
                'is_workday' => $currentDate->isWeekday() ? 1 : 0
            ]);

            $currentDate->addDay(); // Növeljük a dátumot egy nappal
        }

        return redirect()->back()->withStatus('A naptár adatok sikeresen hozzáadva 2070-ig.');
    }
}
