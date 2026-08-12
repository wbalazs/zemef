<?php

namespace App\Http\Controllers;


use App\Models\Photography;
use App\Models\ToolType;
use App\Models\DocType;
use App\Models\Business;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PhotographyListItems;
use Carbon\Carbon;
use App\Http\Requests\PhotographyRequest;
use Illuminate\Http\Request;
use DB;
use Auth;



class PhotographyController extends Controller
{
    public function __construct()
    {
        ////$this->authorizeResource(Photography::class);
    }

    /**
     * Display a listing of the tools
     *
     * @param \App\Item  $model
     * @return \Illuminate\View\View
     */
    public function index(Photography $model)
    {
        return view('photographies.index', ['photo_list' => $model->get()]);
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
        $businesses = Business::where('status', 1)->orderBy('name', 'ASC')->get();
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();
        $products = Product::orderBy('name', 'ASC')->get();

        return view('photographies.create', [
            'products' => $products,
            'businesses' => $businesses,
            'employees' => $employees
        ]);
    }

    /**
     * Store a newly created item in storage
     *
     * @param  \App\Http\Requests\ToolRequest  $request
     * @param  \App\Item  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PhotographyRequest $request, Photography $model)
    {
        $photography = new Photography();
        $photography->employee_id = Auth()->user()->employee_id;
        $photography->list_name = $request->list_name;
        $photography->save();

        $num = 1;
        if(is_array($request->products_array)) {
            foreach($request->products_array as $product_id) {
                $photo_list_item = new PhotographyListItems();
                $photo_list_item->num = $num;
                $photo_list_item->photography_id = $photography->photography_id;
                $photo_list_item->product_id = $product_id;
                $photo_list_item->save();
                $num++;
            }
        }

        return redirect()->route('photography.index')->withStatus('A lista sikeresen létrehozva.');
    }

    /**
     * Show the form for editing the specified item
     *
     * @return \Illuminate\View\View
     */
    public function edit(Photography $photography)
    {
        $businesses = Business::where('status', 1)->orderBy('name', 'ASC')->get();
        $employees = Employee::where('status', 1)->orderBy('lastname', 'ASC')->orderBy('firstname', 'ASC')->get();
        $products = Product::orderBy('name', 'ASC')->get();

        return view('photographies.edit', [
            'photography' => $photography,
            'products' => $products,
            'businesses' => $businesses,
            'employees' => $employees
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Itemuest  $request
     * @param  \App\Item  $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PhotographyRequest $request, Photography $photography)
    {
        $photography->list_name = $request->list_name;
        $photography->save();

        PhotographyListItems::where('photography_id', $photography->photography_id)->delete();

        $num = 1;
        foreach($request->products_array as $product_id) {
            $photo_list_item = new PhotographyListItems();
            $photo_list_item->photography_id = $photography->photography_id;
            $photo_list_item->num = $num;
            $photo_list_item->product_id = $product_id;
            $photo_list_item->save();
            $num++;
        }

        return redirect()->route('photography.index')->withStatus('A lista sikeresen frissítve.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Photography $photography)
    {
        $photography->items()->delete();
        $photography->delete();

        return redirect()->route('photography.index')->withStatus('A lista sikeresen törölve.');
    }
   

}
