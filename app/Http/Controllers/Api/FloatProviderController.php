<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FloatProvider;
use Illuminate\Http\Request;

class FloatProviderController extends Controller
{
    /**
     * List all float providers
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $providers = FloatProvider::orderBy('sort_order')
            ->orderBy('display_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $providers->map(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'display_name' => $provider->display_name,
                    'type' => $provider->type,
                    'description' => $provider->description,
                    'is_active' => $provider->is_active,
                    'sort_order' => $provider->sort_order,
                ];
            })
        ]);
    }

    /**
     * Create float provider
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Create Accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:float_providers,name',
            'display_name' => 'required|string|max:100',
            'type' => 'required|in:bank,mobile_money',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $provider = FloatProvider::create([
            'name' => strtolower($request->name),
            'display_name' => $request->display_name,
            'type' => $request->type,
            'description' => $request->description,
            'is_active' => true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Float provider created successfully.',
            'data' => [
                'id' => $provider->id,
                'name' => $provider->name,
                'display_name' => $provider->display_name,
                'type' => $provider->type,
                'description' => $provider->description,
                'is_active' => $provider->is_active,
                'sort_order' => $provider->sort_order,
            ]
        ], 201);
    }

    /**
     * Update float provider
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Create Accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $provider = FloatProvider::findOrFail($id);

        $request->validate([
            'display_name' => 'required|string|max:100',
            'type' => 'required|in:bank,mobile_money',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $provider->display_name = $request->display_name;
        $provider->type = $request->type;
        $provider->description = $request->description;
        if ($request->has('sort_order')) {
            $provider->sort_order = $request->sort_order;
        }
        $provider->save();

        return response()->json([
            'success' => true,
            'message' => 'Float provider updated successfully.',
            'data' => [
                'id' => $provider->id,
                'name' => $provider->name,
                'display_name' => $provider->display_name,
                'type' => $provider->type,
                'description' => $provider->description,
                'is_active' => $provider->is_active,
                'sort_order' => $provider->sort_order,
            ]
        ]);
    }

    /**
     * Toggle float provider status
     */
    public function toggle(Request $request, $id)
    {
        if ($request->user()->cannot('Create Accounts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $provider = FloatProvider::findOrFail($id);
        $provider->is_active = !$provider->is_active;
        $provider->save();

        return response()->json([
            'success' => true,
            'message' => 'Float provider status updated successfully.',
            'data' => [
                'id' => $provider->id,
                'is_active' => $provider->is_active,
            ]
        ]);
    }
}

