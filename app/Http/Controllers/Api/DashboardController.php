<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\TellerShift;
use App\Models\MoneyPointTransaction;
use App\Models\FloatProvider;
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
        $discrepancyShiftsCount = TellerShift::where('status', 'discrepancy')->count();

        // Total cash in system (stored in cents, but web displays as-is)
        // Based on web behavior, amounts appear to be stored in currency format (not cents)
        // So we don't divide by 100 here
        $totalCash = Account::where('account_type', 'cash')
            ->where('is_active', true)
            ->sum('balance');

        // Float balances by provider with teller details
        $floatProviders = FloatProvider::all()->keyBy('name');
        $floatBalances = Account::where('account_type', 'float')
            ->where('is_active', true)
            ->with('user')
            ->get()
            ->map(function ($account) use ($floatProviders) {
                $providerInfo = $floatProviders->get($account->provider);
                return [
                    'id' => $account->id,
                    'provider' => $account->provider,
                    'display_name' => $providerInfo ? $providerInfo->display_name : ucfirst($account->provider),
                    'type' => $providerInfo ? $providerInfo->type : null,
                    'user_id' => $account->user_id,
                    'user_name' => $account->user->name ?? 'System',
                    'balance' => abs($account->balance),
                    'system_balance' => $account->balance,
                ];
            })
            ->groupBy('provider')
            ->map(function ($accounts, $provider) {
                $total = $accounts->sum('system_balance');
                return [
                    'provider' => $provider,
                    'display_name' => $accounts->first()['display_name'],
                    'type' => $accounts->first()['type'],
                    'total' => abs($total),
                    'system_total' => $total,
                    'accounts' => $accounts->values(),
                    'accounts_count' => $accounts->count(),
                ];
            });

        // Low float alerts
        $lowFloatThresholdSetting = (int) getSetting('money_point_low_float_threshold', 5000000);
        $lowFloatThreshold = -$lowFloatThresholdSetting;
        $lowFloatAlerts = Account::where('account_type', 'float')
            ->where('is_active', true)
            ->where('balance', '>=', $lowFloatThreshold)
            ->with('user')
            ->get()
            ->map(function ($account) {
                return [
                    'provider' => $account->provider,
                    'user' => $account->user->name ?? 'System',
                    'balance' => abs($account->balance),
                    'system_balance' => $account->balance
                ];
            });

        // Count transactions by type for today
        $todayTransactionsByType = $todayTransactions->groupBy('type')->map->count();

        // Total Float Capital (sum of all float balances)
        $totalFloatCapital = $floatBalances->sum(function ($providerData) {
            return abs($providerData['system_total']);
        });

        // This Week vs Last Week Comparison
        $thisWeekStart = now()->startOfWeek();
        $thisWeekEnd = now()->endOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        $thisWeekTransactions = MoneyPointTransaction::whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->get();
        $lastWeekTransactions = MoneyPointTransaction::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->get();

        $thisWeekDepositAmount = $thisWeekTransactions->where('type', 'deposit')->sum(function ($tx) {
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();
            return $cashLine ? abs($cashLine->amount) : 0;
        });

        $lastWeekDepositAmount = $lastWeekTransactions->where('type', 'deposit')->sum(function ($tx) {
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();
            return $cashLine ? abs($cashLine->amount) : 0;
        });

        $thisWeekWithdrawalAmount = $thisWeekTransactions->where('type', 'withdrawal')->sum(function ($tx) {
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();
            return $cashLine ? abs($cashLine->amount) : 0;
        });

        $lastWeekWithdrawalAmount = $lastWeekTransactions->where('type', 'withdrawal')->sum(function ($tx) {
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();
            return $cashLine ? abs($cashLine->amount) : 0;
        });

        $depositChange = $lastWeekDepositAmount > 0
            ? (($thisWeekDepositAmount - $lastWeekDepositAmount) / $lastWeekDepositAmount) * 100
            : 0;
        $withdrawalChange = $lastWeekWithdrawalAmount > 0
            ? (($thisWeekWithdrawalAmount - $lastWeekWithdrawalAmount) / $lastWeekWithdrawalAmount) * 100
            : 0;

        // Average Transaction Size
        $avgDepositSize = $todayDeposits->count() > 0 ? $todayDepositAmount / $todayDeposits->count() : 0;
        $avgWithdrawalSize = $todayWithdrawals->count() > 0 ? $todayWithdrawalAmount / $todayWithdrawals->count() : 0;

        // Shift Performance Metrics
        $todayShifts = TellerShift::whereDate('opened_at', today())->get();
        $todayShiftsCount = $todayShifts->count();
        $todayVerifiedShifts = $todayShifts->where('status', 'verified')->count();
        $todayCompletedShifts = $todayShifts->whereIn('status', ['verified', 'closed'])->count();

        $shiftCompletionRate = $todayShiftsCount > 0
            ? ($todayCompletedShifts / $todayShiftsCount) * 100
            : 0;

        // Average transactions per shift
        $avgTransactionsPerShift = 0;
        if ($todayShiftsCount > 0) {
            $totalTransactionsInShifts = MoneyPointTransaction::whereIn('teller_shift_id', $todayShifts->pluck('id'))->count();
            $avgTransactionsPerShift = $totalTransactionsInShifts / $todayShiftsCount;
        }

        // Total Mtaji in System (sum of opening capitals for active shifts)
        $totalMtajiInSystem = TellerShift::where('status', 'open')
            ->get()
            ->sum(function ($shift) {
                $mtaji = $shift->opening_cash;
                if ($shift->opening_floats) {
                    foreach ($shift->opening_floats as $amount) {
                        $mtaji += abs($amount);
                    }
                }
                return $mtaji;
            });

        // Top Providers by Transaction Volume (today)
        $providerVolumes = [];
        $todayTransactionsWithLines = MoneyPointTransaction::with(['lines.account'])
            ->whereBetween('created_at', [$today, $todayEnd])
            ->whereIn('type', ['deposit', 'withdrawal'])
            ->get();

        foreach ($todayTransactionsWithLines as $tx) {
            $floatLine = $tx->lines->firstWhere('account.account_type', 'float');
            if ($floatLine && $floatLine->account) {
                $provider = $floatLine->account->provider;
                if (!isset($providerVolumes[$provider])) {
                    $providerModel = $floatProviders->get($provider);
                    $providerVolumes[$provider] = [
                        'count' => 0,
                        'amount' => 0,
                        'display_name' => $providerModel ? $providerModel->display_name : ucfirst($provider)
                    ];
                }
                $providerVolumes[$provider]['count']++;
                $providerVolumes[$provider]['amount'] += abs($floatLine->amount);
            }
        }
        // Sort by amount descending
        uasort($providerVolumes, function ($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        // Convert provider volumes amounts to currency
        $providerVolumes = array_map(function ($provider) {
            return [
                'provider' => $provider['display_name'],
                'count' => $provider['count'],
                'amount' => $provider['amount'],
            ];
        }, array_values($providerVolumes));

        // Cash vs Float Ratio
        $totalCapital = $totalCash + $totalFloatCapital;
        $cashRatio = $totalCapital > 0 ? ($totalCash / $totalCapital) * 100 : 0;
        $floatRatio = $totalCapital > 0 ? ($totalFloatCapital / $totalCapital) * 100 : 0;

        // Discrepancy Rate
        $totalShifts = TellerShift::count();
        $discrepancyRate = $totalShifts > 0
            ? ($discrepancyShiftsCount / $totalShifts) * 100
            : 0;

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
                    'treasurer_name' => $openShift->treasurer->name ?? null,
                    'opening_cash' => $openShift->opening_cash ? $openShift->opening_cash : 0,
                    'opening_floats' => $openShift->opening_floats ? array_map(function ($amount) {
                        return abs($amount);
                    }, $openShift->opening_floats) : [],
                    'opened_at' => $openShift->opened_at->toISOString(),
                    'status' => $openShift->status,
                ] : null,
                'statistics' => [
                    'today_transactions_count' => $todayTransactions->count(),
                    'today_deposits_count' => $todayDeposits->count(),
                    'today_withdrawals_count' => $todayWithdrawals->count(),
                    'today_transactions_by_type' => $todayTransactionsByType,
                    'today_deposits' => $todayDepositAmount,
                    'today_withdrawals' => $todayWithdrawalAmount,
                    'active_shifts' => $activeShiftsCount,
                    'pending_verification' => $pendingVerificationCount,
                    'discrepancy_shifts' => $discrepancyShiftsCount,
                    'total_cash' => $totalCash,
                    'total_float_capital' => $totalFloatCapital,
                    'total_mtaji_in_system' => $totalMtajiInSystem,
                    'this_week_deposits' => $thisWeekDepositAmount,
                    'last_week_deposits' => $lastWeekDepositAmount,
                    'this_week_withdrawals' => $thisWeekWithdrawalAmount,
                    'last_week_withdrawals' => $lastWeekWithdrawalAmount,
                    'deposit_change_percent' => round($depositChange, 2),
                    'withdrawal_change_percent' => round($withdrawalChange, 2),
                    'avg_deposit_size' => $avgDepositSize,
                    'avg_withdrawal_size' => $avgWithdrawalSize,
                    'today_shifts_count' => $todayShiftsCount,
                    'today_verified_shifts' => $todayVerifiedShifts,
                    'shift_completion_rate' => round($shiftCompletionRate, 2),
                    'avg_transactions_per_shift' => round($avgTransactionsPerShift, 2),
                    'cash_ratio' => round($cashRatio, 2),
                    'float_ratio' => round($floatRatio, 2),
                    'discrepancy_rate' => round($discrepancyRate, 2),
                ],
                'float_balances' => $floatBalances->values(),
                'low_float_alerts' => $lowFloatAlerts,
                'provider_volumes' => $providerVolumes,
                'recent_shifts' => $recentShifts->map(function ($shift) {
                    return [
                        'id' => $shift->id,
                        'teller_name' => $shift->teller->name ?? null,
                        'treasurer_name' => $shift->treasurer->name ?? null,
                        'status' => $shift->status,
                        'opening_cash' => $shift->opening_cash ? $shift->opening_cash : 0,
                        'opened_at' => $shift->opened_at->toISOString(),
                        'closed_at' => $shift->closed_at ? $shift->closed_at->toISOString() : null,
                    ];
                }),
                'recent_transactions' => $recentTransactions->map(function ($tx) {
                    return [
                        'id' => $tx->id,
                        'type' => $tx->type,
                        'amount' => ($tx->amount ?? 0),
                        'user_name' => $tx->user->name ?? null,
                        'teller_name' => $tx->tellerShift->teller->name ?? null,
                        'created_at' => $tx->created_at->toISOString(),
                    ];
                }),
            ]
        ]);
    }
}
