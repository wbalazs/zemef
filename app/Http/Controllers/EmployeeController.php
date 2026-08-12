<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Employee;
use App\Models\DocType;
use App\Models\BankAccount;
use App\Models\Business;
use App\Models\Company;
use App\Models\Document;
use App\Models\Warehouse;
use App\Models\EmployeeEducation;
use App\Models\EmployeeHoliday;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Database\QueryException;

class EmployeeController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Employee::class);
    }

    /**
     * Display a listing of the employees
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Employee $model)
    {
        Employee::where('firstname', '--empty_row--')->delete();

        return view('employees.index', ['employees' => $model->get()]);
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
        $employee = Employee::create(['lastname' => '--empty_row--',
                    'firstname' => '--empty_row--']);
        $employee->firstname = '';
        $employee->lastname = '';
        $bank_accounts = BankAccount::where('status', 1)->orderBy('bank_account_number', 'ASC')->get();
        $businesses = Business::where('status', 1)->orderBy('name', 'ASC')->get();
        // Lekérdezzük a dolgozóhoz rendelt üzletek ID-jait
        $businessIds = $employee->businesses->pluck('business_business_id');
        
        // Az üzletek ID-jai alapján lekérdezzük a hozzájuk tartozó raktárakat
        $warehouses = Warehouse::where('status', 1)
            ->whereIn('warehouse_id', function ($query) use ($businessIds) {
                $query->select('warehouse_warehouse_id')
                    ->from('business_warehouse')
                    ->whereIn('business_business_id', $businessIds);
            })
            ->orderBy('name', 'ASC')
            ->get();
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $companies = Company::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('employees.edit', [
            'employee' => $employee,
            'businesses' => $businesses,
            'bank_accounts' => $bank_accounts,
            'companies' => $companies,
            'warehouses' => $warehouses,
            'doc_types' => $doc_types
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EmployeeRequest $request, Employee $model)
    {
        $home_zip = Zip::where('id', $request->home_zip_id)->first();
        $home_city = City::where('id', $request->home_city_id)->first();
        $mailing_city = City::where('id', $request->mailing_city_id)->first();
        $mailing_zip = Zip::where('id', $request->mailing_zip_id)->first();
        $temporary_zip = Zip::where('id', $request->temporary_zip_id)->first();
        $temporary_city = City::where('id', $request->temporary_city_id)->first();

        $employee = $model->create($request->merge([
                'home_zip' => $home_zip->name ?? null,
                'home_zip_id' => $home_zip->id ?? 0,
                'home_city' => $home_city->name ?? null,
                'home_city_id' => $home_city->id ?? 0,
                'mailing_zip' => $mailing_zip->name ?? null,
                'mailing_zip_id' => $mailing_zip->id ?? 0,
                'mailing_city' => $mailing_city->name ?? null,
                'mailing_city_id' => $mailing_city->id ?? 0,
                'temporary_zip' => $temporary_zip->name ?? null,
                'temporary_zip_id' => $temporary_zip->id ?? 0,
                'temporary_city' => $temporary_city->name ?? null,
                'temporary_city_id' => $temporary_city->id ?? 0,
                'has_temporary' => $request->has_temporary ?? null]
        )->all());

        return redirect()->route('employee.index')->withStatus('A munkavállaló sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $employee
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Employee $employee)
    {
        $businesses = Business::where('status', 1)->orderBy('name', 'ASC')->get();
        // Lekérdezzük a dolgozóhoz rendelt üzletek ID-jait
        $businessIds = $employee->businesses->pluck('business_id');
        
        // Az üzletek ID-jai alapján lekérdezzük a hozzájuk tartozó raktárakat
        $warehouses = Warehouse::where('status', 1)
            ->whereIn('warehouse_id', function ($query) use ($businessIds) {
                $query->select('warehouse_warehouse_id')
                    ->from('business_warehouse')
                    ->whereIn('business_business_id', $businessIds);
            })
            ->orderBy('name', 'ASC')
            ->get();
        $companies = Company::where('status', 1)->orderBy('name', 'ASC')->get();
        $doc_types = DocType::orderBy('name', 'ASC')->get();

        $bank_accounts = collect();

        // A cégek collection-ön végigmegyünk és összegyűjtjük a bank_accountokat egy közös kollekcióba
        foreach ($companies as $company) {
            $bank_accounts = $bank_accounts->merge($company->bank_accounts);
        }

        // Az összes bank_account-ot egyedivé tesszük a unique() metódussal
        $unique_bank_accounts = $bank_accounts->unique('bank_account_id');

        if(!$employee->holidays->where('holiday_year', date('Y'))->count()){
            $holiday = new EmployeeHoliday();
            $holiday->holiday_year = date('Y');
            $holiday->employee_id = $employee->employee_id;
            $holiday->save();
        }

        return view('employees.edit', [
            'employee' => $employee,
            'bank_accounts' => $unique_bank_accounts,
            'companies' => $companies,
            'businesses' => $businesses,
            'warehouses' => $warehouses,
            'doc_types' => $doc_types
        ]);
    }

    public function employee_business_change(Request $request)
    {
        $employee = Employee::find($request->employee_id);
        $business_ids = $request->has('business_ids') ? $request->business_ids : [];
        $employee->businesses()->sync($business_ids);

        // Meghatározzuk a releváns raktárakat, amelyek az újonnan kiválasztott üzletekhez tartoznak
        $relevantWarehouseIds = Warehouse::whereIn('warehouse_id', function ($query) use ($business_ids) {
            $query->select('warehouse_warehouse_id')
                ->from('business_warehouse')
                ->whereIn('business_business_id', $business_ids);
        })?->pluck('warehouse_id')->toArray();

        // Kizárjuk azokat a raktárakat, amelyek már nem relevánsak, de megtartjuk a többit
        $currentWarehouseIds = $employee->warehouses->pluck('warehouse_id')->toArray();
        $warehousesToDetach = array_diff($currentWarehouseIds, $relevantWarehouseIds);
        $employee->warehouses()->detach($warehousesToDetach);

        // Lekérdezzük a frissített raktárlistát
        $warehouses = Warehouse::whereIn('warehouse_id', $relevantWarehouseIds)
            ->orderBy('name', 'ASC')
            ->get();

        $html = "";
        foreach ($warehouses as $warehouse) {
            $selected = in_array($warehouse->warehouse_id, $currentWarehouseIds) ? ' selected' : '';
            $html .= "<option value='" . $warehouse->warehouse_id . "'" . $selected . ">" . $warehouse->name . "</option>";
        }

        return $html;
    }

    public function getDatatableState(Request $request)
    {
        $validated = $request->validate([
            'datatable_name' => 'required|string',
        ]);

        $user_id = Auth::user()->id;

        $state = DB::table('datatable_states')
            ->where('user_id', $user_id)
            ->where('datatable_name', $validated['datatable_name'])
            ->value('state');

        return response()->json(['state' => $state]);
    }

    public function saveDatatableState(Request $request)
    {
        $validated = $request->validate([
            'datatable_name' => 'required|string',
            'state' => 'required|json',
        ]);

        $user_id = Auth::user()->id;

        DB::table('datatable_states')->updateOrInsert(
            [
                'user_id' => $user_id,
                'datatable_name' => $validated['datatable_name']
            ],
            [
                'state' => $validated['state'],
                'updated_at' => now()
            ]
        );

        return response()->json(['status' => 'success']);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {

        $home_zip = Zip::where('id', $request->home_zip_id)->first();
        $home_city = City::where('id', $request->home_city_id)->first();
        $mailing_city = City::where('id', $request->mailing_city_id)->first();
        $mailing_zip = Zip::where('id', $request->mailing_zip_id)->first();
        $temporary_zip = Zip::where('id', $request->temporary_zip_id)->first();
        $temporary_city = City::where('id', $request->temporary_city_id)->first();
        
        $employee->update(
            array_merge($request->all(),
                ['bank_contact' => $request->bank_contact ? 1 : null,
                'card' => $request->card ? 1 : null,
                'home_zip' => $home_zip->name ?? null,
                'home_zip_id' => $home_zip->id ?? 0,
                'home_city' => $home_city->name ?? null,
                'home_city_id' => $home_city->id ?? 0,
                'mailing_zip' => $mailing_zip->name ?? null,
                'mailing_zip_id' => $mailing_zip->id ?? 0,
                'mailing_city' => $mailing_city->name ?? null,
                'mailing_city_id' => $mailing_city->id ?? 0,
                'temporary_zip' => $temporary_zip->name ?? null,
                'temporary_zip_id' => $temporary_zip->id ?? 0,
                'temporary_city' => $temporary_city->name ?? null,
                'temporary_city_id' => $temporary_city->id ?? 0,
                'has_temporary' => $request->has_temporary ?? null]
            )
        );
        
        $employee->home_city = $home_city->name ?? null;
        $employee->mailing_city = $mailing_city->name ?? null;
        $employee->temporary_city = $temporary_city->name ?? null;
        $employee->save();

        if(!$request->card){
            $employee->card_expiration_year = null;
            $employee->card_expiration_month = null;
            $employee->card_limit = null;
            $employee->save();
        }

        $employee->bank_accounts()->sync($request->bank_accounts);
        $employee->businesses()->sync($request->businesses);
        $employee->companies()->sync($request->companies);

        $businessIds = $request->businesses ?? [];

        $warehouseIds = DB::table('business_warehouse')
            ->whereIn('business_business_id', $businessIds)
            ->pluck('warehouse_warehouse_id')
            ->toArray();

        // Azonosítjuk azokat a raktárakat, amelyeket hozzá kell rendelni a munkavállalóhoz
        $validWarehouseIds = Warehouse::whereIn('warehouse_id', $request->warehouses ?? [])
            ->pluck('warehouse_id')
            ->toArray();

        // Egyesítjük a validWarehouseIds és a warehouseIds tömböket
        $finalWarehouseIds = array_intersect($validWarehouseIds, $warehouseIds);

        $employee->warehouses()->sync($finalWarehouseIds);

        return redirect()->route('employee.index')->withStatus('A munkavállaló adatai sikeresen mentve.');
    }
    public function getDocument($id)
    {
        try {
            $document = Document::with('doc_type')->findOrFail($id);
            
            return response()->json([
                'document_id' => $document->document_id,
                'document_type_id' => $document->document_type_id,
                'document_type_name' => $document->doc_type->name ?? null,
                'document_comment' => $document->document_comment,
                'doc_issue_date' => $document->doc_issue_date,
                'doc_expire_date' => $document->doc_expire_date,
                'doc_reminder_days' => $document->doc_reminder_days,
                'documentTypes' => DocType::orderBy('name', 'ASC')->get()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function updateDocument(Request $request, $id)
    {
        $request->validate([
            'document_type_id' => 'required|exists:doc_types,doc_type_id',
            'doc_issue_date' => 'required|date',
            'doc_expire_date' => 'required|date|after_or_equal:doc_issue_date',
            'doc_reminder_days' => 'nullable|integer|min:0',
            'document_comment' => 'nullable|string|max:500'
        ]);
        
        $data = $request->except(['_token', '_method','document_id']);
        
        try {
            $document = Document::findOrFail($id);
            $document->update($data);
            
            // Load the relationship for the response
            $document->load('doc_type');
            
            return response()->json([
                'success' => true,
                'message' => 'Document updated successfully',
                'document_id' => $document->document_id,
                'document_comment' => $document->document_comment,
                'document_type_name' => $document->doc_type->name ?? null,
                'doc_issue_date' => $document->doc_issue_date,
                'doc_expire_date' => $document->doc_expire_date,
                'doc_reminder_days' => $document->doc_reminder_days
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => ['general' => ['Update failed: ' . $e->getMessage()]]], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $employee
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Employee $employee)
{
    try {
        $employee->delete();

        return redirect()->route('employee.index')
            ->withStatus('A munkavállaló sikeresen törölve.');
    } catch (QueryException $e) {
        // Check for foreign key constraint violation
        if ($e->getCode() == '23000' && strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            return redirect()->route('employee.index')
                ->withErrors('A munkavállaló nem törölhető, mert kapcsolódó rekordok léteznek (pl. raktári kapcsolatok).');
        }

        // Re-throw any other query exception
        throw $e;
    }
}


    public function uploadEmployeeDoc(Request $request)
    {
        $file = $request->file('employee_doc');
        return $file->storeAs('/employee_docs/' . $request->employee_id, $file->getClientOriginalName(), 'document_storage');
    }


    public function saveEmployeeDocData(Request $request)
    {
        DB::table('documents')
            ->insert([
                'resource_id' => $request->employee_id,
                'document_path' => $request->doc_file,
                'document_comment' => $request->employee_doc_comment,
                'document_type_id' => $request->doc_type_id,
                'doc_issue_date' => $request->employee_doc_issue_date,
                'doc_expire_date' => $request->employee_doc_expire_date,
                'doc_reminder_days' => $request->employee_doc_reminder_days,
                'created_at' => date('Y-m-d H:i:s'),
                'document_module' => 'employee_doc'
            ]);
    }


    public function loadEmployeeDoc(Request $request)
    {
        $html = "";

        $employee = Employee::find($request->employee_id);

        foreach($employee->documents as $doc)
        {
            $html .= "<tr class='grey_background'>
                        <td><a href='" . getenv('APP_URL') . '/document_storage/' . $doc->document_path . "' target='_blank'>" . $doc->document_path . "</a></td>
                        <td>" . $doc->document_comment . "</td>
                        ";
                        

            if($doc->doc_type){
                $html .= "<td>" . $doc->doc_type->name . "</td>";
            } else {
                $html .= "<td></td>";
            }

            $html .= "
                        <td>" . $doc->doc_issue_date . "</td>
                        <td>" . $doc->doc_expire_date . "</td>
                        <td>" . $doc->doc_reminder_days . "</td>
                        <td>" . date('Y. m. d. H:i', strtotime($doc->created_at)) . "</td>
                        <td>
                            <div data-doc_id='" . $doc->document_id . "' class='doc_wrapper' >
                                <button class='btn btn-danger delete_doc' data-document_id='" . $doc->document_id . "'>
                                    <i class='nc-icon nc-simple-remove'></i> Törlés
                                </button>
                                <button class='btn btn-warning edit_doc' data-document_id='" . $doc->document_id . "'>
                                    <i class='nc-icon nc-simple-remove'></i> Szerkesztés
                                </button>
                            </div>
                        </td>
                    </tr>";
        }
        
        return $html;
    }


    public function saveEducation(Request $request)
    {
        if($request->education_id){
            $education = EmployeeEducation::find($request->education_id);
        } else {
            $education = new EmployeeEducation();
        }

        $education->employee_id = $request->employee_id;
        $education->education_name = $request->education_name;
        $education->school = $request->school;
        $education->education_start = $request->education_start;
        $education->education_end = $request->education_end;

        $education->save();
    }


    public function deleteEducation(Request $request)
    {
        EmployeeEducation::find($request->education_id)->delete();
    }


    public function loadEducations(Request $request)
    {
        $html = "";

        $employee = Employee::find($request->employee_id);

        foreach($employee->educations as $education)
        {
            $html .= "<tr class='grey_background education_row_". $education->education_id ."'>
                        <td class='education_education_name'>" . $education->education_name . "</td>
                        <td class='education_school'>" . $education->school . "</td>
                        <td class='education_education_start'>" . $education->education_start . "</td>
                        <td class='education_education_end'>" . $education->education_end . "</td>
                        <td>
                            <td>
                                <div data-education_id='" . $education->education_id . "' class='doc_wrapper' >
                                    <button type='button' class='btn btn-success edit_education' data-education_id='" . $education->education_id . "'>
                                        Szerkesztés
                                    </button>
                                </div>
                                <div data-education_id='" . $education->education_id . "' class='doc_wrapper' >
                                    <button type='button' class='btn btn-danger delete_education' data-education_id='" . $education->education_id . "'>
                                        Törlés
                                    </button>
                                </div>
                            </td>
                        </td>
                    </tr>";
        }
        
        return $html;
    }


    public function saveHoliday(Request $request)
    {
        $holiday = EmployeeHoliday::where('employee_id', $request->employee_id)
                                    ->where('holiday_year', $request->holiday_year)
                                    ->first();

        $holiday->employee_id = $request->employee_id;
        $holiday->holiday_days = $request->holiday_days;
        $holiday->holiday_remainings = $request->holiday_remainings ?? 0;

        $holiday->save();
    }
    public function updateBusinessId(Request $request)
    {
        $request->validate([
            'active_business_id' => 'required|exists:businesses,business_id'
        ]);
        
        $employee = auth()->user()->employee;
        $employee->update([
            'active_business_id' => $request->active_business_id
        ]);
        
        return response()->json(['success' => true]);
    }

    public function updateCompanyId(Request $request)
    {
        $request->validate([
            'active_company_id' => 'required|exists:companies,company_id'
        ]);
        
        $employee = auth()->user()->employee;
        $employee->update([
            'active_company_id' => $request->active_company_id
        ]);
        
        return response()->json(['success' => true]);
    }
    // Optional combined method
    public function updateActiveIds(Request $request)
    {
        $request->validate([
            'active_business_id' => 'required|exists:businesses,business_id',
            'active_company_id' => 'required|exists:companies,company_id'
        ]);
        
        $employee = auth()->user()->employee;
        $employee->update([
            'active_business_id' => $request->active_business_id,
            'active_company_id' => $request->active_company_id
        ]);
        
        return response()->json(['success' => true]);
    }
}
