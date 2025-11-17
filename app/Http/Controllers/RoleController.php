<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        if (Auth()->user()->cannot('View Roles')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'ALL ROLES',
            'breadcrumbs' => [
                ['name' => 'Roles', 'url' => route('roles.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'All Roles', 'url' => null, 'active' => true]
            ]
        ];

        $data['roles'] = Role::withCount('permissions')->get();

        return view('roles.index', compact('data'));
    }

    public function create()
    {
        if (Auth()->user()->cannot('Create Roles')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'CREATE ROLES',
            'breadcrumbs' => [
                ['name' => 'Roles', 'url' => route('roles.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Add Roles', 'url' => null, 'active' => true]
            ]
        ];

        $permissions = Permission::get()->groupBy('category')->sortKeys();

        return view('roles.create', compact('data', 'permissions'));
    }

    public function store(Request $request)
    {
        if (Auth()->user()->cannot('Create Roles')) {
            abort(403, 'Access Denied');
        }

        try {
            $request->validate([
                'name' => 'unique:roles|required|string|min:2|max:50',
                'permissions' => 'required',
            ]);

            $role = Role::create([
                'name' => $request->name
            ]);

            if (is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            return back()->with('success', 'Role created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in store() function: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. ' . $e->getMessage() . '');
        }
    }

    public function edit($id)
    {
        if (Auth()->user()->cannot('Edit Roles')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'CREATE ROLES',
            'breadcrumbs' => [
                ['name' => 'Roles', 'url' => route('roles.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Edit Roles', 'url' => null, 'active' => true]
            ]
        ];

        $role = Role::findOrFail($id);

        $AssignedPermissions = DB::select("SELECT permission_id id FROM role_has_permissions where role_id = " . $id . "");
        $permissionsAll = Permission::get()->groupBy('category')->sortKeys();

        $permissionsAssigned = [];
        foreach ($AssignedPermissions as $p) {
            $permissionsAssigned[] = $p->id;
        }

        return view("roles.edit", compact('data', 'role', 'permissionsAll', 'permissionsAssigned'));
    }

    public function update(Request $request)
    {
        if (Auth()->user()->cannot('Edit Roles')) {
            abort(403, 'Access Denied');
        }

        try {
            $request->validate([
                'name' => 'required|string|min:2|max:50',
                'permissions' => 'required',
            ]);

            $role = Role::findOrfail($request->id);
            $role->name = $request->name;
            $role->save();

            if (is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            return back()->with('success', 'Role Updated Successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in store() function: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. ' . $e->getMessage() . '');
        }
    }

    public function destroy(Request $request)
    {
        if (Auth()->user()->cannot('Delete Roles')) {
            abort(403, 'Access Denied');
        }

        try {
            $role = Role::find($request->id);

            if (!$role) {
                return back()->with('error', 'Role not found');
            }

            if (auth()->user()->roles->contains($role)) {
                return back()->with('error', 'You cannot delete your own role');
            }

            $role->permissions()->detach();

            $role->delete();

            return back()->with('success', 'Role Deleted Successfully 😊');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in destroy() function: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. ' . $e->getMessage() . '');
        }
    }
}

