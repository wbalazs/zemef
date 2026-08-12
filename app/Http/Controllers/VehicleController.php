<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
namespace App\Http\Controllers;

use App\Tag;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\DocType;
use App\Models\ZImage;
use App\Models\VehicleCondition;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\VehicleRequest;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use DB;
use File;



class VehicleController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Vehicle::class);
    }

    /**
     * Display a listing of the vehicles
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Vehicle $model)
    {
        Vehicle::where('name', '--empty_row--')->delete();
        return view('vehicles.index', ['vehicles' => $model->get()]);
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
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();

        $vehicle = Vehicle::create(['name' => '--empty_row--']);
        $vehicle->name = '';

        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'employees' => $employees,
            'customers' => $customers,
            'doc_types' => $doc_types,
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\VehicleRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(VehicleRequest $request, Vehicle $model)
    {
        $vehicle = $model->create($request->merge([
        ])->all());

        return redirect()->route('vehicle.index')->withStatus('A jármű sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $vehicle
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Vehicle $vehicle)
    {
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();
        $customers = Customer::where('status', 1)->orderBy('name', 'ASC')->get();

        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'employees' => $employees,
            'customers' => $customers,
            'doc_types' => $doc_types,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $vehicle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update(
            array_merge($request->all(), ['status' => $request->status])
        );

        return redirect()->route('vehicle.index')->withStatus('A jármű adatai sikeresen mentve.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $vehicle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('vehicle.index')->withStatus('A jármű sikeresen törölve.');
    }


    public function uploadVehicleDoc(Request $request)
    {
        $file = $request->file('vehicle_doc');
        return $file->storeAs('/vehicle_docs/' . $request->vehicle_id, $file->getClientOriginalName(), 'document_storage');
    }


    public function saveVehicleDocData(Request $request)
    {
        DB::table('documents')
            ->insert([
                'resource_id' => $request->vehicle_id,
                'document_path' => $request->doc_file,
                'document_comment' => $request->vehicle_doc_comment,
                'document_type_id' => $request->doc_type_id,
                'doc_issue_date' => $request->vehicle_doc_issue_date,
                'doc_expire_date' => $request->vehicle_doc_expire_date,
                'doc_reminder_days' => $request->vehicle_doc_reminder_days,
                'created_at' => date('Y-m-d H:i:s'),
                'document_module' => 'vehicle_doc'
            ]);
    }


    public function saveCondition(Request $request)
    {
        if($request->condition_id){
            $condition = VehicleCondition::find($request->condition_id);
        } else {
            $condition = new VehicleCondition();
        }

        $condition->condition_description = $request->condition_description;
        $condition->condition_date = $request->condition_date;
        $condition->vehicle_id = $request->vehicle_id;

        $condition->save();
    }


    public function autoSaveVehicleImage(Request $request)
    {
        $image = new ZImage();
        $image->resource_id = $request->vehicle_id;
        $image->image_path = $request->image;
        $image->image_date = $request->image_date;
        $image->created_at = date('Y-m-d H:i:s');
        $image->module = 'vehicle';
        $image->save();
    }


    public function deleteCondition(Request $request)
    {
        VehicleCondition::find($request->condition_id)->delete();
    }


    public function loadConditions(Request $request)
    {
        $html = "";

        $vehicle = Vehicle::find($request->vehicle_id);

        foreach($vehicle->conditions as $condition)
        {
            $html .= "<tr class='grey_background'>
                        <td>" . $condition->condition_description . "</td>
                        <td>" . $condition->condition_date . "</td>
                        <td>
                            <td>
                                <div data-condition_id='" . $condition->condition_id . "' class='doc_wrapper' >
                                    <button type='button' class='btn btn-success edit_condition' data-condition_id='' . $condition->condition_id . ''>
                                        Szerkesztés
                                    </button>
                                </div>
                                <div data-condition_id='' . $condition->condition_id . '' class='doc_wrapper' >
                                    <button type='button' class='btn btn-danger delete_condition' data-condition_id='' . $condition->condition_id . ''>
                                        Törlés
                                    </button>
                                </div>
                            </td>
                        </td>
                    </tr>";
        }
        
        return $html;
    }


    public function loadVehicleDoc(Request $request)
    {
        $html = "";

        $vehicle = Vehicle::find($request->vehicle_id);

        foreach($vehicle->documents as $doc)
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


    public function uploadVehicleImage(Request $request)
    {
        $file = $request->file('vehicle_image');
        $path = public_path('/document_storage/vehicle_images/' . $request->vehicle_id . '/');

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }

        $image_resize = Image::make($file->getRealPath());              

        // $w = $image_resize->getWidth();
        // $h = $image_resize->getHeight();
        // $ratio = (1200/$w);
        // $height = ($h*$ratio);

        // $image_resize->resize(1200, $height);

        $image_resize->save($path . $file->getClientOriginalName());

        return $request->vehicle_id . '/' . $file->getClientOriginalName();
    }


    public function loadVehicleGallery(Request $request)
    {
        $html = '';

        $vehicle = Vehicle::find($request->vehicle_id);
        
            $html .= '<div class="delete_buttons">';

            foreach($vehicle->vehicle_images as $img)
            {
                $html .= '<div class="hidden_image_wrapper" data-img_id="' . $img->image_id . '">
                            <button type="button" data-img_id="'. $img->image_id . '" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed"><i data-img_id="'.$img->image_id.'" class="fa fa-times"></i></button>
                            </div>';
            }
        
            $html .= '</div>';
        

        $html .= '<div data-lightGallery="1" id="lightgallery_vehicle_images" class="">';

        foreach($vehicle->vehicle_images as $img)
        {
                $html .= '<div data-sub-html="" data-img_id="' . $img->image_id . '" class="vehicle_img_wrapper" data-src="' . env('PUBLIC_URL') . '/document_storage/' . $img->image_path . '"><img data-img_id="' . $img->image_id . '" class="img-responsive vehicle_image_img" src="' . env('PUBLIC_URL') . '/document_storage/vehicle_images/' . $img->image_path . '" alt=""><span class="image_description">' . date('Y. m. d.', strtotime($img->image_date)) . '</span></div>';
        }
        $html .= '</div>';
            
        return $html;
    }


    public function deleteImg(Request $request)
    {
        $image = ZImage::find($request->img_id);

        if(file_exists(public_path() . '/document_storage/vehicle_images/' . $image->image_path))
        {
            unlink(public_path() . '/document_storage/vehicle_images/' . $image->image_path);
        }

        $image->delete();
    }


    public function deleteDoc(Request $request)
    {
        $doc = DB::table('documents')->where('document_id', $request->doc_id)->first();

        if(file_exists(public_path() . '/document_storage/' . $doc->document_path))
        {
            unlink(public_path() . '/document_storage/' . $doc->document_path);
        }

        $doc = DB::table('documents')->where('document_id', $request->doc_id)->delete();
    }
}
