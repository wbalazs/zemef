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

use App\Role;
use App\User;
use App\Models\Permission;
use App\Http\Requests\RoleRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct()
    {
        //////$this->authorizeResource(Role::class);
    }

    /**
     * Display a listing of the roles
     *
     * @param \App\Role  $model
     * @return \Illuminate\View\View
     */
    public function index(Role $model)
    {
        //$this->authorize('manage-users', User::class);

        return view('roles.index', ['roles' => $model->all()]);
    }

    /**
     * Show the form for creating a new role
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
         return view('roles.create');
    }

    /**
     * Store a newly created role in storage
     *
     * @param  \App\Http\Requests\RoleRequest  $request
     * @param  \App\Role  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RoleRequest $request, Role $model)
    {
        $model->create($request->all());

        return redirect()->route('role.index')->withStatus(__('Role successfully created.'));
    }

    /**
     * Show the form for editing the specified role
     *
     * @param  \App\Role  $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
{
    $permissionsGrouped = Permission::all()
        ->filter(function ($permission) {
            // Only keep permissions with a valid model class string
            return is_string($permission->model) && str_starts_with($permission->model, 'App\\');
        })
        //->sortBy('model')
        ->groupBy('model')
        ->map(fn($perms) => $perms->toArray())
        ->toArray();

    $rolePermissions = $role->permissions->pluck('id')->toArray();

    return view('roles.edit', compact('role', 'permissionsGrouped', 'rolePermissions'));
}

     /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:30', Rule::unique('roles')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:300'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update($validated);

        // Sync permissions – detach old, attach new
        //$role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('role.index')
            ->with('success', 'Role updated successfully.');
    }

    public function togglePermission(Role $role, Permission $permission): JsonResponse
    {
        // Check if the role already has this permission
        $hasPermission = $role->permissions()->where('permission_id', $permission->id)->exists();

        if ($hasPermission) {
            $role->permissions()->detach($permission->id);
            $active = false;
        } else {
            $role->permissions()->attach($permission->id);
            $active = true;
        }

        return response()->json(['active' => $active]);
    }
}
