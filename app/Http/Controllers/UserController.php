<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        if (Auth()->user()->cannot('View Users')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'ALL USERS',
            'breadcrumbs' => [
                ['name' => 'Users', 'url' => route('users.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'All Users', 'url' => null, 'active' => true]
            ]
        ];

        $data['roles'] = Role::get();
        $data['users'] = User::with('roles')->get();

        return view('users.index', compact('data'));
    }


    public function create()
    {
        if (Auth()->user()->cannot('Create Users')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'CREATE USERS',
            'breadcrumbs' => [
                ['name' => 'Users', 'url' => route('users.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Add User', 'url' => null, 'active' => true]
            ]
        ];

        $data['roles'] = Role::get();

        return view('users.create', compact('data'));
    }


    public function store(Request $request)
    {
        if (Auth()->user()->cannot('Create Users')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_no' => ['required', 'numeric', 'digits_between:10,12'],
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
                'regex:/^\S*$/u' // This regex ensures no spaces in the username
            ],
            'gender' => 'required|in:M,F',
            'role_id' => 'required|exists:roles,id'
        ]);


        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'username' => $request->username,
            'gender' => $request->gender,
            'password' => Hash::make('default_password')
        ]);

        // Assign the selected role to the user
        $role = Role::findOrFail($request->role_id);
        $user->assignRole($role);

        // Redirect with a success message
        return back()->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (Auth()->user()->cannot('Edit Users')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'EDIT USER',
            'breadcrumbs' => [
                ['name' => 'Users', 'url' => route('users.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Edit User', 'url' => null, 'active' => true]
            ]
        ];

        $data['roles'] = Role::get();

        return view('users.edit', compact('data', 'user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_no' => 'required|digits:12|starts_with:255|unique:users,phone_no,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'gender' => 'required|in:M,F',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update([
            'name' => $request->full_name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'username' => $request->username,
            'gender' => $request->gender,
        ]);

        // Assign the selected role to the user
        $role = Role::findOrFail($request->role_id);
        $user->syncRoles($role);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        if (Auth()->user()->cannot('Change User Status')) {
            abort(403, 'Access Denied');
        }

        $user->status = !$user->status;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'User status updated successfully.']);
    }

    public function destroy(Request $request)
    {
        if (Auth()->user()->cannot('Delete Users')) {
            abort(403, 'Access Denied');
        }

        try {
            $userId = $request->id;

            // Prevent a user from deleting their own account
            if ($userId == auth()->id()) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            $user = User::findOrFail($userId);

            $user->phone_no = $user->phone_no . '_' . now()->timestamp;
            $user->email = $user->email . '_' . now()->timestamp;
            $user->username = $user->username . '_' . now()->timestamp;
            $user->save();

            $user->delete();

            return back()->with('success', 'User Deleted Successfully');
        } catch (\Exception $e) {
            \Log::error('Error in destroy() function: ' . $e->getMessage());
            return back()->with('error', 'An error occurred. ' . $e->getMessage());
        }
    }
}

