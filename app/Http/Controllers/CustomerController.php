<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\CustomerAddress;
use App\Models\CustomerContact;
use App\Models\Address\Zip;
use App\Models\Address\City;
use App\Models\Payment;
use App\Models\ProductStatus;
use App\Category;
use Carbon\Carbon;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class CustomerController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Customer::class);
    }

    /**
     * Display a listing of the customers
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Customer $model)
    {
        Customer::where('name', '--empty_row--')->delete();
        return view('customers.index', ['customers' => $model->get()]);
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
        $customer_types = CustomerType::where('status', 1)
                                        ->orderBy('name', 'ASC')
                                        ->whereNull('parent_id')
                                        ->get();
        $customer_types = $customer_types->map(function ($customer_type) {
            $customer_type->depth = 0;
            return $this->setCustomerTypeDepth($customer_type);
        });

        $payments = Payment::where('status', 1)
                                ->orderBy('name', 'ASC')
                                ->get();

        $active_product_statuses = ProductStatus::with('vatType')
                                ->where('status', 1)
                                ->orderBy('name', 'ASC')
                                ->get();

        $customer = Customer::create(['name' => '--empty_row--']);
        $customer->name = '';
        $customer->load(['customer_markings.productStatus.vatType']);

        return view('customers.edit', [
            'customer' => $customer,
            'customer_types' => $customer_types,
            'payments' => $payments,
            'active_product_statuses' => $active_product_statuses,
            'sales' => []
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\CustomerRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CustomerRequest $request, Customer $model)
    {
        $customer = $model->create(
            array_merge($request->all(), ['status' => $request->status, 'terms_accept' => $request->terms_accept, 'marketing_accept' => $request->marketing_accept])
        );

        $customer->customer_types()->sync($request->customer_type_ids);

        // Fizetési módok szinkronizálása, üres értékek kiszűrése
        if ($request->has('payment_method_ids')) {
            $payment_method_ids = array_filter($request->payment_method_ids); // Kiszüri az üres értékeket
            $customer->payment_methods()->sync($payment_method_ids);
        }

        return redirect()->route('customer.index')->withStatus('Az ügyfél sikeresen létrehozva.');
    }

    public function storeFromModal(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:30',
            'zip' => 'required|integer|exists:zips,id',
            'city' => 'required|integer|exists:cities,id',
            'address' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Új ügyfél mentése
            $customer = Customer::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'status' => 1
            ]);

            // Ha van cím adat, akkor hozzuk létre a címet
            if (!empty($validated['zip']) && !empty($validated['city']) && !empty($validated['address'])) {
                $zip = \App\Models\Address\Zip::find($validated['zip']);
                $city = \App\Models\Address\City::find($validated['city']);

                $customer->addresses()->create([
                    'zip' => $zip->name,
                    'zip_id' => $zip->id,
                    'city' => $city->name,
                    'city_id' => $city->id,
                    'address' => $validated['address'],
                    'address_type' => 'Lakcím'
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => $customer, 'message' => 'Az ügyfél sikeresen létrehozva.'], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a mentés során.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show the form for editing the specified item
     *
     * @param  \App\Item  $customer
     * @param  \App\Tag   $tagModel
     * @param  \App\Category $categoryModel
     * @return \Illuminate\View\View
     */
    public function edit(Customer $customer)
    {
        // Ügyfél típusok lekérdezése
        $customer_types = CustomerType::where('status', 1)
                                    ->orderBy('name', 'ASC')
                                    ->whereNull('parent_id')
                                    ->get();
        $customer_types = $customer_types->map(function ($customer_type) {
            $customer_type->depth = 0;
            return $this->setCustomerTypeDepth($customer_type);
        });

        // Fizetési módok lekérdezése
        $payments = Payment::where('status', 1)
                        ->orderBy('name', 'ASC')
                        ->get();

        $active_product_statuses = ProductStatus::with('vatType')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        $customer->load(['customer_markings.productStatus.vatType']);

         // A kiválasztott fizetési módok
        $selected_payment_method_ids = $customer->payment_methods()?->pluck('payment_method_payment_method_id')->toArray();

        // Az ügyfél vásárlásainak egyesítése
        $sales = $this->customer_sales($customer->customer_id);

        return view('customers.edit', [
            'customer' => $customer,
            'customer_types' => $customer_types,
            'payments' => $payments,
            'active_product_statuses' => $active_product_statuses,
            'selected_payment_method_ids' => $selected_payment_method_ids,
            'sales' => $sales
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update(
            array_merge($request->all(), ['status' => $request->status, 'terms_accept' => $request->terms_accept, 'marketing_accept' => $request->marketing_accept])
        );

        $customer->customer_types()->sync($request->customer_type_ids);

        // Fizetési módok szinkronizálása, üres értékek kiszűrése
        if ($request->has('payment_method_ids')) {
            $payment_method_ids = array_filter($request->payment_method_ids); // Kiszüri az üres értékeket
            $customer->payment_methods()->sync($payment_method_ids);
        }

        return redirect()->route('customer.index')->withStatus('Az ügyfél adatai sikeresen mentve.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return redirect()->route('customer.index')->withStatus('Az ügyfél sikeresen törölve.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('customer.index')->withErrors('Nem lehet törölni az ügyfelet, mert kapcsolódó bizonylatai vannak.');
        }
    }


    public function setCustomerTypeDepth($customer_type, $depth = 0)
    {
        $customer_type->depth = $depth;

        if ($customer_type->children) {
            foreach ($customer_type->children as $child) {
                $this->setCustomerTypeDepth($child, $depth + 1);
            }
        }

        return $customer_type;
    }


    public function saveAddress(Request $request)
    {
        if($request->address_id){
            $address = CustomerAddress::find($request->address_id);
        } else {
            $address = new CustomerAddress();
        }

        $zip_one = Zip::where('id', $request->zip)->first();
        $city_one = City::where('id', $request->city)->first();

        $address->customer_id = $request->customer_id;
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
        CustomerAddress::find($request->address_id)->delete();
    }


    public function loadAddresses(Request $request)
    {
        $html = "";

        $customer = Customer::find($request->customer_id);

        foreach($customer->addresses as $address)
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
            $contact = CustomerContact::find($request->contact_id);
        } else {
            $contact = new CustomerContact();
        }

        $contact->customer_id = $request->customer_id;
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
        CustomerContact::find($request->contact_id)->delete();
    }


    public function customerCardReg(Request $request)
    {


        return view('customers.customer_card', [
            
        ]);
    }


    public function loadContacts(Request $request)
    {
        $html = "";

        $customer = Customer::find($request->customer_id);

        foreach($customer->contacts as $contact)
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


    public function getCustomerData(Request $request)
    {
        $customer = Customer::with(['customer_markings.productStatus'])->find($request->customer_id);

        $result = new \StdClass();
        $result->customer = $customer;
        if($customer->is_company) {
            $result->address = $customer->addresses->where('address_type', 'Székhely')->first();
        } else {
            $result->address = $customer->addresses->where('address_type', 'Lakcím')->first();
        }

        $result->customer_markings = $customer->customer_markings->map(function ($marking) {
            return [
                'id' => $marking->id,
                'name' => $marking->name,
                'default' => (int) $marking->default,
                'status' => (int) $marking->status,
                'product_status_id' => $marking->product_status_id,
                'price' => optional($marking->productStatus)->price,
                'vat_type_id' => optional($marking->productStatus)->vat_type_id,
            ];
        })->values();

        return json_encode($result);
    }

    public function getCitiesByZip($zip) {
        $cities = DB::table('zips')
                   ->join('zip_city', 'zips.id', '=', 'zip_city.zip_id')
                   ->join('cities', 'zip_city.city_id', '=', 'cities.id')
                   ->where('zips.id', $zip)
                   ->select('zip_city.zip_id', 'cities.id as city_id', 'cities.name')
                   ->get();
    
        return response()->json($cities);
    }

    public function getZips(Request $request) {
        $term = $request->input('term');
    
        $zips = DB::table('zips')
                  ->where('name', 'LIKE', '%' . $term . '%')
                  ->select('id', 'name')
                  ->get();
        
        return response()->json($zips);
    }
    
    public function getCities(Request $request) {
        $term = $request->input('term');
    
        $cities = DB::table('cities')
                  ->where('name', 'LIKE', '%' . $term . '%')
                  ->select('id', 'name')
                  ->get();
        
        return response()->json($cities);
    }
    
public function customer_sales($id)
{
    $purchases = DB::table('purchase_tickets')
        ->join('purchase_ticket_items', 'purchase_tickets.purchase_ticket_id', '=', 'purchase_ticket_items.purchase_ticket_id')
        ->select(
            'purchase_tickets.purchase_ticket_id as id',
            'purchase_tickets.ticket_number',
            DB::raw('COALESCE(SUM(purchase_ticket_items.price), 0) as sum'),
            DB::raw("'Ft' as currency"),
            'purchase_tickets.customer_id',
            'purchase_tickets.created_at',
            'purchase_tickets.updated_at',
            DB::raw("'Vételi jegy' as type")
        )
        ->where('purchase_tickets.customer_id', $id)
        ->groupBy('purchase_tickets.purchase_ticket_id', 'purchase_tickets.ticket_number', 'purchase_tickets.customer_id', 'purchase_tickets.created_at', 'purchase_tickets.updated_at');

    $webshop = DB::table('webshop_orders')
        ->select(
            'id',
            DB::raw('CONCAT("#", order_id) as ticket_number'),
            'total as sum',
            DB::raw("'Ft' as currency"),
            'customer_id',
            'created_at',
            'updated_at',
            DB::raw("'Webshop megrendelés' as type")
        )
        ->where('customer_id', $id);

    $stock = DB::table('stock_entries')
        ->join('stock_entry_items', 'stock_entries.stock_entry_id', '=', 'stock_entry_items.stock_entry_id')
        ->select(
            'stock_entries.stock_entry_id as id',
            'stock_entries.ticket_number',
            DB::raw('COALESCE(SUM(stock_entry_items.price), 0) as sum'),
            DB::raw("'Ft' as currency"),
            'stock_entries.customer_id',
            'stock_entries.created_at',
            'stock_entries.updated_at',
            DB::raw("'Ékszer vásárlás' as type")
        )
        ->where('stock_entries.customer_id', $id)
        ->groupBy('stock_entries.stock_entry_id', 'stock_entries.ticket_number', 'stock_entries.customer_id', 'stock_entries.created_at', 'stock_entries.updated_at');

    return $purchases
        ->union($webshop)
        ->union($stock)
        ->orderBy('created_at', 'DESC')
        ->get();
}

}
