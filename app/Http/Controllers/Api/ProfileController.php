<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Get user profile
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone_no' => $user->phone_no,
                'gender' => $user->gender,
                'profile_picture' => $user->profile_picture,
                'status' => $user->status,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        if ($request->user()->cannot('Edit Own Details')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'username' => 'required|string|max:255|unique:users,username,' . $request->user()->id,
            'phone_no' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:male,female,other',
        ]);

        $user = $request->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->phone_no = $request->input('phone_no');
        
        if ($request->has('gender')) {
            $user->gender = $request->input('gender');
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone_no' => $user->phone_no,
                'gender' => $user->gender,
            ]
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        if ($request->user()->cannot('Change Password')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.'
        ]);
    }

    /**
     * Get login history
     */
    public function loginHistory(Request $request)
    {
        $user = $request->user();
        
        $loginHistories = LoginHistory::where('user_id', $user->id)
            ->orderBy('login_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $loginHistories->map(function ($login) {
                return [
                    'id' => $login->id,
                    'ip_address' => $login->ip_address,
                    'user_agent' => $login->user_agent,
                    'login_at' => $login->login_at->toISOString(),
                ];
            })
        ]);
    }
}

