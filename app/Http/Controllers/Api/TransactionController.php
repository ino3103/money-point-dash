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
                $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                return [
                    'id' => $tx->id,
                    'uuid' => $tx->uuid,
                    'type' => $tx->type,
                    'reference' => $tx->reference,
                    'user_id' => $tx->user_id,
                    'user_name' => $tx->user->name ?? null,
                    'shift_id' => $tx->teller_shift_id,
                    'teller_name' => $tx->tellerShift->teller->name ?? null,
                    'amount' => $cashLine ? abs($cashLine->amount) : 0,
                    'created_at' => $tx->created_at->toISOString(),
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
            'customer_phone' => 'nullable|string',
        ]);

        $amount = (int)$request->amount; // Amount is in currency units

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Amount must be greater than 0.'
            ], 422);
        }

        $shift = TellerShift::where('teller_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'You must have an open shift to perform transactions.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $transaction = $this->accountingService->processWithdrawal(
                $request->user(),
                $shift,
                $request->provider,
                $amount,
                [
                    'reference' => $request->reference,
                    'customer_phone' => $request->customer_phone,
                ]
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
            'customer_phone' => 'nullable|string',
        ]);

        $amount = (int)$request->amount; // Amount is in currency units

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Amount must be greater than 0.'
            ], 422);
        }

        $shift = TellerShift::where('teller_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return response()->json([
                'success' => false,
                'message' => 'You must have an open shift to perform transactions.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $transaction = $this->accountingService->processDeposit(
                $request->user(),
                $shift,
                $request->provider,
                $amount,
                [
                    'reference' => $request->reference,
                    'customer_phone' => $request->customer_phone,
                ]
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
}
