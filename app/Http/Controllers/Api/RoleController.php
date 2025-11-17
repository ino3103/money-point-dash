<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * List all roles
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Roles')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $roles = Role::withCount('permissions')->get();

        return response()->json([
            'success' => true,
            'data' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions_count' => $role->permissions_count,
                ];
            })
        ]);
    }

    /**
     * Show role details
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('View Roles')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'category' => $permission->category,
                    ];
                })->groupBy('category'),
            ]
        ]);
    }

    /**
     * Create role
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Roles')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|min:2|max:50|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name
        ]);

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions_count' => $permissions->count(),
            ]
        ], 201);
    }

    /**
     * Update role
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Edit Roles')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|min:2|max:50|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->name = $request->name;
        $role->save();

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions_count' => $permissions->count(),
            ]
        ]);
    }

    /**
     * Delete role
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Roles')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $role = Role::findOrFail($id);

        if ($request->user()->roles->contains($role)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own role.'
            ], 422);
        }

        $role->permissions()->detach();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.'
        ]);
    }
}

