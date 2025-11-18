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

        // Convert gender from database format to user-friendly format
        $genderMap = [
            'M' => 'male',
            'F' => 'female',
            'O' => 'other',
        ];
        $genderDisplay = $user->gender ? ($genderMap[$user->gender] ?? $user->gender) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone_no' => $user->phone_no,
                'gender' => $genderDisplay,
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
            'gender' => 'nullable|string|in:male,female,other,M,F,O',
        ]);

        $user = $request->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
        $user->phone_no = $request->input('phone_no');

        // Convert gender from user-friendly format to database format
        if ($request->has('gender') && $request->input('gender') !== null) {
            $gender = $request->input('gender');
            // Map user-friendly values to database values
            $genderMap = [
                'male' => 'M',
                'female' => 'F',
                'other' => 'O',
            ];
            $user->gender = $genderMap[$gender] ?? $gender; // Use mapped value or original if already M/F/O
        }

        $user->save();

        // Refresh user to ensure we have the latest data
        $user->refresh();

        // Convert gender from database format to user-friendly format
        $genderMap = [
            'M' => 'male',
            'F' => 'female',
            'O' => 'other',
        ];
        $genderDisplay = $user->gender ? ($genderMap[$user->gender] ?? $user->gender) : null;

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone_no' => $user->phone_no,
                'gender' => $genderDisplay,
                'profile_picture' => $user->profile_picture,
                'status' => $user->status,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
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
