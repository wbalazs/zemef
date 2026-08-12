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
use App\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function __construct()
    {
        //////$this->authorizeResource(Category::class);
    }

    /**
     * Display a listing of the categories
     *
     * @param \App\Category  $model
     * @return \Illuminate\View\View
     */
    public function index(Category $model)
    {
        ////$this->authorize('manage-items', User::class);

        $categories = Category::with('children')
                                ->whereNull('parent_id')
                                ->orderBy('name', 'ASC')
                                ->get();

        $categories = $categories->map(function ($category) {
            $category->depth = 0;
            return $this->setCategoryDepth($category);
        });

        return view('categories.index', ['categories' => $categories]);
    }


    public function setCategoryDepth($category, $depth = 0)
    {
        $category->depth = $depth;

        if ($category->children) {
            foreach ($category->children as $child) {
                $this->setCategoryDepth($child, $depth + 1);
            }
        }

        return $category;
    }


    /**
     * Show the form for creating a new category
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('categories.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created category in storage
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Category  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request, Category $model)
    {
        $model->create($request->all());

        return redirect()->route('category.index')->withStatus('A kategória adatai sikeresen mentve.');
    }

    /**
     * Show the form for editing the specified category
     *
     * @param  \App\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        $categories = Category::orderBy('name', 'ASC')->get();

        return view('categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category in storage
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update(array_merge($request->all(), []));

        return redirect()->route('category.index')->withStatus('A kategória adatai sikeresen mentve.');
    }

    /**
     * Remove the specified category from storage
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        if (!$category->items->isEmpty()) {
            return redirect()->route('category.index')->withErrors('A kategória nem törölhető, mert még tartalmaz elemeket.');
        }

        $category->delete();

        return redirect()->route('category.index')->withStatus('A kategória sikeresen törölve.');
    }
}
