<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\TellerShift;
use App\Models\MoneyPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->cannot('View Money Point Module')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $user = $request->user();

        // Get current user's open shift if teller
        $openShift = null;
        if ($user->can('View Shifts')) {
            $openShift = TellerShift::where('teller_id', $user->id)
                ->where('status', 'open')
                ->with(['teller', 'treasurer'])
                ->first();
        }

        // Get recent shifts
        $recentShifts = TellerShift::with(['teller', 'treasurer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Dashboard Statistics
        $today = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Today's transactions
        $todayTransactions = MoneyPointTransaction::whereBetween('created_at', [$today, $todayEnd])->get();
        $todayDeposits = $todayTransactions->where('type', 'deposit');
        $todayWithdrawals = $todayTransactions->where('type', 'withdrawal');

        $todayDepositAmount = $todayDeposits->sum(function ($tx) {
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();
            return $cashLine ? abs($cashLine->amount) : abs($tx->lines()->where('amount', '>', 0)->sum('amount'));
        });

        $todayWithdrawalAmount = $todayWithdrawals->sum(function ($tx) {
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();
            return $cashLine ? abs($cashLine->amount) : abs($tx->lines()->where('amount', '>', 0)->sum('amount'));
        });

        // Active shifts count
        $activeShiftsCount = TellerShift::where('status', 'open')->count();
        $pendingVerificationCount = TellerShift::where('status', 'submitted')->count();

        // Total cash in system
        $totalCash = Account::where('account_type', 'cash')
            ->where('is_active', true)
            ->sum('balance');

        // Recent transactions
        $recentTransactions = MoneyPointTransaction::with(['user', 'tellerShift.teller', 'lines.account'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($tx) {
                if (in_array($tx->type, ['withdrawal', 'deposit'])) {
                    $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                    if ($cashLine) {
                        $tx->amount = abs($cashLine->amount);
                        return $tx;
                    }
                }
                $tx->amount = abs($tx->lines->where('amount', '>', 0)->sum('amount'));
                return $tx;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'open_shift' => $openShift ? [
                    'id' => $openShift->id,
                    'teller_name' => $openShift->teller->name ?? null,
                    'opening_float' => $openShift->opening_float,
                    'opened_at' => $openShift->opened_at->toISOString(),
                ] : null,
                'statistics' => [
                    'today_deposits' => $todayDepositAmount / 100,
                    'today_withdrawals' => $todayWithdrawalAmount / 100,
                    'active_shifts' => $activeShiftsCount,
                    'pending_verification' => $pendingVerificationCount,
                    'total_cash' => $totalCash / 100,
                ],
                'recent_shifts' => $recentShifts->map(function ($shift) {
                    return [
                        'id' => $shift->id,
                        'teller_name' => $shift->teller->name ?? null,
                        'status' => $shift->status,
                        'opening_cash' => $shift->opening_cash ? $shift->opening_cash / 100 : 0,
                        'opened_at' => $shift->opened_at->toISOString(),
                    ];
                }),
                'recent_transactions' => $recentTransactions->map(function ($tx) {
                    return [
                        'id' => $tx->id,
                        'type' => $tx->type,
                        'amount' => ($tx->amount ?? 0) / 100,
                        'user_name' => $tx->user->name ?? null,
                        'created_at' => $tx->created_at->toISOString(),
                    ];
                }),
            ]
        ]);
    }
}

