<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TellerShift;
use App\Models\User;
use App\Models\FloatProvider;
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
                // Format opening floats preserving keys
                $openingFloatsFormatted = [];
                if ($shift->opening_floats) {
                    foreach ($shift->opening_floats as $provider => $amount) {
                        $openingFloatsFormatted[$provider] = abs($amount);
                    }
                }

                // Format closing floats preserving keys
                $closingFloatsFormatted = null;
                if ($shift->closing_floats) {
                    $closingFloatsFormatted = [];
                    foreach ($shift->closing_floats as $provider => $amount) {
                        $closingFloatsFormatted[$provider] = abs($amount);
                    }
                }

                // Format variance floats preserving keys
                $varianceFloatsFormatted = null;
                if ($shift->variance_floats) {
                    $varianceFloatsFormatted = [];
                    foreach ($shift->variance_floats as $provider => $amount) {
                        $varianceFloatsFormatted[$provider] = $amount;
                    }
                }

                return [
                    'id' => $shift->id,
                    'teller_id' => $shift->teller_id,
                    'teller_name' => $shift->teller->name ?? null,
                    'treasurer_id' => $shift->treasurer_id,
                    'treasurer_name' => $shift->treasurer->name ?? null,
                    'status' => $shift->status,
                    'opening_cash' => $shift->opening_cash ? $shift->opening_cash : 0,
                    'opening_floats' => $openingFloatsFormatted,
                    'closing_cash' => $shift->closing_cash ? $shift->closing_cash : null,
                    'closing_floats' => $closingFloatsFormatted,
                    'variance_cash' => $shift->variance_cash ? $shift->variance_cash : null,
                    'variance_floats' => $varianceFloatsFormatted,
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

        // Get provider display names
        $providerNames = [];
        if ($shift->opening_floats) {
            foreach ($shift->opening_floats as $provider => $amount) {
                $providerModel = FloatProvider::where('name', $provider)->first();
                $providerNames[$provider] = $providerModel ? $providerModel->display_name : ucfirst($provider);
            }
        }

        // Calculate actual starting balances from transaction lines (after allocation)
        $actualStartingCash = $shift->opening_cash;
        $actualStartingFloats = $shift->opening_floats ?? [];

        // Get cash account balance after allocation transaction
        $cashAllocationTx = $shift->transactions->where('type', 'allocation')
            ->where('metadata->allocation_type', 'cash')
            ->first();
        if ($cashAllocationTx) {
            $cashLine = $cashAllocationTx->lines->where('account.account_type', 'cash')->first();
            if ($cashLine) {
                $actualStartingCash = $cashLine->balance_after;
            }
        }

        // Get float account balances after allocation transactions
        foreach ($shift->opening_floats ?? [] as $provider => $amount) {
            $floatAllocationTx = $shift->transactions->where('type', 'allocation')
                ->where('metadata->allocation_type', 'float')
                ->where('metadata->provider', $provider)
                ->first();
            if ($floatAllocationTx) {
                $floatLine = $floatAllocationTx->lines->where('account.account_type', 'float')
                    ->where('account.provider', $provider)
                    ->first();
                if ($floatLine) {
                    $actualStartingFloats[$provider] = abs($floatLine->balance_after);
                }
            } else {
                // If no allocation transaction found, use the stored opening float value
                $actualStartingFloats[$provider] = abs($amount);
            }
        }

        // Calculate Mtaji (Opening Capital) = Actual Starting Cash + Sum of Actual Starting Floats
        // All values are in cents, so we sum them first, then divide by 100
        $mtaji = $actualStartingCash;
        foreach ($actualStartingFloats as $provider => $amount) {
            $mtaji += abs($amount);
        }

        // Calculate Balanced (Closing Capital) = Closing Cash + Sum of Closing Floats (if submitted)
        $balanced = null;
        if ($shift->closing_cash !== null) {
            $balanced = $shift->closing_cash;
            if ($shift->closing_floats) {
                foreach ($shift->closing_floats as $amount) {
                    $balanced += abs($amount);
                }
            }
        }

        // Format opening floats with provider names
        $openingFloatsFormatted = [];
        if ($shift->opening_floats) {
            foreach ($shift->opening_floats as $provider => $amount) {
                $openingFloatsFormatted[] = [
                    'provider' => $provider,
                    'display_name' => $providerNames[$provider] ?? ucfirst($provider),
                    'amount' => abs($amount),
                ];
            }
        }

        // Format closing floats with provider names
        $closingFloatsFormatted = null;
        if ($shift->closing_floats) {
            $closingFloatsFormatted = [];
            foreach ($shift->closing_floats as $provider => $amount) {
                $closingFloatsFormatted[] = [
                    'provider' => $provider,
                    'display_name' => $providerNames[$provider] ?? ucfirst($provider),
                    'amount' => abs($amount),
                ];
            }
        }

        // Format variance floats
        $varianceFloatsFormatted = null;
        if ($shift->variance_floats) {
            $varianceFloatsFormatted = [];
            foreach ($shift->variance_floats as $provider => $amount) {
                $varianceFloatsFormatted[] = [
                    'provider' => $provider,
                    'display_name' => $providerNames[$provider] ?? ucfirst($provider),
                    'amount' => $amount,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shift->id,
                'teller_id' => $shift->teller_id,
                'teller_name' => $shift->teller->name ?? null,
                'treasurer_id' => $shift->treasurer_id,
                'treasurer_name' => $shift->treasurer->name ?? null,
                'status' => $shift->status,
                'opening_cash' => $shift->opening_cash ? $shift->opening_cash : 0,
                'opening_floats' => $openingFloatsFormatted,
                'actual_starting_cash' => $actualStartingCash,
                'actual_starting_floats' => array_map(function ($v) {
                    return abs($v);
                }, $actualStartingFloats),
                'mtaji' => $mtaji, // Opening Capital (Cash + Sum of all Floats)
                'closing_cash' => $shift->closing_cash ? $shift->closing_cash : null,
                'closing_floats' => $closingFloatsFormatted,
                'expected_closing_cash' => $shift->expected_closing_cash ? $shift->expected_closing_cash : null,
                'expected_closing_floats' => $shift->expected_closing_floats ? array_map(function ($v) {
                    return abs($v);
                }, $shift->expected_closing_floats) : null,
                'variance_cash' => $shift->variance_cash ? $shift->variance_cash : null,
                'variance_floats' => $varianceFloatsFormatted,
                'balanced' => $balanced ? $balanced : null, // Closing Capital
                'opened_at' => $shift->opened_at->toISOString(),
                'closed_at' => $shift->closed_at ? $shift->closed_at->toISOString() : null,
                'notes' => $shift->notes,
                'can_submit' => $shift->canSubmit(),
                'can_verify' => $shift->canVerify(),
                'transactions' => $shift->transactions->map(function ($tx) {
                    $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                    return [
                        'id' => $tx->id,
                        'type' => $tx->type,
                        'amount' => $cashLine ? abs($cashLine->amount) : 0,
                        'user_name' => $tx->user->name ?? null,
                        'created_at' => $tx->created_at->toISOString(),
                    ];
                }),
            ]
        ]);
    }

    /**
     * Get data needed for opening a shift (tellers, float providers, previous balances)
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('Open Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        // Get users with Teller role
        $tellers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teller');
        })->get()->map(function ($teller) {
            return [
                'id' => $teller->id,
                'name' => $teller->name,
                'email' => $teller->email,
                'username' => $teller->username,
            ];
        });

        // Get active float providers
        $floatProviders = FloatProvider::getActive()->map(function ($provider) {
            return [
                'name' => $provider->name,
                'display_name' => $provider->display_name,
                'type' => $provider->type,
                'is_active' => $provider->is_active,
            ];
        });

        // Get previous closing balances for each teller
        $previousClosingBalances = [];
        foreach ($tellers as $teller) {
            $previousShift = TellerShift::where('teller_id', $teller['id'])
                ->whereIn('status', ['verified', 'closed'])
                ->whereNotNull('closing_cash')
                ->orderByRaw('COALESCE(closed_at, updated_at) DESC')
                ->orderBy('id', 'desc')
                ->first();

            if ($previousShift) {
                $previousClosingBalances[$teller['id']] = [
                    'cash' => $previousShift->closing_cash ? $previousShift->closing_cash : 0,
                    'floats' => $previousShift->closing_floats ? array_map(function ($v) {
                        return abs($v);
                    }, $previousShift->closing_floats) : [],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tellers' => $tellers,
                'float_providers' => $floatProviders,
                'previous_closing_balances' => $previousClosingBalances,
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
            'teller_id' => 'required|exists:users,id',
            'opening_cash' => 'required|numeric|min:0',
            'opening_floats' => 'nullable|array',
            'use_previous_cash' => 'nullable|boolean',
            'use_previous_float' => 'nullable|array',
        ]);

        // Convert amounts to integers (amounts are already in currency format)
        $openingCash = (int)$request->opening_cash;
        $openingFloats = [];
        if ($request->opening_floats) {
            foreach ($request->opening_floats as $provider => $amount) {
                if ($amount > 0) {
                    $openingFloats[$provider] = (int)$amount;
                }
            }
        }

        // Validate amounts
        if ($openingCash <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Opening cash must be greater than 0.'
            ], 422);
        }

        try {
            $teller = User::findOrFail($request->teller_id);

            // Verify the user has the Teller role
            if (!$teller->hasRole('Teller')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user does not have the "Teller" role.'
                ], 422);
            }

            // Check if teller already has an open shift
            $existingShift = TellerShift::where('teller_id', $teller->id)
                ->where('status', 'open')
                ->first();

            if ($existingShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teller already has an open shift. Please close it first.'
                ], 422);
            }

            // Get flags for using previous closing balances
            $usePreviousCash = $request->input('use_previous_cash') === true || $request->input('use_previous_cash') === 1 || $request->input('use_previous_cash') === '1';
            $usePreviousFloats = [];
            if ($request->has('use_previous_float') && is_array($request->use_previous_float)) {
                foreach ($request->use_previous_float as $provider => $value) {
                    if ($value === true || $value === 1 || $value === '1') {
                        $usePreviousFloats[$provider] = true;
                    }
                }
            }

            DB::beginTransaction();

            // Use AccountingService to open shift
            $shift = $this->accountingService->openShift(
                $request->user(), // treasurer
                $teller, // teller
                $openingCash,
                $openingFloats,
                $usePreviousCash,
                $usePreviousFloats
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift opened successfully.',
                'data' => [
                    'id' => $shift->id,
                    'teller_id' => $shift->teller_id,
                    'teller_name' => $shift->teller->name ?? null,
                    'treasurer_id' => $shift->treasurer_id,
                    'treasurer_name' => $shift->treasurer->name ?? null,
                    'status' => $shift->status,
                    'opening_cash' => $shift->opening_cash ? $shift->opening_cash : 0,
                    'opening_floats' => $shift->opening_floats ? array_map(function ($v) {
                        return abs($v);
                    }, $shift->opening_floats) : [],
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
     * Get shift data for submission form
     */
    public function submitForm(Request $request, $id)
    {
        if ($request->user()->cannot('Submit Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::with(['teller'])->findOrFail($id);

        if (!$shift->canSubmit() || $shift->teller_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot submit this shift.'
            ], 403);
        }

        // Get provider display names
        $providerNames = [];
        if ($shift->opening_floats) {
            foreach ($shift->opening_floats as $provider => $amount) {
                $providerModel = FloatProvider::where('name', $provider)->first();
                $providerNames[$provider] = $providerModel ? $providerModel->display_name : ucfirst($provider);
            }
        }

        // Format opening floats with provider names
        $openingFloatsFormatted = [];
        if ($shift->opening_floats) {
            foreach ($shift->opening_floats as $provider => $amount) {
                $providerModel = FloatProvider::where('name', $provider)->first();
                $openingFloatsFormatted[] = [
                    'provider' => $provider,
                    'display_name' => $providerNames[$provider] ?? ucfirst($provider),
                    'type' => $providerModel ? $providerModel->type : null,
                    'amount' => abs($amount),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shift->id,
                'teller_name' => $shift->teller->name ?? null,
                'opening_cash' => $shift->opening_cash ? $shift->opening_cash : 0,
                'opening_floats' => $openingFloatsFormatted,
                'expected_closing_cash' => $shift->expected_closing_cash ? $shift->expected_closing_cash : null,
                'expected_closing_floats' => $shift->expected_closing_floats ? array_map(function ($v) {
                    return abs($v);
                }, $shift->expected_closing_floats) : null,
            ]
        ]);
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

        if (!$shift->canSubmit() || $shift->teller_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot submit this shift.'
            ], 403);
        }

        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'closing_floats' => 'required|array',
            'closing_floats.*' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Convert amounts to integers (amounts are already in currency format)
        $closingCash = (int)$request->closing_cash;
        $closingFloats = [];
        foreach ($request->closing_floats as $provider => $amount) {
            $closingFloats[$provider] = (int)$amount;
        }

        // Validate amounts
        if ($closingCash < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Closing cash must be greater than or equal to 0.'
            ], 422);
        }

        foreach ($closingFloats as $provider => $amount) {
            if ($amount < 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Closing float for {$provider} must be greater than or equal to 0."
                ], 422);
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

            // Format variance floats
            $varianceFloatsFormatted = null;
            if ($shift->variance_floats) {
                $varianceFloatsFormatted = [];
                foreach ($shift->variance_floats as $provider => $amount) {
                    $providerModel = FloatProvider::where('name', $provider)->first();
                    $varianceFloatsFormatted[] = [
                        'provider' => $provider,
                        'display_name' => $providerModel ? $providerModel->display_name : ucfirst($provider),
                        'amount' => $amount,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Shift submitted successfully.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                    'closing_cash' => $shift->closing_cash ? $shift->closing_cash : null,
                    'closing_floats' => $shift->closing_floats ? array_map(function ($v) {
                        return abs($v);
                    }, $shift->closing_floats) : null,
                    'variance_cash' => $shift->variance_cash ? $shift->variance_cash : null,
                    'variance_floats' => $varianceFloatsFormatted,
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
     * Get shift data for verification form
     */
    public function verifyForm(Request $request, $id)
    {
        if ($request->user()->cannot('Verify Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::with(['teller', 'treasurer'])->findOrFail($id);

        if (!$shift->canVerify()) {
            return response()->json([
                'success' => false,
                'message' => 'Shift cannot be verified.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $shift->id,
                'teller_name' => $shift->teller->name ?? null,
                'treasurer_name' => $shift->treasurer->name ?? null,
                'status' => $shift->status,
                'closing_cash' => $shift->closing_cash ? $shift->closing_cash : null,
                'closing_floats' => $shift->closing_floats ? array_map(function ($v) {
                    return abs($v);
                }, $shift->closing_floats) : null,
                'expected_closing_cash' => $shift->expected_closing_cash ? $shift->expected_closing_cash : null,
                'expected_closing_floats' => $shift->expected_closing_floats ? array_map(function ($v) {
                    return abs($v);
                }, $shift->expected_closing_floats) : null,
                'variance_cash' => $shift->variance_cash ? $shift->variance_cash : null,
                'variance_floats' => $shift->variance_floats ? array_map(function ($v) {
                    return $v;
                }, $shift->variance_floats) : null,
                'notes' => $shift->notes,
            ]
        ]);
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

        if (!$shift->canVerify()) {
            return response()->json([
                'success' => false,
                'message' => 'Shift cannot be verified.'
            ], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,request_adjustment',
            'adjustments' => 'required_if:action,request_adjustment|array',
            'adjustments.*.account_id' => 'required_if:action,request_adjustment|exists:accounts,id',
            'adjustments.*.amount' => 'required_if:action,request_adjustment|numeric',
            'adjustments.*.reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Process adjustments if present
            $adjustments = [];
            if ($request->action === 'request_adjustment' && $request->has('adjustments')) {
                foreach ($request->adjustments as $adjustment) {
                    $adjustments[] = [
                        'account_id' => $adjustment['account_id'],
                        'amount' => (int)$adjustment['amount'], // Amounts are in currency format
                        'reason' => $adjustment['reason'] ?? 'Reconciliation adjustment',
                    ];
                }
            }

            $shift = $this->accountingService->verifyShift(
                $shift,
                $request->action,
                $adjustments,
                $request->notes
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->action === 'approve' ? 'Shift verified successfully.' : 'Shift marked as discrepancy.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify shift: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept shift by teller
     */
    public function acceptShift(Request $request, $id)
    {
        if ($request->user()->cannot('Submit Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::findOrFail($id);

        if (!$shift->canAccept()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot accept this shift.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $shift = $this->accountingService->acceptShift($shift);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift accepted successfully.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                    'accepted_at' => $shift->accepted_at->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept shift: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject shift by teller
     */
    public function rejectShift(Request $request, $id)
    {
        if ($request->user()->cannot('Submit Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::findOrFail($id);

        if (!$shift->canReject()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot reject this shift.'
            ], 422);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $shift = $this->accountingService->rejectShift($shift, $request->rejection_reason);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift rejected. The treasurer will review your concerns.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                    'rejection_reason' => $shift->rejection_reason,
                    'rejected_at' => $shift->rejected_at->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject shift: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm funds by teller
     */
    public function confirmFunds(Request $request, $id)
    {
        if ($request->user()->cannot('Submit Shifts')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $shift = TellerShift::findOrFail($id);

        if (!$shift->canConfirm()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot confirm this shift.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $shift = $this->accountingService->confirmFunds($shift);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Funds confirmed. You can now start working.',
                'data' => [
                    'id' => $shift->id,
                    'status' => $shift->status,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm funds: ' . $e->getMessage()
            ], 500);
        }
    }
}
