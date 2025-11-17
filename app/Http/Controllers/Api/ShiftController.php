<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TellerShift;
use App\Models\User;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * List all shifts
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $query = TellerShift::with(['teller', 'treasurer']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('teller_id')) {
            $query->where('teller_id', $request->teller_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        $shifts = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $shifts->map(function ($shift) {
                return [
                    'id' => $shift->id,
                    'teller_id' => $shift->teller_id,
                    'teller_name' => $shift->teller->name ?? null,
                    'treasurer_id' => $shift->treasurer_id,
                    'treasurer_name' => $shift->treasurer->name ?? null,
                    'status' => $shift->status,
                    'opening_cash' => $shift->opening_cash ? $shift->opening_cash / 100 : 0,
                    'opening_floats' => $shift->opening_floats ? array_map(function($v) { return $v / 100; }, $shift->opening_floats) : [],
                    'closing_cash' => $shift->closing_cash ? $shift->closing_cash / 100 : null,
                    'closing_floats' => $shift->closing_floats ? array_map(function($v) { return $v / 100; }, $shift->closing_floats) : null,
                    'variance_cash' => $shift->variance_cash ? $shift->variance_cash / 100 : null,
                    'variance_floats' => $shift->variance_floats ? array_map(function($v) { return $v / 100; }, $shift->variance_floats) : null,
                    'opened_at' => $shift->opened_at->toISOString(),
                    'closed_at' => $shift->closed_at ? $shift->closed_at->toISOString() : null,
                    'notes' => $shift->notes,
                ];
            })
        ]);
    }

    /**
     * Show shift details
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('View Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::with(['teller', 'treasurer', 'transactions.user', 'transactions.lines.account'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shift->id,
                'teller_id' => $shift->teller_id,
                'teller_name' => $shift->teller->name ?? null,
                'treasurer_id' => $shift->treasurer_id,
                'treasurer_name' => $shift->treasurer->name ?? null,
                'status' => $shift->status,
                'opening_cash' => $shift->opening_cash ? $shift->opening_cash / 100 : 0,
                'opening_floats' => $shift->opening_floats ? array_map(function($v) { return $v / 100; }, $shift->opening_floats) : [],
                'closing_cash' => $shift->closing_cash ? $shift->closing_cash / 100 : null,
                'closing_floats' => $shift->closing_floats ? array_map(function($v) { return $v / 100; }, $shift->closing_floats) : null,
                'expected_closing_cash' => $shift->expected_closing_cash ? $shift->expected_closing_cash / 100 : null,
                'expected_closing_floats' => $shift->expected_closing_floats ? array_map(function($v) { return $v / 100; }, $shift->expected_closing_floats) : null,
                'variance_cash' => $shift->variance_cash ? $shift->variance_cash / 100 : null,
                'variance_floats' => $shift->variance_floats ? array_map(function($v) { return $v / 100; }, $shift->variance_floats) : null,
                'opened_at' => $shift->opened_at->toISOString(),
                'closed_at' => $shift->closed_at ? $shift->closed_at->toISOString() : null,
                'notes' => $shift->notes,
                'transactions' => $shift->transactions->map(function ($tx) {
                    $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                    return [
                        'id' => $tx->id,
                        'type' => $tx->type,
                        'amount' => $cashLine ? abs($cashLine->amount) / 100 : 0,
                        'user_name' => $tx->user->name ?? null,
                        'created_at' => $tx->created_at->toISOString(),
                    ];
                }),
            ]
        ]);
    }

    /**
     * Open/create shift
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Open Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'opening_floats' => 'nullable|array',
            'treasurer_id' => 'nullable|exists:users,id',
        ]);

        // Convert amounts to cents (integers)
        $openingCash = (int)($request->opening_cash * 100);
        $openingFloats = [];
        if ($request->opening_floats) {
            foreach ($request->opening_floats as $provider => $amount) {
                $openingFloats[$provider] = (int)($amount * 100);
            }
        }

        // Check if user already has an open shift
        $existingShift = TellerShift::where('teller_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if ($existingShift) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an open shift. Please close it first.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get treasurer (use provided treasurer_id or current user)
            $treasurer = $request->treasurer_id 
                ? User::findOrFail($request->treasurer_id)
                : $request->user();

            // Use AccountingService to open shift (it creates the shift and allocations)
            $shift = $this->accountingService->openShift(
                $treasurer,
                $request->user(), // teller
                $openingCash,
                $openingFloats,
                false, // usePreviousCash
                [] // usePreviousFloats
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift opened successfully.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                    'opening_cash' => $shift->opening_cash ? $shift->opening_cash / 100 : 0,
                    'opened_at' => $shift->opened_at->toISOString(),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to open shift: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit shift
     */
    public function submit(Request $request, $id)
    {
        if ($request->user()->cannot('Submit Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::findOrFail($id);

        if ($shift->teller_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only submit your own shifts.'
            ], 403);
        }

        if ($shift->status != 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Shift is not open.'
            ], 422);
        }

        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'closing_floats' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        // Convert amounts to cents (integers)
        $closingCash = (int)($request->closing_cash * 100);
        $closingFloats = [];
        if ($request->closing_floats) {
            foreach ($request->closing_floats as $provider => $amount) {
                $closingFloats[$provider] = (int)($amount * 100);
            }
        }

        try {
            DB::beginTransaction();

            // Use AccountingService to submit shift
            $shift = $this->accountingService->submitShift(
                $shift,
                $closingCash,
                $closingFloats,
                $request->notes
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift submitted successfully.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                    'variance_cash' => $shift->variance_cash ? $shift->variance_cash / 100 : null,
                    'variance_floats' => $shift->variance_floats ? array_map(function($v) { return $v / 100; }, $shift->variance_floats) : null,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit shift: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify shift
     */
    public function verify(Request $request, $id)
    {
        if ($request->user()->cannot('Verify Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::findOrFail($id);

        if ($shift->status != 'submitted') {
            return response()->json([
                'success' => false,
                'message' => 'Shift is not submitted for verification.'
            ], 422);
        }

        $request->validate([
            'approved' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $action = $request->approved ? 'approve' : 'request_adjustment';
            $adjustments = $request->adjustments ?? [];
            
            $shift = $this->accountingService->verifyShift(
                $shift,
                $action,
                $adjustments,
                $request->notes
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->approved ? 'Shift verified successfully.' : 'Shift marked as discrepancy.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify shift: ' . $e->getMessage()
            ], 500);
        }
    }
}

