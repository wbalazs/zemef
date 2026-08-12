<?php

namespace App\Http\Controllers;


use App\Models\Business;
use App\Models\Employee;
use App\Models\Calendar;
use App\Models\Holiday;
use App\Models\HolidayStatus;
use App\Models\EmployeeHoliday;
use App\Models\UserCalendarEvent;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\HolidayRequest;
use Illuminate\Http\Request;
use DB;
use Auth;



class HolidayController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Holiday::class);
    }

    
    public function index()
    {
        //Szabadságok lekérdezése a bejelentkezett felhasználóhoz
        $holidays = Holiday::with(['approvedBy', 'declinedBy'])
                   ->where('employee_id', Auth::user()->employee?->employee_id)
                   ->get();
        $currentYear = date('Y');
        $employeeId = auth()->user()->employee?->employee_id;
        $employeeHoliday = EmployeeHoliday::where('employee_id', $employeeId)
                                        ->where('holiday_year', $currentYear)
                                        ->first();

        return view('holidays.index', ['holidays' => $holidays,'employeeHoliday' => $employeeHoliday]);
    }

    //Új szabadságkérelem felvitele
    public function create()
    {
        $currentYear = date('Y');
        $employeeId = auth()->user()->employee?->employee_id;
        $employeeHoliday = EmployeeHoliday::where('employee_id', $employeeId)
                                        ->where('holiday_year', $currentYear)
                                        ->first();

        if (!$employeeHoliday || is_null($employeeHoliday->holiday_days)) {
            return redirect()->route('holiday.index')
                            ->with('error', 'Hiányoznak az aktuális évre vonatkozó szabadság adatok!');
        }elseif ($employeeHoliday->holiday_remainings == 0) {
            return redirect()->route('holiday.index')
                            ->with('error', 'A tárgyi évre már nem igényelhetsz szabadságot!');
        }

        return view('holidays.create', ['employeeHoliday' => $employeeHoliday]);
    }

    //Szabadságkérelem mentése
    public function store(Request $request)
    {
        $messages = [
            'end_date.after_or_equal' => 'A Dátum-tól nem lehet korábbi dátum, mint Dátum-ig!',
        ];

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|max:100',
        ], $messages);

        $employeeId = auth()->user()->employee?->employee_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $existingHolidays = Holiday::where('employee_id', $employeeId)
        ->where('status_id', '<>', 3)
        ->where(function($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere([
                    ['start_date', '<=', $startDate],
                    ['end_date', '>=', $endDate]
                ]);
        })
        ->count();

        $currentYear = date('Y');
        $employeeHoliday = EmployeeHoliday::where('employee_id', $employeeId)
                                        ->where('holiday_year', $currentYear)
                                        ->first();

        if ($existingHolidays > 0) {
            return redirect()->route('holiday.index')
                ->withErrors(['overlap' => 'Erre az időszakra már van beadott szabadságkérelem: '.$startDate.' - '.$endDate]);
        }

        $holiday = new Holiday();
        $holiday->employee_id = $employeeId;
        $holiday->start_date = $startDate;
        $holiday->end_date = $endDate;
        $holiday->notes = $request->input('notes');
        $holiday->status_id = 1;
        $holiday->year = date('Y');

        $workdays = DB::table('master_calendar')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_workday', 1)
            ->count();

        if ($employeeHoliday->holiday_remainings < $workdays) {
            return redirect()->route('holiday.index')
                ->withErrors(['overlap' => 'Nem áll rendelkezésre elegendő szabadság!']);
        }

        
        if($workdays == 0) {
            return redirect()->route('holiday.index')
                ->withErrors(['workdays' => 'A kiválasztott időszakban nincs munkanap!']);
        }
        $holiday->days_taken = $workdays;

        $holiday->save();

        return redirect()->route('holiday.index')
            ->with('success', 'Az új szabadság kérelem sikeresen el lett küldve!');
    }


    //Jóváhagyásra váró szabadságok listája
    public function wait_approve()
    {
        $holidays = Holiday::with(['status', 'employee'])->where('status_id', 1)->get();
        return view('holidays.wait_approve', ['holidays' => $holidays]);
    }

    //Szabadság jóváhagyása
    public function approve_holiday(Request $request)
    {
        try {
            DB::beginTransaction();

            $holiday = Holiday::findOrFail($request->id);

            if ($holiday->status_id == 2) {
                return response()->json(['data' => ['error' => 'error', 'message' => 'A szabadság már jóváhagyásra került!']]);
            }elseif ($holiday->status_id == 3) {
                return response()->json(['data' => ['error' => 'error', 'message' => 'A szabadság már el lett utasítva!']]);
            }

            $employeeHoliday = EmployeeHoliday::where('employee_id', $holiday->employee_id)
                                            ->where('holiday_year', $holiday->year)
                                            ->first();

            if (!$employeeHoliday || $employeeHoliday->holiday_remainings < $holiday->days_taken) {
                DB::rollback();
                return response()->json(['data' => ['error' => 'error', 'message' => 'Nincs elegendő szabadság!']]);
            }

            $employeeHoliday->holiday_remainings -= $holiday->days_taken;
            $employeeHoliday->save();

            $holiday->status_id = 2;
            $holiday->approved_employee = auth()->user()->employee?->employee_id;
            $holiday->approved_at = now();
            $holiday->save();

            DB::commit();
            return response()->json(['data' => ['success' => 'success']]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' => ['error' => 'error', 'message' => 'Hiba történt:'.$e->getMessage()]]);
        }
    }

    //Szabadság elutasítása
    public function decline_holiday(Request $request)
    {
        try {
            DB::beginTransaction();
            $holiday = Holiday::findOrFail($request->id);

            if ($holiday->status_id == 3) {
                return response()->json(['data' => ['error' => 'error', 'message' => 'A szabadság már el lett utasítva!']]);
            }elseif ($holiday->status_id == 2) {
                return response()->json(['data' => ['error' => 'error', 'message' => 'A szabadság már jóváhagyásra került!']]);
            }

            $holiday->status_id = 3;
            $holiday->declined_employee = auth()->user()->employee?->employee_id;
            $holiday->declined_at = now();
            $holiday->save();

            DB::commit();
            return response()->json(['data' => ['success' => 'success']]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['data' => ['error' => 'error', 'message' => 'Hiba történt:'.$e->getMessage()]]);
        }
    }

    //usercalendar
    public function userCalendar()
    {
        return view('user_calendar.calendar', ['none' => 0]);
    }

    public function getCalendarEvents(Request $request)
    {
        $userid = auth()->user()->id;
        $events = UserCalendarEvent::where('user_id', $userid)
            ->whereBetween('start', [$request->start, $request->end])
            ->get();

        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'color' => $event->color
            ];
        }

        return response()->json($formattedEvents);
    }

    public function saveCalendarEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'color' => 'required|string'
        ]);

        $event = UserCalendarEvent::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'start' => Carbon::parse($request->start)->setTime(12, 0)->toDateTimeString(),
            'end' => Carbon::parse($request->end)->setTime(13, 0)->toDateTimeString(),
            'color' => $request->color
        ]);

        return response()->json(['success' => true, 'message' => 'Esemény frissítése sikeres!','event' => $event], 200);
    }

    public function updateCalendarEventFull(Request $request, $id)
    {
        $event = UserCalendarEvent::find($id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Esemény nem található!'], 200);
        }

        $event->start = Carbon::parse($request->start)->setTime(12, 0)->toDateTimeString();
        $event->end = Carbon::parse($request->end)->setTime(13, 0)->toDateTimeString();
        $event->title = $request->title;
        $event->color = $request->color;
        $event->save();

        return response()->json(['success' => true, 'message' => 'Esemény frissítése sikeres!'], 200);
    }

    public function updateCalendarEvent(Request $request, $id)
    {
        $event = UserCalendarEvent::find($id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Esemény nem található!'], 200);
        }

        $event->start = Carbon::parse($request->start)->setTime(12, 0)->toDateTimeString();
        $event->end = Carbon::parse($request->end)->setTime(13, 0)->toDateTimeString();
        $event->save();

        return response()->json(['success' => true, 'message' => 'Esemény frissítése sikeres!'], 200);
    }

    public function deleteCalendarEvent($id)
    {
        $event = UserCalendarEvent::find($id);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Esemény nem található!'], 200);
        }

        $event->delete();

        return response()->json(['success' => true, 'message' => 'Esemény törlése sikeres!'], 200);
    }


    private function formatDateToDayName($date) {
        setlocale(LC_TIME, 'hu_HU.UTF-8');
        return Carbon::parse($date)->formatLocalized('%Y-%m-%d %A');
    }



}
