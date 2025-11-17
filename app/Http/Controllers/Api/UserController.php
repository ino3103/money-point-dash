<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List all users
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Users')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $query = User::with('roles');

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'username' => $user->username,
                    'phone_no' => $user->phone_no,
                    'gender' => $user->gender,
                    'status' => $user->status,
                    'roles' => $user->getRoleNames(),
                ];
            })
        ]);
    }

    /**
     * Show user details
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('View Users')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone_no' => $user->phone_no,
                'gender' => $user->gender,
                'status' => $user->status,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    /**
     * Create user
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Users')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_no' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users,username|regex:/^\S*$/u',
            'gender' => 'nullable|in:male,female,other,M,F',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'username' => $request->username,
            'gender' => $request->gender,
            'password' => Hash::make($request->password ?? 'password'),
            'status' => 1,
        ]);

        $role = Role::findOrFail($request->role_id);
        $user->assignRole($role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
            ]
        ], 201);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Edit Users')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_no' => 'nullable|string|max:20',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id . '|regex:/^\S*$/u',
            'gender' => 'nullable|in:male,female,other,M,F',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_no = $request->phone_no;
        $user->username = $request->username;
        $user->gender = $request->gender;
        $user->save();

        $role = Role::findOrFail($request->role_id);
        $user->syncRoles([$role]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(Request $request, $id)
    {
        if ($request->user()->cannot('Change User Status')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $user = User::findOrFail($id);

        if ($user->id == $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status.'
            ], 422);
        }

        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully.',
            'data' => [
                'id' => $user->id,
                'status' => $user->status,
            ]
        ]);
    }

    /**
     * Delete user
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->cannot('Delete Users')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $user = User::findOrFail($id);

        if ($user->id == $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 422);
        }

        // Soft delete by updating identifiers
        $user->phone_no = $user->phone_no . '_' . now()->timestamp;
        $user->email = $user->email . '_' . now()->timestamp;
        $user->username = $user->username . '_' . now()->timestamp;
        $user->save();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
}

