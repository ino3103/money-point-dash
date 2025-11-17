<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TellerShift;
use App\Models\MoneyPointTransaction;
use App\Models\User;
use App\Models\Account;
use App\Models\FloatProvider;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Shift Summary Report
     */
    public function shiftSummary(Request $request)
    {
        if ($request->user()->cannot('View Money Point Reports')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'teller_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:open,submitted,verified,closed,discrepancy',
        ]);

        $query = TellerShift::with(['teller', 'treasurer'])
            ->whereBetween('opened_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

        if ($request->teller_id) {
            $query->where('teller_id', $request->teller_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $shifts = $query->orderBy('opened_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shifts' => $shifts->map(function ($shift) {
                    return [
                        'id' => $shift->id,
                        'teller_name' => $shift->teller->name ?? null,
                        'treasurer_name' => $shift->treasurer->name ?? null,
                        'status' => $shift->status,
                        'opening_cash' => $shift->opening_cash ? $shift->opening_cash / 100 : 0,
                        'closing_cash' => $shift->closing_cash ? $shift->closing_cash / 100 : null,
                        'variance_cash' => $shift->variance_cash ? $shift->variance_cash / 100 : null,
                        'opened_at' => $shift->opened_at->toISOString(),
                        'closed_at' => $shift->closed_at ? $shift->closed_at->toISOString() : null,
                    ];
                }),
                'summary' => [
                    'total_shifts' => $shifts->count(),
                    'total_opening_cash' => $shifts->sum('opening_cash') / 100,
                    'total_closing_cash' => $shifts->sum('closing_cash') / 100,
                    'total_variance' => $shifts->sum('variance_cash') / 100,
                ]
            ]
        ]);
    }

    /**
     * Transactions Report
     */
    public function transactions(Request $request)
    {
        if ($request->user()->cannot('View Money Point Reports')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|in:withdrawal,deposit,allocation,transfer,reconciliation,adjustment,fee',
            'teller_id' => 'nullable|exists:users,id',
        ]);

        $query = MoneyPointTransaction::with(['user', 'tellerShift.teller'])
            ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->teller_id) {
            $query->where('user_id', $request->teller_id);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $totalDeposits = 0;
        $totalWithdrawals = 0;

        foreach ($transactions as $tx) {
            $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
            if ($cashLine) {
                $amount = abs($cashLine->amount) / 100;
                if ($tx->type === 'deposit') {
                    $totalDeposits += $amount;
                } elseif ($tx->type === 'withdrawal') {
                    $totalWithdrawals += $amount;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transactions' => $transactions->map(function ($tx) {
                    $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                    return [
                        'id' => $tx->id,
                        'type' => $tx->type,
                        'amount' => $cashLine ? abs($cashLine->amount) / 100 : 0,
                        'user_name' => $tx->user->name ?? null,
                        'teller_name' => $tx->tellerShift->teller->name ?? null,
                        'reference' => $tx->reference,
                        'created_at' => $tx->created_at->toISOString(),
                    ];
                }),
                'summary' => [
                    'total_transactions' => $transactions->count(),
                    'total_deposits' => $totalDeposits,
                    'total_withdrawals' => $totalWithdrawals,
                    'net_flow' => $totalDeposits - $totalWithdrawals,
                ]
            ]
        ]);
    }

    /**
     * Float Balance Report
     */
    public function floatBalance(Request $request)
    {
        if ($request->user()->cannot('View Money Point Reports')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $query = Account::with('user')
            ->where('account_type', 'float')
            ->where('is_active', true);

        if ($request->provider_id) {
            $provider = FloatProvider::find($request->provider_id);
            if ($provider) {
                $query->where('provider', $provider->name);
            }
        }

        if ($request->teller_id) {
            $query->where('user_id', $request->teller_id);
        }

        $accounts = $query->orderBy('provider')->orderBy('user_id')->get();

        $grouped = $accounts->groupBy('provider')->map(function ($providerAccounts, $provider) {
            return [
                'provider' => $provider,
                'total_balance' => abs($providerAccounts->sum('balance')) / 100,
                'accounts' => $providerAccounts->map(function ($account) {
                    return [
                        'id' => $account->id,
                        'user_name' => $account->user->name ?? null,
                        'balance' => abs($account->balance) / 100,
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'providers' => $grouped->values(),
                'total_float' => abs($accounts->sum('balance')) / 100,
            ]
        ]);
    }

    /**
     * Variance Report
     */
    public function variance(Request $request)
    {
        if ($request->user()->cannot('View Money Point Reports')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $query = TellerShift::with(['teller', 'treasurer'])
            ->where('status', 'discrepancy');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('closed_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->teller_id) {
            $query->where('teller_id', $request->teller_id);
        }

        $shifts = $query->orderBy('closed_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'shifts' => $shifts->map(function ($shift) {
                    return [
                        'id' => $shift->id,
                        'teller_name' => $shift->teller->name ?? null,
                        'variance_cash' => $shift->variance_cash ? $shift->variance_cash / 100 : null,
                        'variance_floats' => $shift->variance_floats ? array_map(function($v) { return $v / 100; }, $shift->variance_floats) : null,
                        'closed_at' => $shift->closed_at ? $shift->closed_at->toISOString() : null,
                    ];
                }),
                'total_variance' => $shifts->sum('variance_cash') / 100,
            ]
        ]);
    }

    /**
     * Daily Summary Report
     */
    public function dailySummary(Request $request)
    {
        if ($request->user()->cannot('View Money Point Reports')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;
        $startOfDay = $date . ' 00:00:00';
        $endOfDay = $date . ' 23:59:59';

        $transactions = MoneyPointTransaction::with(['user', 'lines.account'])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->whereIn('type', ['deposit', 'withdrawal'])
            ->get();

        $totalDeposits = 0;
        $totalWithdrawals = 0;
        $depositCount = 0;
        $withdrawalCount = 0;

        foreach ($transactions as $tx) {
            $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
            if ($cashLine) {
                $amount = abs($cashLine->amount) / 100;
                if ($tx->type === 'deposit') {
                    $totalDeposits += $amount;
                    $depositCount++;
                } elseif ($tx->type === 'withdrawal') {
                    $totalWithdrawals += $amount;
                    $withdrawalCount++;
                }
            }
        }

        $shiftsOpened = TellerShift::whereDate('opened_at', $date)->count();
        $shiftsClosed = TellerShift::whereDate('closed_at', $date)->count();
        $shiftsVerified = TellerShift::whereDate('closed_at', $date)->where('status', 'verified')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'transactions' => [
                    'total_deposits' => $totalDeposits,
                    'total_withdrawals' => $totalWithdrawals,
                    'deposit_count' => $depositCount,
                    'withdrawal_count' => $withdrawalCount,
                    'net_flow' => $totalDeposits - $totalWithdrawals,
                ],
                'shifts' => [
                    'opened' => $shiftsOpened,
                    'closed' => $shiftsClosed,
                    'verified' => $shiftsVerified,
                ]
            ]
        ]);
    }

    /**
     * Teller Performance Report
     */
    public function tellerPerformance(Request $request)
    {
        if ($request->user()->cannot('View Money Point Reports')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tellers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teller');
        })->get();

        $performance = [];

        foreach ($tellers as $teller) {
            $shifts = TellerShift::where('teller_id', $teller->id)
                ->whereBetween('opened_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->get();

            $transactions = MoneyPointTransaction::with('lines.account')
                ->where('user_id', $teller->id)
                ->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59'])
                ->whereIn('type', ['deposit', 'withdrawal'])
                ->get();

            $totalDeposits = 0;
            $totalWithdrawals = 0;

            foreach ($transactions as $tx) {
                $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                if ($cashLine) {
                    $amount = abs($cashLine->amount) / 100;
                    if ($tx->type === 'deposit') {
                        $totalDeposits += $amount;
                    } elseif ($tx->type === 'withdrawal') {
                        $totalWithdrawals += $amount;
                    }
                }
            }

            $performance[] = [
                'teller_id' => $teller->id,
                'teller_name' => $teller->name,
                'shifts_count' => $shifts->count(),
                'shifts_verified' => $shifts->where('status', 'verified')->count(),
                'shifts_discrepancy' => $shifts->where('status', 'discrepancy')->count(),
                'transactions_count' => $transactions->count(),
                'total_deposits' => $totalDeposits,
                'total_withdrawals' => $totalWithdrawals,
                'net_flow' => $totalDeposits - $totalWithdrawals,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $performance
        ]);
    }
}

