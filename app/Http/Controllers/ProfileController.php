<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\View\View;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'PROFILE',
            'breadcrumbs' => [
                ['name' => 'Profile', 'url' => route('profile.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'My Profile', 'url' => null, 'active' => true]
            ]
        ];

        // Get login histories for the current user
        $user = Auth::user();
        $adminRoleId = env('ADMIN_ROLE_ID', null);
        
        $loginsQuery = LoginHistory::with('user');
        
        if ($adminRoleId) {
            $roleName = Role::where('id', $adminRoleId)->value('name');
            if ($user->hasRole($roleName)) {
                $loginsQuery->orderBy('login_histories.id', 'desc');
            } else {
                $loginsQuery->where('login_histories.user_id', $user->id)
                    ->orderBy('login_histories.id', 'desc');
            }
        } else {
            $loginsQuery->where('login_histories.user_id', $user->id)
                ->orderBy('login_histories.id', 'desc');
        }

        $data['loginHistories'] = $loginsQuery->get();

        return view('profile.index', compact('data'));
    }

    public function update(Request $request)
    {
        if (Auth::user()->cannot('Edit Own Details')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'phone_no' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->phone_no = $request->input('phone_no');
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }


    public function passwordUpdate(Request $request)
    {
        if (Auth::user()->cannot('Change Password')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check if the current password is correct
        if (!Hash::check($request->input('current_password'), Auth::user()->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update the user password
        $user = Auth::user();
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Password updated successfully.');
    }
}
