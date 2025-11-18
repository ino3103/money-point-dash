<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MoneyPointTransaction;
use App\Models\TellerShift;
use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * List all transactions
     */
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Money Point Transactions')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $query = MoneyPointTransaction::with(['user', 'tellerShift.teller', 'lines.account']);

        // Filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('shift_id')) {
            $query->where('teller_shift_id', $request->shift_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 50);

        return response()->json([
            'success' => true,
            'data' => $transactions->through(function ($tx) {
                $amount = 0;
                if (in_array($tx->type, ['withdrawal', 'deposit'])) {
                    $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                    if ($cashLine) {
                        $amount = abs($cashLine->amount);
                    }
                } else {
                    $amount = abs($tx->lines->where('amount', '>', 0)->sum('amount'));
                }

                // Generate print URL for withdrawal and deposit transactions
                $printUrl = null;
                if (in_array($tx->type, ['withdrawal', 'deposit'])) {
                    $printUrl = url('/money-point/transactions/' . $tx->id . '/print');
                }

                return [
                    'id' => $tx->id,
                    'uuid' => $tx->uuid,
                    'type' => $tx->type,
                    'reference' => $tx->reference,
                    'user_id' => $tx->user_id,
                    'user_name' => $tx->user->name ?? null,
                    'shift_id' => $tx->teller_shift_id,
                    'teller_name' => $tx->tellerShift->teller->name ?? null,
                    'amount' => $amount,
                    'created_at' => $tx->created_at->toISOString(),
                    'print_url' => $printUrl,
                ];
            }),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]);
    }

    /**
     * Show transaction details
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('View Money Point Transactions')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $transaction = MoneyPointTransaction::with(['user', 'tellerShift.teller', 'tellerShift.treasurer', 'lines.account.user'])
            ->findOrFail($id);

        $cashLine = $transaction->lines->firstWhere('account.account_type', 'cash');
        $amount = $cashLine ? abs($cashLine->amount) : abs($transaction->lines->where('amount', '>', 0)->sum('amount'));

        // Generate print URL for withdrawal and deposit transactions
        $printUrl = null;
        if (in_array($transaction->type, ['withdrawal', 'deposit'])) {
            $printUrl = url('/money-point/transactions/' . $transaction->id . '/print');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $transaction->id,
                'uuid' => $transaction->uuid,
                'type' => $transaction->type,
                'reference' => $transaction->reference,
                'user_id' => $transaction->user_id,
                'user_name' => $transaction->user->name ?? null,
                'shift_id' => $transaction->teller_shift_id,
                'shift_status' => $transaction->tellerShift->status ?? null,
                'teller_name' => $transaction->tellerShift->teller->name ?? null,
                'amount' => $amount,
                'created_at' => $transaction->created_at->toISOString(),
                'print_url' => $printUrl,
                'metadata' => $transaction->metadata,
                'lines' => $transaction->lines->map(function ($line) {
                    return [
                        'id' => $line->id,
                        'account_id' => $line->account_id,
                        'account_type' => $line->account->account_type,
                        'provider' => $line->account->provider,
                        'amount' => $line->amount,
                        'description' => $line->description,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Create withdrawal
     */
    public function withdraw(Request $request)
    {
        if ($request->user()->cannot('Create Withdrawals')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'provider' => 'required|string',
            'reference' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'account_no' => 'nullable|string|max:255',
        ]);

        $amount = (int)$request->amount; // Amount is in currency units

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Amount must be greater than 0.'
            ], 422);
        }

        // Determine if provider is bank or mobile money
        $providerModel = \App\Models\FloatProvider::where('name', $request->provider)->first();
        $isBankProvider = $providerModel && $providerModel->type === 'bank';

        // Validate based on provider type
        if ($isBankProvider) {
            if (!$request->account_no) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account number is required for bank transactions.'
                ], 422);
            }
        } else {
            if (!$request->customer_phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer phone number is required for mobile money transactions.'
                ], 422);
            }
        }

        $shift = TellerShift::where('teller_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            // Check if shift exists but is pending confirmation
            $pendingShift = TellerShift::where('teller_id', $request->user()->id)
                ->where('status', 'pending_teller_confirmation')
                ->first();

            if ($pendingShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must confirm the funds before performing transactions. Please confirm your shift funds first.'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'You must have an open shift to perform transactions.'
            ], 422);
        }

        if ($shift->isRejected()) {
            return response()->json([
                'success' => false,
                'message' => 'This shift has been rejected. You cannot perform transactions until the treasurer reviews and corrects the issues.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $metadata = [
                'reference' => $request->reference,
                'customer_name' => $request->customer_name,
            ];

            if ($isBankProvider) {
                $metadata['account_no'] = $request->account_no;
            } else {
                $metadata['customer_phone'] = $request->customer_phone;
            }

            $transaction = $this->accountingService->processWithdrawal(
                $request->user(),
                $shift,
                $request->provider,
                $amount,
                $metadata
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal processed successfully.',
                'data' => [
                    'id' => $transaction->id,
                    'uuid' => $transaction->uuid,
                    'type' => $transaction->type,
                    'amount' => $amount,
                    'created_at' => $transaction->created_at->toISOString(),
                    'print_url' => url('/money-point/transactions/' . $transaction->id . '/print'),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process withdrawal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create deposit
     */
    public function deposit(Request $request)
    {
        if ($request->user()->cannot('Create Deposits')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'provider' => 'required|string',
            'reference' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'account_no' => 'nullable|string|max:255',
        ]);

        $amount = (int)$request->amount; // Amount is in currency units

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Amount must be greater than 0.'
            ], 422);
        }

        // Determine if provider is bank or mobile money
        $providerModel = \App\Models\FloatProvider::where('name', $request->provider)->first();
        $isBankProvider = $providerModel && $providerModel->type === 'bank';

        // Validate based on provider type
        if ($isBankProvider) {
            if (!$request->account_no) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account number is required for bank transactions.'
                ], 422);
            }
        } else {
            if (!$request->customer_phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer phone number is required for mobile money transactions.'
                ], 422);
            }
        }

        $shift = TellerShift::where('teller_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            // Check if shift exists but is pending confirmation
            $pendingShift = TellerShift::where('teller_id', $request->user()->id)
                ->where('status', 'pending_teller_confirmation')
                ->first();

            if ($pendingShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must confirm the funds before performing transactions. Please confirm your shift funds first.'
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'You must have an open shift to perform transactions.'
            ], 422);
        }

        if ($shift->isRejected()) {
            return response()->json([
                'success' => false,
                'message' => 'This shift has been rejected. You cannot perform transactions until the treasurer reviews and corrects the issues.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $metadata = [
                'reference' => $request->reference,
                'customer_name' => $request->customer_name,
            ];

            if ($isBankProvider) {
                $metadata['account_no'] = $request->account_no;
            } else {
                $metadata['customer_phone'] = $request->customer_phone;
            }

            $transaction = $this->accountingService->processDeposit(
                $request->user(),
                $shift,
                $request->provider,
                $amount,
                $metadata
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Deposit processed successfully.',
                'data' => [
                    'id' => $transaction->id,
                    'uuid' => $transaction->uuid,
                    'type' => $transaction->type,
                    'amount' => $amount,
                    'created_at' => $transaction->created_at->toISOString(),
                    'print_url' => url('/money-point/transactions/' . $transaction->id . '/print'),
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process deposit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get print receipt URL
     */
    public function printReceipt(Request $request, $id)
    {
        if ($request->user()->cannot('View Money Point Transactions')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $transaction = MoneyPointTransaction::findOrFail($id);

        // Only allow printing for withdrawal and deposit transactions
        if (!in_array($transaction->type, ['withdrawal', 'deposit'])) {
            return response()->json([
                'success' => false,
                'message' => 'Print receipt is only available for withdrawal and deposit transactions.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'print_url' => url('/money-point/transactions/' . $transaction->id . '/print'),
            ]
        ]);
    }
}
