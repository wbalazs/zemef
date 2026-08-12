<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Company;
use App\Models\Activity;
use App\Models\CompanyAddress;
use App\Models\BankAccount;
use App\Models\CompanyContact;
use App\Models\CompanyActivity;
use App\Models\CompanyLoan;
use App\Models\DocType;
use App\Models\Employee;
use App\Models\Bank;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\CompanyRequest;
use Illuminate\Http\Request;
use DB;
use Illuminate\Database\QueryException;

class CompanyController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Company::class);
    }

    /**
     * Display a listing of the companies
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Company $model)
    {
        Company::where('name', '--empty_row--')->delete();
        return view('companies.index', ['companies' => $model->get()]);
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
        $activities = Activity::where('status', 1)
                                        ->orderBy('name', 'ASC')
                                        ->get();
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();
        $bank_accounts = BankAccount::where('status', 1)->orderBy('bank_account_number', 'ASC')->get(); 

        $company = Company::create(['name' => '--empty_row--']);
        $company->name = '';

        return view('companies.edit', [
            'company' => $company,
            'activities' => $activities,
            'banks' => $banks,
            'doc_types' => $doc_types,
            'bank_accounts' => $bank_accounts
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\CompanyRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CompanyRequest $request, Company $model)
    {
        $company = $model->create($request->merge([
        ])->all());

        $company->activities()->sync($request->activities);

        return redirect()->route('company.index')->withStatus('A cég sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $company
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Company $company)
    {

        $activities = Activity::where('status', 1)
                                        ->orderBy('name', 'ASC')
                                        ->get();
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();
        $bank_accounts = BankAccount::where('status', 1)->orderBy('bank_account_number', 'ASC')->get();

        return view('companies.edit', [
            'company' => $company,
            'activities' => $activities,
            'banks' => $banks,
            'bank_accounts' => $bank_accounts,
            'doc_types' => $doc_types
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CompanyRequest $request, Company $company)
    {
        $company->update(
            array_merge($request->all(), ['status' => $request->status])
        );

        $company->activities()->sync($request->activities);
        $company->bank_accounts()->sync($request->bank_accounts);

        return redirect()->route('company.index')->withStatus('A cég adatai sikeresen mentve.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Company $company)
    {
        try {
            $company->delete();
            return redirect()->route('company.index')->withStatus('A cég sikeresen törölve.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                return redirect()->route('company.index')->withErrors('A cég nem törölhető, mert más adatok kapcsolódnak hozzá (pl. tevékenységek).');
            }
            throw $e;
        }
    }


    public function saveCompanyEmployee(Request $request)
    {
        $employee = Employee::find($request->employee_id);

        if($request->card == 'true'){
            $employee->card = 1;
            $employee->card_expiration_year = $request->card_expiration_year;
            $employee->card_expiration_month = $request->card_expiration_month;
            $employee->card_limit = $request->card_limit;
        } else {
            $employee->card = null;
            $employee->card_expiration_year = null;
            $employee->card_expiration_month = null;
            $employee->card_limit = null;
        }

        $employee->save();
    }


    public function saveAddress(Request $request)
    {
        if($request->address_id){
            $address = CompanyAddress::find($request->address_id);
        } else {
            $address = new CompanyAddress();
        }

        $zip_one = Zip::where('id', $request->zip)->first();
        $city_one = City::where('id', $request->city)->first();

        $address->company_id = $request->company_id;
        $address->zip = $zip_one->name;
        $address->zip_id = $zip_one->id;
        $address->city = $city_one->name;
        $address->city_id = $city_one->id;
        $address->address = $request->address;
        $address->address_type = $request->address_type;
        $address->floor = $request->floor;
        $address->door = $request->door;

        $address->save();
    }


    public function deleteAddress(Request $request)
    {
        CompanyAddress::find($request->address_id)->delete();
    }


    public function loadAddresses(Request $request)
    {
        $html = "";

        $company = Company::find($request->company_id);

        foreach($company->addresses as $address)
        {
            $html .= "<tr class='grey_background address_row_". $address->address_id ."'>
                        <td class='address_type'>" . $address->address_type . "</td>
                        <td class='address_zip' data-id='".$address->zip_id."'>" . $address->zip . "</td>
                        <td class='address_city' data-id='".$address->city_id."'>" . $address->city . "</td>
                        <td class='address_address'>" . $address->address . "</td>
                        <td class='address_floor'>" . $address->floor . "</td>
                        <td class='address_door'>" . $address->door . "</td>
                        <td>
                            <div style='display: inline-block;' data-address_id='" . $address->address_id . "' class='doc_wrapper'>
                                <button type='button' class='btn btn-success edit_address' data-address_id='" . $address->address_id . "'>
                                    <i class='fa fa-edit'></i>
                                </button>
                            </div>
                            <div style='display: inline-block;' data-address_id='" . $address->address_id . "' class='doc_wrapper'>
                                <button type='button' class='btn btn-danger delete_address' data-address_id='" . $address->address_id . "'>
                                    <i class='fa fa-trash'></i>
                                </button>
                            </div>
                        </td>
                    </tr>";
        }
        
        return $html;
    }


    public function saveContact(Request $request)
    {
        if($request->contact_id){
            $contact = CompanyContact::find($request->contact_id);
        } else {
            $contact = new CompanyContact();
        }

        $contact->company_id = $request->company_id;
        $contact->firstname = $request->firstname;
        $contact->lastname = $request->lastname;
        $contact->phone = $request->phone;
        $contact->email = $request->email;
        $contact->rank = $request->rank;
        $contact->comment = $request->comment;

        $contact->save();
    }


    public function deleteContact(Request $request)
    {
        CompanyContact::find($request->contact_id)->delete();
    }


    public function loadContacts(Request $request)
    {
        $html = "";

        $company = Company::find($request->company_id);

        foreach($company->contacts as $contact)
        {
            $html .= "<tr class='grey_background contact_row_". $contact->contact_id ."'>
                        <td class='contact_lastname'>" . $contact->lastname . "</td>
                        <td class='contact_firstname'>" . $contact->firstname . "</td>
                        <td class='contact_phone'>" . $contact->phone . "</td>
                        <td class='contact_email'>" . $contact->email . "</td>
                        <td class='contact_rank'>" . $contact->rank . "</td>
                        <td class='contact_comment'>" . $contact->comment . "</td>
                        <td>
                            <div style='display: inline-block;' data-contact_id='" . $contact->contact_id . "' class='doc_wrapper'>
                                <button type='button' class='btn btn-success edit_contact' data-contact_id='" . $contact->contact_id . "'>
                                    <i class='fa fa-edit'></i>
                                </button>
                            </div>
                            <div style='display: inline-block;' data-contact_id='" . $contact->contact_id . "' class='doc_wrapper'>
                                <button type='button' class='btn btn-danger delete_contact' data-contact_id='" . $contact->contact_id . "'>
                                    <i class='fa fa-trash'></i>
                                </button>
                            </div>
                        </td>
                    </tr>";
        }
        
        return $html;
    }


    public function uploadCompanyDoc(Request $request)
    {
        $file = $request->file('company_doc');
        return $file->storeAs('/company_docs/' . $request->company_id, $file->getClientOriginalName(), 'document_storage');
    }


    public function saveCompanyDocData(Request $request)
    {
        DB::table('documents')
            ->insert([
                'resource_id' => $request->company_id,
                'document_path' => $request->doc_file,
                'document_comment' => $request->company_doc_comment,
                'document_type_id' => $request->doc_type_id,
                'doc_issue_date' => $request->company_doc_issue_date,
                'doc_expire_date' => $request->company_doc_expire_date,
                'doc_reminder_days' => $request->company_doc_reminder_days,
                'created_at' => date('Y-m-d H:i:s'),
                'document_module' => 'company_doc'
            ]);
    }


    public function loadCompanyDoc(Request $request)
    {
        $html = "";

        $company = Company::find($request->company_id);

        foreach($company->documents as $doc)
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
                            </div>
                        </td>
                    </tr>";
        }
        
        return $html;
    }


    public function saveLoan(Request $request)
    {
        if($request->loan_id){
            $loan = CompanyLoan::find($request->loan_id);
        } else {
            $loan = new CompanyLoan();
        }

        $loan->company_id = $request->company_id;
        $loan->bank_id = $request->bank_id;
        $loan->amount = $request->amount;
        $loan->loan_end_date = $request->loan_end_date;
        $loan->loan_monthly = $request->loan_monthly;
        $loan->loan_type = $request->loan_type;
        $loan->private = $request->private;

        $loan->save();
    }


    public function deleteLoan(Request $request)
    {
        CompanyLoan::find($request->loan_id)->delete();
    }


    public function loadLoans(Request $request)
    {
        $html = "";

        $company = Company::find($request->company_id);

        foreach($company->loans as $loan)
        {
            $html .= "<tr class='grey_background loan_row_". $loan->loan_id ."'>
                        <td data-bank_id='" . $loan->bank->bank_id . "' class='loan_bank_name'>" . $loan->bank->name . "</td>
                        <td class='loan_private'>";

                        if($loan->private){
                            $html .= "Igen";
                        } else {
                            $html .= "Nem";
                        }
                        
            $html .= "<td class='loan_type'>". $loan->loan_type . "</td>
                        <td class='loan_amount'>". $loan->amount . "</td>
                        <td class='loan_end_date'>". $loan->loan_end_date . "</td>
                        <td class='loan_monthly'>". $loan->loan_monthly . "</td>
                        <td>
                            <td>
                                <div data-loan_id='" . $loan->loan_id . "' class='doc_wrapper' >
                                    <button type='button' class='btn btn-success edit_loan' data-loan_id='" . $loan->loan_id . "'>
                                        Szerkesztés
                                    </button>
                                </div>
                                <div data-loan_id='" . $loan->loan_id . "' class='doc_wrapper' >
                                    <button type='button' class='btn btn-danger delete_loan' data-loan_id='" . $loan->loan_id . "'>
                                        Törlés
                                    </button>
                                </div>
                            </td>
                        </td>
                    </tr>";
        }
        
        return $html;
    }


    public function getCompanyLoansProgress(Request $request)
    {
        $currentDate = Carbon::now();
        $selectedLoans = $request->input('selected_loans');
        
        $loans = DB::table('company_loans')
            ->where('company_id', $request->company_id) // Szűrés cég alapján
            ->whereIn('loan_id', $selectedLoans) // Szűrés kiválasztott hitek alapján
            ->get();

        $loanData = $loans->map(function ($loan) use ($currentDate) {
            $loanStartDate = Carbon::parse($loan->created_at);
            $loanEndDate = Carbon::parse($loan->loan_end_date);
            
            $loan->days_elapsed = $currentDate->diffInDays($loanStartDate);
            $loan->total_days = $loanStartDate->diffInDays($loanEndDate);
            $loan->progress_percentage = ($loan->total_days > 0) ? ($loan->days_elapsed / $loan->total_days) * 100 : 0;

            return $loan;
        });

        return response()->json($loanData);
    }
}
