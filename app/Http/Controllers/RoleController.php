<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('roles.index');
    }

    /**
     * Get roles data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $roles = Role::where('guard_name', 'web')->withCount(['permissions', 'users']);

            return DataTables::of($roles)
                ->addIndexColumn() // Add serial number column
                ->addColumn('permissions_count', function ($role) {
                    return $role->permissions_count ?? 0;
                })
                ->addColumn('users_count', function ($role) {
                    return $role->users_count ?? 0;
                })
                ->addColumn('actions', function ($role) {
                    return view('roles.partials.actions', compact('role'))->render();
                })
                ->editColumn('created_at', function ($role) {
                    return $role->created_at ? $role->created_at->format('M d, Y') : '';
                })
                ->editColumn('updated_at', function ($role) {
                    return $role->updated_at ? $role->updated_at->format('M d, Y') : '';
                })
                ->filterColumn('permissions_count', function ($query, $keyword) {
                    // Prevent searching on computed column
                    return $query;
                })
                ->filterColumn('users_count', function ($query, $keyword) {
                    // Prevent searching on computed column
                    return $query;
                })
                ->filterColumn('actions', function ($query, $keyword) {
                    // Prevent searching on actions column
                    return $query;
                })
                ->filterColumn('DT_RowIndex', function ($query, $keyword) {
                    // Prevent searching on index column
                    return $query;
                })
                ->filterColumn('name', function ($query, $keyword) {
                    // Enable searching on name column
                    $query->where('roles.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('created_at', function ($query, $keyword) {
                    // Enable searching on created_at column (formatted date)
                    $query->whereRaw("DATE_FORMAT(roles.created_at, '%b %d, %Y') like ?", ["%{$keyword}%"]);
                })
                ->orderColumn('permissions_count', false)
                ->orderColumn('users_count', false)
                ->orderColumn('actions', false)
                ->rawColumns(['actions'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('DataTables error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'draw' => $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all permissions for web guard
        $allPermissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        // Group permissions by category (resource name) - same logic as edit method
        $permissionsByCategory = [];

        // Define common action words to extract category (resource) from permission name
        $actionWords = ['view', 'create', 'edit', 'update', 'delete', 'assign', 'approve', 'verify', 'reject', 'open', 'close', 'export'];
        // Resource type words that should be the category even if they come before other words
        $resourceTypes = ['reports', 'dashboard', 'summary', 'performance'];

        foreach ($allPermissions as $permission) {
            $name = strtolower($permission->name);
            $parts = explode(' ', $name);

            // Find the resource word - prefer resource types, otherwise use the last word after removing actions and "own"
            $category = '';

            // First, check if any resource type word exists
            foreach ($resourceTypes as $resourceType) {
                if (in_array($resourceType, $parts)) {
                    $category = ucfirst($resourceType);
                    break;
                }
            }

            // If no resource type found, extract category differently
            if (empty($category)) {
                // Remove action words and "own" from parts
                $resourceParts = [];
                foreach ($parts as $part) {
                    if (!in_array($part, $actionWords) && $part !== 'own') {
                        $resourceParts[] = $part;
                    }
                }

                // Use the last meaningful word(s) as category
                if (!empty($resourceParts)) {
                    // Take last 1-2 words (handles "float providers" -> "Float Providers")
                    $categoryParts = array_slice($resourceParts, -min(2, count($resourceParts)));
                    $category = ucwords(implode(' ', $categoryParts));
                } else {
                    // Fallback: use last word
                    $category = ucfirst(end($parts));
                }
            }

            // Handle special case: "assign roles" should be in "Roles" category
            if (in_array('roles', $parts) && !in_array($category, ['Roles', 'Role'])) {
                $category = 'Roles';
            }

            // If category doesn't exist, create it
            if (!isset($permissionsByCategory[$category])) {
                $permissionsByCategory[$category] = [];
            }

            $permissionsByCategory[$category][] = $permission;
        }

        // Sort permissions within each category
        foreach ($permissionsByCategory as $category => $permissions) {
            usort($permissionsByCategory[$category], function ($a, $b) {
                return strcmp($a->name, $b->name);
            });
        }

        // Sort categories alphabetically
        ksort($permissionsByCategory);

        return view('roles.create', compact('permissionsByCategory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->where('guard_name', 'web')->where(function ($query) {
                    $query->whereNull('deleted_at');
                })
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'The role name is required.',
            'name.unique' => 'A role with this name already exists.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
        ]);

        // Create the role
        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        // Sync permissions - convert IDs to Permission models
        $permissionIds = $request->input('permissions', []);

        if (!empty($permissionIds)) {
            // Get Permission models by IDs
            $permissions = Permission::whereIn('id', $permissionIds)
                ->where('guard_name', 'web')
                ->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        // Prevent editing Super Admin role
        if ($role->id === 1) {
            return redirect()->route('roles.index')
                ->with('error', 'The Super Admin role cannot be edited.');
        }

        // Get all permissions for the same guard
        $allPermissions = Permission::where('guard_name', $role->guard_name)->orderBy('name')->get();

        // Group permissions by category (resource name)
        $permissionsByCategory = [];

        // Define common action words to extract category (resource) from permission name
        $actionWords = ['view', 'create', 'edit', 'update', 'delete', 'assign', 'approve', 'verify', 'reject', 'open', 'close', 'export'];
        // Resource type words that should be the category even if they come before other words
        $resourceTypes = ['reports', 'dashboard', 'summary', 'performance'];

        foreach ($allPermissions as $permission) {
            $name = strtolower($permission->name);
            $parts = explode(' ', $name);

            // Find the resource word - prefer resource types, otherwise use the last word after removing actions and "own"
            $category = '';

            // First, check if any resource type word exists
            foreach ($resourceTypes as $resourceType) {
                if (in_array($resourceType, $parts)) {
                    $category = ucfirst($resourceType);
                    break;
                }
            }

            // If no resource type found, extract category differently
            if (empty($category)) {
                // Remove action words and "own" from parts
                $resourceParts = [];
                foreach ($parts as $part) {
                    if (!in_array($part, $actionWords) && $part !== 'own') {
                        $resourceParts[] = $part;
                    }
                }

                // Use the last meaningful word(s) as category
                if (!empty($resourceParts)) {
                    // Take last 1-2 words (handles "float providers" -> "Float Providers")
                    $categoryParts = array_slice($resourceParts, -min(2, count($resourceParts)));
                    $category = ucwords(implode(' ', $categoryParts));
                } else {
                    // Fallback: use last word
                    $category = ucfirst(end($parts));
                }
            }

            // Handle special case: "assign roles" should be in "Roles" category
            if (in_array('roles', $parts) && !in_array($category, ['Roles', 'Role'])) {
                $category = 'Roles';
            }

            // If category doesn't exist, create it
            if (!isset($permissionsByCategory[$category])) {
                $permissionsByCategory[$category] = [];
            }

            $permissionsByCategory[$category][] = $permission;
        }

        // Sort permissions within each category
        foreach ($permissionsByCategory as $category => $permissions) {
            usort($permissionsByCategory[$category], function ($a, $b) {
                return strcmp($a->name, $b->name);
            });
        }

        // Sort categories alphabetically
        ksort($permissionsByCategory);

        // Get role's current permissions
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissionsByCategory', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Prevent updating Super Admin role
        if ($role->id === 1) {
            return redirect()->route('roles.index')
                ->with('error', 'The Super Admin role cannot be edited.');
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id)->where(function ($query) {
                    $query->where('guard_name', 'web')->whereNull('deleted_at');
                })
            ],
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.required' => 'The role name is required.',
            'name.unique' => 'A role with this name already exists.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
        ]);

        $role->update(['name' => $validated['name']]);

        // Sync permissions - convert IDs to Permission models
        $permissionIds = $request->input('permissions', []);

        if (!empty($permissionIds)) {
            // Get Permission models by IDs
            $permissions = Permission::whereIn('id', $permissionIds)
                ->where('guard_name', $role->guard_name)
                ->get();
            $role->syncPermissions($permissions);
        } else {
            // If no permissions selected, remove all permissions
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Prevent deleting Super Admin role
        if ($role->id === 1) {
            return response()->json([
                'success' => false,
                'message' => 'The Super Admin role cannot be deleted.'
            ], 403);
        }

        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role. There are users assigned to this role.'
            ], 422);
        }

        $roleName = $role->name;
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.'
        ]);
    }
}
