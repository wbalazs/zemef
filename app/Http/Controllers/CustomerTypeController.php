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

use App\User;
use App\Models\CustomerType;
use App\Http\Requests\CustomerTypeRequest;

class CustomerTypeController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(CustomerType::class);
    }

    /**
     * Display a listing of the customer_types
     *
     * @param \App\Category  $model
     * @return \Illuminate\View\View
     */
    public function index(CustomerType $model)
    {
        //$this->authorize('manage-items', User::class);

        $customer_types= CustomerType::with('children')
                                ->whereNull('parent_id')
                                ->orderBy('name', 'ASC')
                                ->get();

        $customer_types = $customer_types->map(function ($customer_type) {
            $customer_type->depth = 0;
            return $this->setCustomerTypeDepth($customer_type);
        });

        return view('customer_types.index', ['customer_types' => $customer_types]);
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


    /**
     * Show the form for creating a new category
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $customer_types = CustomerType::orderBy('name', 'ASC')->get();

        $customer_types = $customer_types->map(function ($customer_type) {
            $customer_type->depth = 0;
            return $this->setCustomerTypeDepth($customer_type);
        });

        return view('customer_types.create', ['customer_types' => $customer_types]);
    }

    /**
     * Store a newly created category in storage
     *
     * @param  \App\Http\Requests\CustomerTypeRequest  $request
     * @param  \App\Category  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CustomerTypeRequest $request, CustomerType $model)
    {
        $model->create(array_merge($request->all(), []));

        return redirect()->route('customer_type.index')->withStatus('A típus adatai sikeresen mentve.');
    }

    /**
     * Show the form for editing the specified category
     *
     * @param  \App\Category  $customer_type
     * @return \Illuminate\View\View
     */
    public function edit(CustomerType $customer_type)
    {
        $customer_types = CustomerType::orderBy('name', 'ASC')->get();

        $customer_types = $customer_types->map(function ($customer_type) {
            $customer_type->depth = 0;
            return $this->setCustomerTypeDepth($customer_type);
        });

        return view('customer_types.edit', compact('customer_type', 'customer_types'));
    }

    /**
     * Update the specified category in storage
     *
     * @param  \App\Http\Requests\CustomerTypeRequest  $request
     * @param  \App\Category  $customer_type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CustomerTypeRequest $request, CustomerType $customer_type)
    {
        $customer_type->update(array_merge($request->all(), []));

        return redirect()->route('customer_type.index')->withStatus('A típus adatai sikeresen mentve.');
    }

    /**
     * Remove the specified category from storage
     *
     * @param  \App\Category  $customer_type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CustomerType $customer_type)
    {
        if ($customer_type->items && !$customer_type->items->isEmpty()) {
            return redirect()->route('customer_type.index')->withErrors('A típus nem törölhető, mert még tartalmaz elemeket.');
        }

        $customer_type->delete();

        return redirect()->route('customer_type.index')->withStatus('A típus sikeresen törölve.');
    }
}
