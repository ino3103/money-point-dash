<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\FloatType;
use Yajra\DataTables\Facades\DataTables;

class FloatTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('float-types.index');
    }

    /**
     * Get float types data for DataTables.
     */
    public function getData(Request $request)
    {
        try {
            $floatTypes = FloatType::query();

            // Handle status filter - check if search contains status keywords
            $searchValue = $request->input('search.value', '');
            $hasActiveKeyword = preg_match('/\bactive\b/i', $searchValue);
            $hasInactiveKeyword = preg_match('/\binactive\b/i', $searchValue);

            // Apply status filter if keyword is present
            if ($hasActiveKeyword && !$hasInactiveKeyword) {
                $floatTypes->where('is_active', true);
            } elseif ($hasInactiveKeyword && !$hasActiveKeyword) {
                $floatTypes->where('is_active', false);
            }

            return DataTables::of($floatTypes)
                ->addIndexColumn() // Add serial number column
                ->editColumn('code', function ($floatType) {
                    return $floatType->code ?: '-';
                })
                ->editColumn('description', function ($floatType) {
                    return $floatType->description ?: '-';
                })
                ->addColumn('status', function ($floatType) {
                    return $floatType->is_active
                        ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-3 py-1 text-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">' . __('auth.active') . '</span>'
                        : '<span class="inline-flex items-center gap-1.5 rounded-full bg-error-50 px-3 py-1 text-sm font-medium text-error-700 dark:bg-error-500/10 dark:text-error-400">' . __('auth.inactive') . '</span>';
                })
                ->addColumn('actions', function ($floatType) {
                    return view('float-types.partials.actions', compact('floatType'))->render();
                })
                ->editColumn('created_at', function ($floatType) {
                    return $floatType->created_at ? $floatType->created_at->format('M d, Y') : '';
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
                        $query->where('float_types.name', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('code', function ($query, $keyword) {
                    // Enable searching on code column (excluding status keywords)
                    if (!empty($keyword) && !preg_match('/\b(active|inactive)\b/i', $keyword)) {
                        $query->where('float_types.code', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('description', function ($query, $keyword) {
                    // Enable searching on description column (excluding status keywords)
                    if (!empty($keyword) && !preg_match('/\b(active|inactive)\b/i', $keyword)) {
                        $query->where('float_types.description', 'like', "%{$keyword}%");
                    }
                })
                ->filterColumn('created_at', function ($query, $keyword) {
                    // Enable searching on created_at column (formatted date)
                    $query->whereRaw("DATE_FORMAT(float_types.created_at, '%b %d, %Y') like ?", ["%{$keyword}%"]);
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
        return view('float-types.create');
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
                Rule::unique('float_types')->whereNull('deleted_at')
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('float_types')->whereNull('deleted_at')
            ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'name.required' => __('auth.float_type_name_required'),
            'name.unique' => __('auth.float_type_name_unique'),
            'code.unique' => __('auth.float_type_code_unique'),
        ]);

        FloatType::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('float-types.index')
            ->with('success', __('auth.float_type_created'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $floatType = FloatType::findOrFail($id);
        return view('float-types.edit', compact('floatType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $floatType = FloatType::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('float_types')->ignore($floatType->id)->whereNull('deleted_at')
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('float_types')->ignore($floatType->id)->whereNull('deleted_at')
            ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ], [
            'name.required' => __('auth.float_type_name_required'),
            'name.unique' => __('auth.float_type_name_unique'),
            'code.unique' => __('auth.float_type_code_unique'),
        ]);

        $floatType->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('float-types.index')
            ->with('success', __('auth.float_type_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $floatType = FloatType::findOrFail($id);

        $floatTypeName = $floatType->name;
        $floatType->delete();

        return response()->json([
            'success' => true,
            'message' => __('auth.float_type_deleted')
        ]);
    }
}
