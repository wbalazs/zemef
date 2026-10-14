<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Business;
use App\Models\Warehouse;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Category;
use App\Models\ProductStatus;
use Carbon\Carbon;
use App\Http\Requests\BusinessRequest;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Business::class);
    }

    /**
     * Display a listing of the employees
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Business $model)
    {
        Business::where('name', '--empty_row--')->delete();
        return view('businesses.index', ['businesses' => $model->get()]);
    }

    /**
     * Show the form for creating a new item
     *
     * @param  \App\Tag $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $warehouses = Warehouse::where('status', 1)->orderBy('name', 'ASC')->get();
        $business = Business::create(['name' => '--empty_row--']);
        $business->name = '';
        $business->discount_value = 0;
        $active_product_statuses = ProductStatus::with('vatType')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
        $business->load(['business_markings.productStatus.vatType', 'business_aliases.warehouse']);

        return view('businesses.edit', [
            'business' => $business,
            'warehouses' => $warehouses,
            'active_product_statuses' => $active_product_statuses,
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\BusinessRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BusinessRequest $request, Business $model)
    {
        //save business_warehouses
        $zip = Zip::where('id', $request->zip_id)->first();
        $city = City::where('id', $request->city_id)->first();

        $business = $model->create($request->merge([
                'zip' => $zip->name ?? null,
                'zip_id' => $zip->id ?? 0,
                'city' => $city->name ?? null,
                'city_id' => $city->id ?? 0
        ])->all());
        $business->warehouses()->sync($request->warehouses);

        return redirect()->route('business.index')->withStatus('Az üzlet sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $business
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Business $business)
    {
        $warehouses = Warehouse::where('status', 1)->orderBy('name', 'ASC')->get();
        $active_product_statuses = ProductStatus::with('vatType')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();
        $business->load(['business_markings.productStatus.vatType', 'business_aliases.warehouse']);

        return view('businesses.edit', [
            'business' => $business,
            'warehouses' => $warehouses,
            'active_product_statuses' => $active_product_statuses,
        ]);
    }

    public function getBusinessData(Request $request)
    {
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,business_id',
        ]);

        $business = Business::with(['business_markings.productStatus.vatType'])
            ->where('business_id', $request->business_id)
            ->firstOrFail();

        $result = new \stdClass();
        $result->business_id = $business->business_id;
        $result->name = $business->name;
        $result->business_markings = $business->business_markings->map(function ($marking) {
            return [
                'id' => $marking->id,
                'name' => $marking->name,
                'product_status_id' => $marking->product_status_id,
                'price' => optional($marking->productStatus)->price,
                'vat_type_id' => optional($marking->productStatus)->vat_type_id,
                'vat_name' => optional(optional($marking->productStatus)->vatType)->name,
                'default' => (int) $marking->default,
                'status' => (int) $marking->status,
            ];
        })->values();

        return response()->json($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BusinessRequest  $request
     * @param  \App\Models\Business  $business
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BusinessRequest $request, Business $business)
    {
        $zip = Zip::where('id', $request->zip_id)->first();
        $city = City::where('id', $request->city_id)->first();

        $business->update(
            array_merge($request->all(), [
                'status' => $request->status,
                'zip' => $zip->name ?? null,
                'zip_id' => $zip->id ?? 0,
                'city' => $city->name ?? null,
                'city_id' => $city->id ?? 0,
                'discount_value' => $request->discount_value ?? 0
            ])
        );

        $business->warehouses()->sync($request->warehouses);
        return redirect()->route('business.index')->withStatus('Az üzlet adatai sikeresen mentve.');
    }

    public function updateCalendar(Request $request, Business $business)
    {
        try {
            $calendarId = $request->input('calendar_id');
            $isWorkday = $request->input('is_workday');
            $date = $request->input('date');
            
            \Log::info('Calendar update request:', [
                'business_id' => $business->business_id,
                'calendar_id' => $calendarId,
                'date' => $date,
                'is_workday' => $isWorkday
            ]);
            
            // Először próbáljuk ID alapján keresni
            $calendar = null;
            
            if ($calendarId && !strpos($calendarId, 'new_') === 0) {
                $calendar = $business->calendars()
                    ->where('id', $calendarId)
                    ->first();
                    
                if ($calendar) {
                    \Log::info('Found by ID:', ['id' => $calendar->id, 'day' => $calendar->day]);
                }
            }
            
            // Ha nem találtuk ID alapján, próbáljuk dátum alapján
            if (!$calendar) {
                $calendar = $business->calendars()
                    ->where('day', $date)
                    ->first();
                    
                if ($calendar) {
                    \Log::info('Found by date:', ['id' => $calendar->id, 'day' => $calendar->day]);
                }
            }
            
            if ($calendar) {
                // Meglévő rekord frissítése
                $calendar->update(['is_workday' => $isWorkday]);
                
                \Log::info('Updated calendar:', ['id' => $calendar->id, 'is_workday' => $calendar->is_workday]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Naptár bejegyzés frissítve',
                    'calendar_id' => $calendar->id,
                    'is_workday' => $calendar->is_workday
                ]);
            } else {
                // Új rekord létrehozása
                $calendar = $business->calendars()->create([
                    'business_id' => $business->business_id,
                    'day' => $date,
                    'day_name' => \Carbon\Carbon::parse($date)->format('l'),
                    'is_workday' => $isWorkday
                ]);
                
                \Log::info('Created new calendar:', ['id' => $calendar->id, 'day' => $calendar->day]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Új naptár bejegyzés létrehozva',
                    'calendar_id' => $calendar->id,
                    'is_workday' => $calendar->is_workday
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Calendar update error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false, 
                'message' => 'Hiba: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Business $business)
    {
        $business->delete();

        return redirect()->route('business.index')->withStatus('Az üzlet sikeresen törölve.');
    }
}