<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Posting;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\NAVFuel;
use App\Models\NAVStandard;
use App\Models\Vehicle;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\PostingRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use PDF;



class PostingController extends Controller
{
    public function __construct()
    {
        //////$this->authorizeResource(Posting::class);
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Posting $model)
    {
        Posting::where('from_to', '--empty_row--')->delete();
        $postings = Posting::where('employee_id', Auth::user()->employee?->employee_id)->orderBy('posting_date', 'ASC')->get();
        return view('postings.index', ['postings' => $postings]);
    }

    
    public function create()
    {
        $posting = Posting::create(['from_to' => '--empty_row--']);
        $posting->from_to = "";

        $employees = Employee::where('status', 1)
                                ->orderBy('lastname', 'ASC')
                                ->orderBy('firstname', 'ASC')
                                ->get();
        $current_employee = Auth::user()->employee?->employee_id;

        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();
        $vehicles = Vehicle::where('status', 1)->where('responsible_id',$current_employee)->orderBy('name', 'ASC')->get();


        return view('postings.edit', [
            'posting' => $posting,
            'employees' => $employees,
            'customers' => $customers,
            'vehicles' => $vehicles,
        ]);
    }


    public function store(PostingRequest $request, Posting $model)
    {
        $posting = $model->create($request->merge([
            
        ])->all());

        return redirect()->route('posting.index')->withStatus('A kiküldetés sikeresen létrehozva.');
    }

    
    public function edit(Posting $posting)
    {
        $employees = Employee::where('status', 1)
                                ->orderBy('lastname', 'ASC')
                                ->orderBy('firstname', 'ASC')
                                ->get();

        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();
        $vehicles = Vehicle::where('status', 1)->orderBy('name', 'ASC')->get();


        return view('postings.edit', [
            'posting' => $posting,
            'employees' => $employees,
            'customers' => $customers,
            'vehicles' => $vehicles,
        ]);
    }

    public function update(PostingRequest $request, Posting $posting)
    {
        $posting->update(
            array_merge($request->all(),
                []
            )
        );

        return redirect()->route('posting.index')->withStatus('A kiküldetés sikeresen mentve.');
    }

    
    public function destroy(Posting $posting)
    {
        $posting->delete();

        return redirect()->route('posting.index')->withStatus('A kiküldetés sikeresen törölve.');
    }


    public function printPosting(Request $request)
    {
        array_map('unlink', glob(public_path() . '/pdf/postings/*'));

        $file_name = 'kikuldetesi_rendelveny.pdf';

        $pdf = \App::make('dompdf.wrapper');


        $data['year'] = $request->year;
        $data['month'] = Carbon::create(null, $request->month)->locale('hu')->monthName;
        $data['postings'] = Posting::where('employee_id', Auth::user()->employee?->employee_id)->orderBy('posting_date', 'ASC')->get();

        $data['total_km'] = 0;

        foreach($data['postings'] as $posting){
            $data['total_km'] += $posting->distance;
        }

        $data['nav_fuel'] = NAVFuel::where('year', $request->year)
                                    ->where('month', $request->month)
                                    ->first();
        $data['nav_standard'] = NAVStandard::find(1);

        $fuel_key = (auth()->user()->employee?->vehicle->fuel == 'benzin') ? 'benzin95' : 'dizel';

        $data['fuel_price'] = $data['nav_fuel']->$fuel_key;


        if(auth()->user()->employee?->vehicle->fuel == 'benzin'){
            if(auth()->user()->employee?->vehicle->cylinder < 1001){
                $cylinder = 1000;
            } elseif(auth()->user()->employee?->vehicle->cylinder > 1000 && auth()->user()->employee?->vehicle->cylinder < 1501) {
                $cylinder = 1500;
            } elseif(auth()->user()->employee?->vehicle->cylinder > 1500 && auth()->user()->employee?->vehicle->cylinder < 2001) {
                $cylinder = 2000;
            } elseif(auth()->user()->employee?->vehicle->cylinder > 2000 && auth()->user()->employee?->vehicle->cylinder < 3001) {
                $cylinder = 3000;
            } else {
                $cylinder = 5000;
            }
        } else {
            if(auth()->user()->employee?->vehicle->cylinder < 1501){
                $cylinder = 1500;
            } elseif(auth()->user()->employee?->vehicle->cylinder > 1500 && auth()->user()->employee?->vehicle->cylinder < 2001) {
                $cylinder = 2000;
            } elseif(auth()->user()->employee?->vehicle->cylinder > 2000 && auth()->user()->employee?->vehicle->cylinder < 3001) {
                $cylinder = 3000;
            } else {
                $cylinder = 5000;
            }
        }


        $standard_key = auth()->user()->employee?->vehicle->fuel . '_' . $cylinder;

        $data['nav_standard_consumption'] = $data['nav_standard']->$standard_key;


        $data['total_fuel_price'] = $data['total_km']/100*$data['nav_standard']->$standard_key*$data['fuel_price'];
        $data['total_amort_price'] = $data['total_km']*$data['nav_standard']->amort;
        $data['total_price'] = $data['total_amort_price']+$data['total_fuel_price'];

        $pdf->setOptions(['defaultFont' => 'sans-serif', 'isRemoteEnabled' => TRUE]);
        $pdf->loadView('pdf.posting', ['data' => $data]);
        $pdf->setPaper('a4', 'profile');
        $pdf->save(public_path() . '/pdf/postings/' . $file_name);

        return $file_name;
    }


   
}
