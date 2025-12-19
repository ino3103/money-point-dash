<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Get users data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $users = User::with(['branch', 'roles']);

            return DataTables::of($users)
                ->addIndexColumn() // Add serial number column
                ->addColumn('branch_name', function ($user) {
                    return $user->branch ? $user->branch->name : __('auth.no_branch');
                })
                ->addColumn('role_name', function ($user) {
                    return $user->roles->first() ? $user->roles->first()->name : '-';
                })
                ->addColumn('status', function ($user) {
                    if ($user->is_active) {
                        return '<span class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            ' . __('auth.active') . '
                        </span>';
                    } else {
                        return '<span class="inline-flex items-center gap-1.5 rounded-full bg-error-50 px-3 py-1 text-xs font-medium text-error-700 dark:bg-error-500/10 dark:text-error-400">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            ' . __('auth.inactive') . '
                        </span>';
                    }
                })
                ->addColumn('actions', function ($user) {
                    return view('users.partials.actions', compact('user'))->render();
                })
                ->filterColumn('branch_name', function ($query, $keyword) {
                    // Search by branch name
                    $query->whereHas('branch', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })->orWhereDoesntHave('branch');
                })
                ->filterColumn('role_name', function ($query, $keyword) {
                    // Search by role name
                    $query->whereHas('roles', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
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
                    $query->where('users.name', 'like', "%{$keyword}%");
                })
                ->filterColumn('email', function ($query, $keyword) {
                    // Enable searching on email column
                    $query->where('users.email', 'like', "%{$keyword}%");
                })
                ->filterColumn('phone', function ($query, $keyword) {
                    // Enable searching on phone column
                    if (!empty($keyword)) {
                        $query->where('users.phone', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('nin', function ($query, $keyword) {
                    // Enable searching on NIN column
                    if (!empty($keyword)) {
                        $query->where('users.nin', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('status', function ($query, $keyword) {
                    // Prevent searching on status column
                    return $query;
                })
                ->orderColumn('actions', false)
                ->rawColumns(['status', 'actions'])
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
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        return view('users.create', compact('branches', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nin' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'role' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => __('auth.user_name') . ' ' . __('auth.is_required'),
            'email.required' => __('auth.email_address') . ' ' . __('auth.is_required'),
            'email.unique' => __('auth.email_unique'),
            'password.required' => __('auth.password') . ' ' . __('auth.is_required'),
            'password.min' => __('auth.password_min'),
            'password.confirmed' => __('auth.password_confirmed'),
            'role.required' => __('auth.role_required'),
            'role.exists' => __('auth.role_exists'),
            'branch_id.exists' => __('auth.branch_exists'),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nin' => $validated['nin'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'is_active' => $request->has('is_active') && $request->input('is_active') == '1',
        ]);

        // Assign role (required)
        $role = Role::findOrFail($validated['role']);
        $user->assignRole($role);

        return redirect()->route('users.index')
            ->with('success', __('auth.user_created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        $userRole = $user->roles->first();
        return view('users.edit', compact('user', 'branches', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'nin' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'role' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => __('auth.user_name') . ' ' . __('auth.is_required'),
            'email.required' => __('auth.email_address') . ' ' . __('auth.is_required'),
            'email.unique' => __('auth.email_unique'),
            'role.required' => __('auth.role_required'),
            'role.exists' => __('auth.role_exists'),
            'branch_id.exists' => __('auth.branch_exists'),
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nin' => $validated['nin'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
        ];

        // Handle checkbox: if present in request, it's checked (1), otherwise unchecked (0)
        $updateData['is_active'] = $request->has('is_active') && $request->input('is_active') == '1';

        $user->update($updateData);

        // Sync role (required)
        $role = Role::findOrFail($validated['role']);
        $user->syncRoles([$role]);

        return redirect()->route('users.index')
            ->with('success', __('auth.user_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting the currently logged-in user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_delete_own_account')
            ], 422);
        }

        // Prevent deleting user with id = 1
        if ($user->id === 1) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_delete_user')
            ], 422);
        }

        // Prevent deleting Super Admin users
        if ($user->hasRole('Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_delete_super_admin_user')
            ], 422);
        }

        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('auth.user_deleted')
        ]);
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ], [
                'password.required' => __('auth.password') . ' ' . __('auth.is_required'),
                'password.min' => __('auth.password_min'),
                'password.confirmed' => __('auth.password_confirmed'),
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('auth.password_updated')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('auth.failed_to_update_password')
            ], 500);
        }
    }
}
