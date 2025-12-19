<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Branch;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('branches.index');
    }

    /**
     * Get branches data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $branches = Branch::query();

            // Handle status filter - check if search contains status keywords
            $searchValue = $request->input('search.value', '');
            $hasActiveKeyword = preg_match('/\bactive\b/i', $searchValue);
            $hasInactiveKeyword = preg_match('/\binactive\b/i', $searchValue);

            // Apply status filter if keyword is present
            if ($hasActiveKeyword && !$hasInactiveKeyword) {
                $branches->where('is_active', true);
            } elseif ($hasInactiveKeyword && !$hasActiveKeyword) {
                $branches->where('is_active', false);
            }

            return DataTables::of($branches)
                ->addIndexColumn() // Add serial number column
                ->editColumn('address', function ($branch) {
                    return $branch->address ?: '-';
                })
                ->addColumn('status', function ($branch) {
                    return $branch->is_active
                        ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-3 py-1 text-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">Active</span>'
                        : '<span class="inline-flex items-center gap-1.5 rounded-full bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700 dark:bg-gray-500/10 dark:text-gray-400">Inactive</span>';
                })
                ->addColumn('actions', function ($branch) {
                    return view('branches.partials.actions', compact('branch'))->render();
                })
                ->filterColumn('status', function ($query, $keyword) {
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
                    // Enable searching on name column (excluding status keywords)
                    if (!empty($keyword) && !preg_match('/\b(active|inactive)\b/i', $keyword)) {
                        $query->where('branches.name', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('code', function ($query, $keyword) {
                    // Enable searching on code column (excluding status keywords)
                    if (!empty($keyword) && !preg_match('/\b(active|inactive)\b/i', $keyword)) {
                        $query->where('branches.code', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('email', function ($query, $keyword) {
                    // Enable searching on email column (excluding status keywords)
                    if (!empty($keyword) && !preg_match('/\b(active|inactive)\b/i', $keyword)) {
                        $query->where('branches.email', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('phone', function ($query, $keyword) {
                    // Enable searching on phone column (excluding status keywords)
                    if (!empty($keyword) && !preg_match('/\b(active|inactive)\b/i', $keyword)) {
                        $query->where('branches.phone', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('address', function ($query, $keyword) {
                    // Enable searching on address column (excluding status keywords)
                    if (!empty($keyword) && !preg_match('/\b(active|inactive)\b/i', $keyword)) {
                        $query->where('branches.address', 'like', "%{$keyword}%");
                    }
                })
                ->orderColumn('status', false)
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
        return view('branches.create');
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
                Rule::unique('branches')->whereNull('deleted_at')
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('branches')->whereNull('deleted_at')
            ],
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'The branch name is required.',
            'name.unique' => 'A branch with this name already exists.',
            'code.unique' => 'A branch with this code already exists.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        Branch::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('branches.index')
            ->with('success', __('auth.branch_created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branches')->ignore($branch->id)->whereNull('deleted_at')
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('branches')->ignore($branch->id)->whereNull('deleted_at')
            ],
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'The branch name is required.',
            'name.unique' => 'A branch with this name already exists.',
            'code.unique' => 'A branch with this code already exists.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $branch->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        $branchName = $branch->name;
        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => __('auth.branch_deleted')
        ]);
    }
}
