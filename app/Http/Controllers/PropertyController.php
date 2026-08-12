<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Property;
use App\Models\ZImage;
use App\Models\DocType;
use App\Models\Attribute;
use App\Models\Business;
use App\Models\Tool;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\User;
use App\Models\PropertyContact;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\PropertyRequest;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Http\Request;
use DB;
use File;



class PropertyController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Property::class);
    }

    /**
     * Display a listing of the properties
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Property $model)
    {
        Property::where('name', '--empty_row--')->delete();
        return view('properties.index', ['properties' => $model->get()]);
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
        $attributes = Attribute::where('status', 1)->orderBy('name', 'ASC')->get();
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $tools = Tool::orderBy('name', 'ASC')->get();
        $users = User::orderBy('name', 'ASC')->get();
        $businesses = Business::orderBy('name', 'ASC')->get();

        $property = Property::create(['name' => '--empty_row--']);
        $property->name = '';

        return view('properties.edit', [
            'property' => $property,
            'attributes' => $attributes,
            'doc_types' => $doc_types,
            'tools' => $tools,
            'users' => $users,
            'businesses' => $businesses
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\PropertyRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PropertyRequest $request, Property $model)
    {
        $zip = Zip::where('id', $request->zip_id)->first();
        $city = City::where('id', $request->city_id)->first();

        $property = $model->create($request->merge([
                'zip' => $zip->name ?? null,
                'zip_id' => $zip->id ?? 0,
                'city' => $city->name ?? null,
                'city_id' => $city->id ?? 0
        ])->all());

        return redirect()->route('property.index')->withStatus('Az eszköz sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $property
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Property $property)
    {
        $attributes = Attribute::where('status', 1)->orderBy('name', 'ASC')->get();
        $doc_types = DocType::orderBy('name', 'ASC')->get();
        $tools = Tool::orderBy('name', 'ASC')->get();
        $users = User::orderBy('name', 'ASC')->get();
        $businesses = Business::orderBy('name', 'ASC')->get();


        return view('properties.edit', [
            'property' => $property,
            'attributes' => $attributes,
            'tools' => $tools,
            'users' => $users,
            'doc_types' => $doc_types,
            'businesses' => $businesses
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PropertyRequest $request, Property $property)
    {
        $zip = Zip::where('id', $request->zip_id)->first();
        $city = City::where('id', $request->city_id)->first();
        $reservation = new \stdClass();


        if($request->is_own == 0){
            $reservation->reservation_type = 0;
            $reservation->reservation_to_date = null;
            $reservation->reservation_expire_in_days = 0;
        }else{
            $reservation->reservation_type = $request->reservation_type;
            $reservation->reservation_to_date = $request->reservation_to_date;
            $reservation->reservation_expire_in_days = $request->reservation_expire_in_days;
        }
        
        $property->update(
            array_merge(
                $request->all(),
                [
                    'is_own' => $request->is_own,
                    'is_business' => $request->is_business,
                    'is_franchise' => $request->is_franchise,
                    'status' => $request->status,
                    'zip' => $zip->name ?? null,
                    'zip_id' => $zip->id ?? 0,
                    'city' => $city->name ?? null,
                    'city_id' => $city->id ?? 0,
                    'reservation_type' => $reservation->reservation_type,
                    'reservation_to_date' => $reservation->reservation_to_date,
                    'reservation_expire_in_days' => $reservation->reservation_expire_in_days

                ]
            )
        );

        DB::transaction(function() use ($request, $property) {
            // Szinkronizáljuk az attributes értékeket a kiválasztott elemek alapján
            $property->attributes()->sync($request->get('attributes', []));
        
            // Ha vannak egyedi értékek az attributes-hez, ezeket itt kezeljük
            foreach ($request->all() as $attr => $param) {
                if (substr($attr, 0, 10) == 'attribute_') {
                    $existing = DB::table('attribute_property')
                        ->where('attribute_attribute_id', substr($attr, 10))
                        ->where('property_property_id', $property->property_id)
                        ->first();
        
                    if ($existing) {
                        DB::table('attribute_property')
                            ->where('attribute_attribute_id', substr($attr, 10))
                            ->where('property_property_id', $property->property_id)
                            ->update(['value' => $param]);
                    } 
                }
            }
        });
        

        return redirect()->route('property.index')->withStatus('Az eszköz adatai sikeresen mentve.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $property
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('property.index')->withStatus('Az eszköz sikeresen törölve.');
    }


    public function uploadPropertyDoc(Request $request)
    {
        $file = $request->file('property_doc');
        return $file->storeAs('/property_docs/' . $request->property_id, $file->getClientOriginalName(), 'document_storage');
    }


    public function savePropertyDocData(Request $request)
    {
        DB::table('documents')
            ->insert([
                'resource_id' => $request->property_id,
                'document_path' => $request->doc_file,
                'document_comment' => $request->property_doc_comment,
                'document_type_id' => $request->doc_type_id,
                'doc_issue_date' => $request->property_doc_issue_date,
                'doc_expire_date' => $request->property_doc_expire_date,
                'doc_reminder_days' => $request->property_doc_reminder_days,
                'created_at' => date('Y-m-d H:i:s'),
                'document_module' => 'property_doc'
            ]);
    }


    public function autoSavePropertyImage(Request $request)
    {
        $image = new ZImage();
        $image->resource_id = $request->property_id;
        $image->image_path = $request->image;
        $image->image_date = $request->image_date;
        $image->created_at = date('Y-m-d H:i:s');
        $image->module = 'property';
        $image->save();
    }



    public function loadPropertyDoc(Request $request)
    {
        $html = "";

        $property = Property::find($request->property_id);

        foreach($property->documents as $doc)
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


    public function uploadPropertyImage(Request $request)
    {
        $file = $request->file('property_image');
        $path = public_path('/document_storage/property_images/' . $request->property_id . '/');

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

        return $request->property_id . '/' . $file->getClientOriginalName();
    }


    public function loadPropertyGallery(Request $request)
    {
        $html = '';

        $property = Property::find($request->property_id);
        
            $html .= '<div class="delete_buttons">';

            foreach($property->property_images as $img)
            {
                $html .= '<div class="hidden_image_wrapper" data-img_id="' . $img->image_id . '">
                            <button type="button" data-img_id="'. $img->image_id . '" class="btn delete_img btn-danger btn-sm btn-icon waves-effect waves-themed"><i data-img_id="'.$img->image_id.'" class="fa fa-times"></i></button>
                            </div>';
            }
        
            $html .= '</div>';
        

        $html .= '<div data-lightGallery="1" id="lightgallery_property_images" class="">';

        foreach($property->property_images as $img)
        {
                $html .= '<div data-sub-html="" data-img_id="' . $img->image_id . '" class="property_img_wrapper" data-src="' . env('PUBLIC_URL') . '/document_storage/' . $img->image_path . '"><img data-img_id="' . $img->image_id . '" class="img-responsive property_image_img" src="' . env('PUBLIC_URL') . '/document_storage/property_images/' . $img->image_path . '" alt=""><span class="image_description">' . date('Y. m. d.', strtotime($img->image_date)) . '</span></div>';
        }
        $html .= '</div>';
            
        return $html;
    }


    public function deleteImg(Request $request)
    {
        $image = ZImage::find($request->img_id);

        if(file_exists(public_path() . '/document_storage/property_images/' . $image->image_path))
        {
            unlink(public_path() . '/document_storage/property_images/' . $image->image_path);
        }

        $image->delete();
    }


    public function saveContact(Request $request)
    {
        if($request->contact_id){
            $contact = PropertyContact::find($request->contact_id);
        } else {
            $contact = new PropertyContact();
        }

        $contact->property_id = $request->property_id;
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
        PropertyContact::find($request->contact_id)->delete();
    }


    public function loadContacts(Request $request)
    {
        $html = "";

        $property = Property::find($request->property_id);

        foreach($property->contacts as $contact)
        {
            $html .= "<tr class='grey_background contact_row_". $contact->contact_id ."'>
                        <td>" . $contact->lastname . "</td>
                        <td>" . $contact->firstname . "</td>
                        <td>" . $contact->phone . "</td>
                        <td>" . $contact->email . "</td>
                        <td>" . $contact->rank . "</td>
                        <td>" . $contact->comment . "</td>
                        <td>
                            <td>
                                <div data-contact_id='" . $contact->contact_id . "' class='doc_wrapper' >
                                    <button type='button' class='btn btn-success edit_contact' data-contact_id='" . $contact->contact_id . "'>
                                        Szerkesztés
                                    </button>
                                </div>
                                <div data-contact_id='" . $contact->contact_id . "' class='doc_wrapper' >
                                    <button type='button' class='btn btn-danger delete_contact' data-contact_id='" . $contact->contact_id . "'>
                                        Törlés
                                    </button>
                                </div>
                            </td>
                        </td>
                    </tr>";
        }
        
        return $html;
    }
}
