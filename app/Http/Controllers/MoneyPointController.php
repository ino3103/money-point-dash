<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\TellerShift;
use App\Models\MoneyPointTransaction;
use App\Models\User;
use App\Models\FloatProvider;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MoneyPointController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Dashboard/Index
     */
    public function index()
    {
        if (Auth()->user()->cannot('View Money Point Module')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'MONEY POINT DASHBOARD',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Dashboard', 'url' => null, 'active' => true]
            ]
        ];

        // Get current user's open shift if teller
        $openShift = null;
        if (Auth()->user()->can('View Shifts')) {
            $openShift = TellerShift::where('teller_id', Auth::id())
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
            // For deposits, get the cash line amount (actual transaction amount)
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();

            if ($cashLine) {
                return abs($cashLine->amount);
            }

            // Fallback: sum positive amounts
            return abs($tx->lines()->where('amount', '>', 0)->sum('amount'));
        });

        $todayWithdrawalAmount = $todayWithdrawals->sum(function ($tx) {
            // For withdrawals, get the cash line amount (actual transaction amount)
            $cashLine = $tx->lines()->whereHas('account', function ($q) {
                $q->where('account_type', 'cash');
            })->first();

            if ($cashLine) {
                return abs($cashLine->amount);
            }

            // Fallback: sum positive amounts
            return abs($tx->lines()->where('amount', '>', 0)->sum('amount'));
        });

        // Active shifts count
        $activeShiftsCount = TellerShift::where('status', 'open')->count();

        // Pending verification shifts
        $pendingVerificationCount = TellerShift::where('status', 'submitted')->count();

        // Discrepancy shifts
        $discrepancyShiftsCount = TellerShift::where('status', 'discrepancy')->count();

        // Total cash in system (sum of all cash accounts)
        $totalCash = Account::where('account_type', 'cash')
            ->where('is_active', true)
            ->sum('balance');

        // Float balances by provider with teller details
        $floatProviders = \App\Models\FloatProvider::all()->keyBy('name');
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
                    'user_id' => $account->user_id,
                    'user_name' => $account->user->name ?? 'System',
                    'balance' => abs($account->balance), // Display as positive
                    'system_balance' => $account->balance, // Keep negative for calculations
                ];
            })
            ->groupBy('provider')
            ->map(function ($accounts, $provider) {
                $total = $accounts->sum('system_balance');
                return [
                    'provider' => $provider,
                    'display_name' => $accounts->first()['display_name'],
                    'total' => abs($total),
                    'system_total' => $total,
                    'accounts' => $accounts->values(), // Reset keys for proper iteration
                    'accounts_count' => $accounts->count(),
                ];
            });

        // Recent transactions (last 10) with preloaded lines
        $recentTransactions = MoneyPointTransaction::with(['user', 'tellerShift.teller', 'lines.account'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($tx) {
                // For withdrawals and deposits, get the cash line amount (actual transaction amount)
                if (in_array($tx->type, ['withdrawal', 'deposit'])) {
                    $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                    if ($cashLine) {
                        $tx->cached_amount = abs($cashLine->amount);
                        return $tx;
                    }
                }

                // For other transaction types, sum positive amounts
                $tx->cached_amount = abs($tx->lines->where('amount', '>', 0)->sum('amount'));
                return $tx;
            });

        // Low float alerts (if any float is below threshold)
        // Float accounts are stored as negative: -51M = 51M available (good), -5M = 5M available (low)
        // A balance >= -5M (closer to zero) means only 5M or less available = LOW FLOAT
        // Get threshold from settings (stored as positive value, convert to negative for comparison)
        $lowFloatThresholdSetting = (int) getSetting('money_point_low_float_threshold', 5000000);
        $lowFloatThreshold = -$lowFloatThresholdSetting; // Convert to negative for comparison
        $lowFloatAlerts = Account::where('account_type', 'float')
            ->where('is_active', true)
            ->where('balance', '>=', $lowFloatThreshold) // >= because -1M > -5M (less negative = less money available)
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
        $totalFloatCapital = $floatBalances->sum('total');

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

        // Average transactions per shift (for today's shifts)
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

        // Cash vs Float Ratio
        $totalCapital = $totalCash + $totalFloatCapital;
        $cashRatio = $totalCapital > 0 ? ($totalCash / $totalCapital) * 100 : 0;
        $floatRatio = $totalCapital > 0 ? ($totalFloatCapital / $totalCapital) * 100 : 0;

        // Discrepancy Rate
        $totalShifts = TellerShift::count();
        $discrepancyRate = $totalShifts > 0
            ? ($discrepancyShiftsCount / $totalShifts) * 100
            : 0;

        $stats = [
            'today_transactions_count' => $todayTransactions->count(),
            'today_deposits_count' => $todayDeposits->count(),
            'today_withdrawals_count' => $todayWithdrawals->count(),
            'today_transactions_by_type' => $todayTransactionsByType,
            'today_deposit_amount' => $todayDepositAmount,
            'today_withdrawal_amount' => $todayWithdrawalAmount,
            'active_shifts' => $activeShiftsCount,
            'pending_verification' => $pendingVerificationCount,
            'discrepancy_shifts' => $discrepancyShiftsCount,
            'total_cash' => $totalCash,
            'float_balances' => $floatBalances,
            'recent_transactions' => $recentTransactions,
            'low_float_alerts' => $lowFloatAlerts,
            // New metrics
            'total_float_capital' => $totalFloatCapital,
            'this_week_deposit_amount' => $thisWeekDepositAmount,
            'last_week_deposit_amount' => $lastWeekDepositAmount,
            'this_week_withdrawal_amount' => $thisWeekWithdrawalAmount,
            'last_week_withdrawal_amount' => $lastWeekWithdrawalAmount,
            'deposit_change_percent' => $depositChange,
            'withdrawal_change_percent' => $withdrawalChange,
            'avg_deposit_size' => $avgDepositSize,
            'avg_withdrawal_size' => $avgWithdrawalSize,
            'today_shifts_count' => $todayShiftsCount,
            'today_verified_shifts' => $todayVerifiedShifts,
            'shift_completion_rate' => $shiftCompletionRate,
            'avg_transactions_per_shift' => $avgTransactionsPerShift,
            'total_mtaji_in_system' => $totalMtajiInSystem,
            'provider_volumes' => $providerVolumes,
            'cash_ratio' => $cashRatio,
            'float_ratio' => $floatRatio,
            'discrepancy_rate' => $discrepancyRate,
        ];

        return view('money-point.index', compact('data', 'openShift', 'recentShifts', 'stats'));
    }

    /**
     * List all shifts
     */
    public function shifts()
    {
        if (Auth()->user()->cannot('View Shifts')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'ALL SHIFTS',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Shifts', 'url' => null, 'active' => true]
            ]
        ];

        // Get shifts data
        $shifts = TellerShift::with(['teller', 'treasurer'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get users with Teller role
        $tellers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teller');
        })->get();

        // Check if any tellers exist
        if ($tellers->isEmpty()) {
            return view('money-point.shifts.index', compact('data', 'tellers', 'shifts'))
                ->with('warning', 'No tellers found. Please create users with the "Teller" role first.');
        }

        return view('money-point.shifts.index', compact('data', 'tellers', 'shifts'));
    }

    /**
     * Show shift details
     */
    public function showShift($id)
    {
        if (Auth()->user()->cannot('View Shifts')) {
            abort(403, 'Access Denied');
        }

        $shift = TellerShift::with(['teller', 'treasurer', 'transactions.lines.account'])
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
        // This gives us the true starting balance (previous + new allocation if previous was used)
        $actualStartingCash = $shift->opening_cash; // Default to stored value
        $actualStartingFloats = $shift->opening_floats ?? []; // Default to stored values

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
                    // Convert system balance (negative) to display balance (positive)
                    $actualStartingFloats[$provider] = abs($floatLine->balance_after);
                }
            }
        }

        // Calculate Mtaji (Opening Capital) = Actual Starting Cash + Sum of Actual Starting Floats
        $mtaji = $actualStartingCash;
        foreach ($actualStartingFloats as $amount) {
            $mtaji += abs($amount);
        }

        // Pass actual starting balances to view
        $actualStartingCash = $actualStartingCash;
        $actualStartingFloats = $actualStartingFloats;

        // Calculate Balanced (Closing Capital) = Closing Cash + Sum of Closing Floats (if submitted)
        $balanced = null;
        if ($shift->closing_cash !== null) {
            $balanced = $shift->closing_cash;
            if ($shift->closing_floats) {
                foreach ($shift->closing_floats as $amount) {
                    $balanced += abs($amount); // Closing floats are in display format (positive)
                }
            }
        }

        $data = [
            'title' => 'SHIFT DETAILS',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Shifts', 'url' => route('money-point.shifts'), 'icon' => 'uil uil-estate'],
                ['name' => 'Shift Details', 'url' => null, 'active' => true]
            ]
        ];

        return view('money-point.shifts.show', compact('data', 'shift', 'providerNames', 'mtaji', 'balanced', 'actualStartingCash', 'actualStartingFloats'));
    }

    /**
     * Show form to open shift
     */
    public function createShift()
    {
        if (Auth()->user()->cannot('Open Shifts')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'OPEN SHIFT',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Shifts', 'url' => route('money-point.shifts'), 'icon' => 'uil uil-estate'],
                ['name' => 'Open Shift', 'url' => null, 'active' => true]
            ]
        ];

        // Get users with Teller role
        $tellers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teller');
        })->get();

        // Check if any tellers exist
        if ($tellers->isEmpty()) {
            return redirect()->route('money-point.shifts')
                ->with('error', 'No tellers found. Please create users with the "Teller" role before opening a shift.');
        }

        $floatProviders = FloatProvider::getActive();

        // Check if any float providers exist
        if ($floatProviders->isEmpty()) {
            return redirect()->route('money-point.shifts')
                ->with('error', 'No active float providers found. Please configure float providers first.');
        }

        // Get previous closing balances for each teller (if they have a verified/closed shift)
        $previousClosingBalances = [];
        foreach ($tellers as $teller) {
            $previousShift = TellerShift::where('teller_id', $teller->id)
                ->whereIn('status', ['verified', 'closed'])
                ->whereNotNull('closing_cash')
                ->orderByRaw('COALESCE(closed_at, updated_at) DESC')
                ->orderBy('id', 'desc')
                ->first();

            if ($previousShift) {
                $previousClosingBalances[$teller->id] = [
                    'cash' => $previousShift->closing_cash ?? 0,
                    'floats' => $previousShift->closing_floats ?? [],
                ];
            }
        }

        return view('money-point.shifts.create', compact('data', 'tellers', 'floatProviders', 'previousClosingBalances'));
    }

    /**
     * Store new shift
     */
    public function storeShift(Request $request)
    {
        if (Auth()->user()->cannot('Open Shifts')) {
            abort(403, 'Access Denied');
        }

        // Clean and convert formatted amounts to integers
        $openingCash = $this->cleanAmount($request->opening_cash);
        $openingFloats = [];
        foreach ($request->opening_floats ?? [] as $provider => $amount) {
            $cleanedAmount = $this->cleanAmount($amount);
            if ($cleanedAmount > 0) {
                $openingFloats[$provider] = $cleanedAmount;
            }
        }

        $request->validate([
            'teller_id' => 'required|exists:users,id',
            'opening_cash' => 'required',
        ], [
            'teller_id.required' => 'Please select a teller.',
            'teller_id.exists' => 'Selected teller does not exist.',
            'opening_cash.required' => 'Opening cash amount is required.',
        ]);

        // Validate amounts are positive integers
        if ($openingCash <= 0) {
            return back()->withInput()->with('error', 'Opening cash must be greater than 0.');
        }

        try {
            $teller = User::findOrFail($request->teller_id);

            // Verify the user has the Teller role
            if (!$teller->hasRole('Teller')) {
                return back()->withInput()->with('error', 'Selected user does not have the "Teller" role.');
            }

            // Get flags for using previous closing balances
            // With hidden input, we get '0' when unchecked, '1' when checked
            $usePreviousCash = $request->input('use_previous_cash') === '1' || $request->input('use_previous_cash') === 1 || $request->input('use_previous_cash') === true;
            $usePreviousFloats = [];
            if ($request->has('use_previous_float') && is_array($request->use_previous_float)) {
                foreach ($request->use_previous_float as $provider => $value) {
                    if ($value == '1' || $value === 1 || $value === true) {
                        $usePreviousFloats[$provider] = true;
                    }
                }
            }

            // Log for debugging
            \Log::info('Opening shift', [
                'teller_id' => $teller->id,
                'request_use_previous_cash' => $request->input('use_previous_cash'),
                'use_previous_cash' => $usePreviousCash,
                'request_use_previous_float' => $request->input('use_previous_float'),
                'use_previous_floats' => $usePreviousFloats,
                'opening_cash' => $openingCash,
                'opening_floats' => $openingFloats,
            ]);

            $shift = $this->accountingService->openShift(
                Auth::user(),
                $teller,
                $openingCash,
                $openingFloats,
                $usePreviousCash,
                $usePreviousFloats
            );

            return redirect()->route('money-point.shifts.show', $shift->id)
                ->with('success', 'Shift opened successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show submit shift form
     */
    public function submitShiftForm($id)
    {
        if (Auth()->user()->cannot('Submit Shifts')) {
            abort(403, 'Access Denied');
        }

        $shift = TellerShift::with(['teller'])->findOrFail($id);

        if (!$shift->canSubmit() || $shift->teller_id != Auth::id()) {
            abort(403, 'You cannot submit this shift');
        }

        // Get provider display names
        $providerNames = [];
        if ($shift->opening_floats) {
            foreach ($shift->opening_floats as $provider => $amount) {
                $providerModel = FloatProvider::where('name', $provider)->first();
                $providerNames[$provider] = $providerModel ? $providerModel->display_name : ucfirst($provider);
            }
        }

        $data = [
            'title' => 'SUBMIT SHIFT',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Shifts', 'url' => route('money-point.shifts'), 'icon' => 'uil uil-estate'],
                ['name' => 'Submit Shift', 'url' => null, 'active' => true]
            ]
        ];

        return view('money-point.shifts.submit', compact('data', 'shift', 'providerNames'));
    }

    /**
     * Submit shift
     */
    public function submitShift(Request $request, $id)
    {
        if (Auth()->user()->cannot('Submit Shifts')) {
            abort(403, 'Access Denied');
        }

        $shift = TellerShift::findOrFail($id);

        if (!$shift->canSubmit() || $shift->teller_id != Auth::id()) {
            abort(403, 'You cannot submit this shift');
        }

        $request->validate([
            'closing_cash' => 'required',
            'closing_floats' => 'required|array',
            'closing_floats.*' => 'required',
            'notes' => 'nullable|string',
        ]);

        // Clean formatted amounts
        $closingCash = $this->cleanAmount($request->closing_cash);
        $closingFloats = [];
        foreach ($request->closing_floats as $provider => $amount) {
            $closingFloats[$provider] = $this->cleanAmount($amount);
        }

        // Validate cleaned amounts
        if ($closingCash < 0) {
            return back()->withInput()->with('error', 'Closing cash must be greater than or equal to 0.');
        }
        foreach ($closingFloats as $provider => $amount) {
            if ($amount < 0) {
                return back()->withInput()->with('error', "Closing float for {$provider} must be greater than or equal to 0.");
            }
        }

        try {
            $shift = $this->accountingService->submitShift(
                $shift,
                $closingCash,
                $closingFloats,
                $request->notes
            );

            return redirect()->route('money-point.shifts.show', $shift->id)
                ->with('success', 'Shift submitted successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show verify shift form
     */
    public function verifyShiftForm($id)
    {
        if (Auth()->user()->cannot('Verify Shifts')) {
            abort(403, 'Access Denied');
        }

        $shift = TellerShift::with(['teller', 'treasurer'])->findOrFail($id);

        if (!$shift->canVerify()) {
            abort(403, 'Shift cannot be verified');
        }

        $data = [
            'title' => 'VERIFY SHIFT',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Shifts', 'url' => route('money-point.shifts'), 'icon' => 'uil uil-estate'],
                ['name' => 'Verify Shift', 'url' => null, 'active' => true]
            ]
        ];

        return view('money-point.shifts.verify', compact('data', 'shift'));
    }

    /**
     * Verify shift
     */
    public function verifyShift(Request $request, $id)
    {
        if (Auth()->user()->cannot('Verify Shifts')) {
            abort(403, 'Access Denied');
        }

        $shift = TellerShift::findOrFail($id);

        if (!$shift->canVerify()) {
            abort(403, 'Shift cannot be verified');
        }

        $request->validate([
            'action' => 'required|in:approve,request_adjustment',
            'adjustments' => 'required_if:action,request_adjustment|array',
            'notes' => 'nullable|string',
        ]);

        // Clean adjustment amounts if present
        $adjustments = [];
        if ($request->action === 'request_adjustment' && $request->has('adjustments')) {
            foreach ($request->adjustments as $adjustment) {
                $adjustments[] = [
                    'account_id' => $adjustment['account_id'],
                    'amount' => $this->cleanAmount($adjustment['amount']),
                    'reason' => $adjustment['reason'] ?? 'Reconciliation adjustment',
                ];
            }
        }

        try {
            $shift = $this->accountingService->verifyShift(
                $shift,
                $request->action,
                $adjustments,
                $request->notes
            );

            return redirect()->route('money-point.shifts.show', $shift->id)
                ->with('success', 'Shift verified successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * List accounts
     */
    public function accounts()
    {
        if (Auth()->user()->cannot('View Accounts')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'ALL ACCOUNTS',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Accounts', 'url' => null, 'active' => true]
            ]
        ];

        // Get accounts data
        $accounts = Account::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $users = User::has('accounts')->get();

        return view('money-point.accounts.index', compact('data', 'users', 'accounts'));
    }

    /**
     * Show account ledger
     */
    public function accountLedger($id)
    {
        if (Auth()->user()->cannot('View Ledger')) {
            abort(403, 'Access Denied');
        }

        $account = Account::with('user')->findOrFail($id);

        // Get transaction lines for the account
        $lines = \App\Models\TransactionLine::where('account_id', $account->id)
            ->with(['transaction.user', 'transaction.tellerShift'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if this is an AJAX request for modal
        if (request()->ajax() && !request()->has('draw')) {
            // Regular AJAX request for modal - return JSON with HTML and data
            $html = view('money-point.accounts.ledger-content', compact('account', 'lines'))->render();
            return response()->json(['html' => $html]);
        }

        // Fallback for non-AJAX requests (backward compatibility)
        $data = [
            'title' => 'ACCOUNT LEDGER',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Accounts', 'url' => route('money-point.accounts'), 'icon' => 'uil uil-estate'],
                ['name' => 'Ledger', 'url' => null, 'active' => true]
            ]
        ];

        return view('money-point.accounts.ledger', compact('data', 'account', 'lines'));
    }

    /**
     * List transactions
     */
    public function transactions()
    {
        if (Auth()->user()->cannot('View Money Point Transactions')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'ALL TRANSACTIONS',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Transactions', 'url' => null, 'active' => true]
            ]
        ];

        // Get transactions data
        $transactions = MoneyPointTransaction::with(['user', 'tellerShift.teller', 'lines'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get active shift and float accounts for modals
        $shift = null;
        $floatAccounts = collect();

        if (Auth::check()) {
            $shift = TellerShift::where('teller_id', Auth::id())
                ->where('status', 'open')
                ->first();

            if ($shift) {
                $floatAccounts = Account::where('user_id', Auth::id())
                    ->where('account_type', 'float')
                    ->where('is_active', true)
                    ->get();
            }
        }

        // Get shifts and users for filters
        $shifts = TellerShift::with(['teller', 'treasurer'])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        $users = User::whereHas('moneyPointTransactions')
            ->orWhereHas('tellerShifts')
            ->distinct()
            ->orderBy('name')
            ->get();

        return view('money-point.transactions.index', compact('data', 'shift', 'floatAccounts', 'shifts', 'users', 'transactions'));
    }

    /**
     * Show transaction details (AJAX)
     */
    public function showTransaction($id)
    {
        if (Auth()->user()->cannot('View Money Point Transactions')) {
            abort(403, 'Access Denied');
        }

        $transaction = MoneyPointTransaction::with(['user', 'tellerShift.teller', 'tellerShift.treasurer', 'lines.account.user'])
            ->findOrFail($id);

        $dateFormat = getSetting('date_format', 'Y-m-d');
        $timeFormat = getSetting('time_format', 'H:i:s');
        $dateTimeFormat = "$dateFormat $timeFormat";

        // Get type badge
        $typeText = ucwords($transaction->type);
        $bgClass = match ($transaction->type) {
            'deposit' => 'success',
            'withdrawal' => 'danger',
            'allocation' => 'primary',
            'transfer' => 'info',
            'reconciliation' => 'warning',
            'adjustment' => 'secondary',
            'fee' => 'dark',
            default => 'secondary'
        };

        $html = view('money-point.transactions.details-content', compact('transaction', 'dateTimeFormat', 'typeText', 'bgClass'))->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Print transaction receipt
     */
    public function printReceipt($id)
    {
        if (Auth()->user()->cannot('View Money Point Transactions')) {
            abort(403, 'Access Denied');
        }

        $transaction = MoneyPointTransaction::with(['user', 'tellerShift.teller', 'lines.account'])
            ->findOrFail($id);

        // Calculate amount
        $cashLine = $transaction->lines->firstWhere('account.account_type', 'cash');
        $amount = $cashLine ? abs($cashLine->amount) : abs($transaction->lines->where('amount', '>', 0)->sum('amount'));

        return view('money-point.transactions.print-receipt', compact('transaction', 'amount'));
    }

    /**
     * Show withdrawal form
     */
    public function createWithdrawal()
    {
        if (Auth()->user()->cannot('Create Withdrawals')) {
            abort(403, 'Access Denied');
        }

        $shift = TellerShift::where('teller_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return redirect()->route('money-point.index')
                ->with('error', 'You must have an open shift to perform transactions.');
        }

        $data = [
            'title' => 'CREATE WITHDRAWAL',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Transactions', 'url' => route('money-point.transactions'), 'icon' => 'uil uil-estate'],
                ['name' => 'Withdrawal', 'url' => null, 'active' => true]
            ]
        ];

        // Get available float accounts
        $floatAccounts = Account::where('user_id', Auth::id())
            ->where('account_type', 'float')
            ->where('is_active', true)
            ->get();

        return view('money-point.transactions.withdraw', compact('data', 'shift', 'floatAccounts'));
    }

    /**
     * Store withdrawal
     */
    public function storeWithdrawal(Request $request)
    {
        if (Auth()->user()->cannot('Create Withdrawals')) {
            abort(403, 'Access Denied');
        }

        // Clean and convert formatted amount to integer
        $amount = $this->cleanAmount($request->amount);

        $request->validate([
            'amount' => 'required',
            'provider' => 'required|string',
            'reference' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'account_no' => 'nullable|string|max:255',
        ]);

        // Validate amount is positive
        if ($amount <= 0) {
            return back()->withInput()->with('error', 'Amount must be greater than 0.');
        }

        // Determine if provider is bank or mobile money
        $providerModel = \App\Models\FloatProvider::where('name', $request->provider)->first();
        $isBankProvider = $providerModel && $providerModel->type === 'bank';

        // Validate based on provider type
        if ($isBankProvider) {
            if (!$request->account_no) {
                return back()->withInput()->with('error', 'Account number is required for bank transactions.');
            }
        } else {
            if (!$request->customer_phone) {
                return back()->withInput()->with('error', 'Customer phone number is required for mobile money transactions.');
            }
        }

        $shift = TellerShift::where('teller_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return back()->with('error', 'You must have an open shift to perform transactions.');
        }

        try {
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
                Auth::user(),
                $shift,
                $request->provider,
                $amount,
                $metadata
            );

            return redirect()->route('money-point.transactions')
                ->with('success', 'Withdrawal processed successfully.')
                ->with('print_transaction_id', $transaction->id);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show deposit form
     */
    public function createDeposit()
    {
        if (Auth()->user()->cannot('Create Deposits')) {
            abort(403, 'Access Denied');
        }

        $shift = TellerShift::where('teller_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return redirect()->route('money-point.index')
                ->with('error', 'You must have an open shift to perform transactions.');
        }

        $data = [
            'title' => 'CREATE DEPOSIT',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Transactions', 'url' => route('money-point.transactions'), 'icon' => 'uil uil-estate'],
                ['name' => 'Deposit', 'url' => null, 'active' => true]
            ]
        ];

        // Get available float accounts
        $floatAccounts = Account::where('user_id', Auth::id())
            ->where('account_type', 'float')
            ->where('is_active', true)
            ->get();

        return view('money-point.transactions.deposit', compact('data', 'shift', 'floatAccounts'));
    }

    /**
     * Store deposit
     */
    public function storeDeposit(Request $request)
    {
        if (Auth()->user()->cannot('Create Deposits')) {
            abort(403, 'Access Denied');
        }

        // Clean and convert formatted amount to integer
        $amount = $this->cleanAmount($request->amount);

        $request->validate([
            'amount' => 'required',
            'provider' => 'required|string',
            'reference' => 'nullable|string',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'account_no' => 'nullable|string|max:255',
        ]);

        // Validate amount is positive
        if ($amount <= 0) {
            return back()->withInput()->with('error', 'Amount must be greater than 0.');
        }

        // Determine if provider is bank or mobile money
        $providerModel = \App\Models\FloatProvider::where('name', $request->provider)->first();
        $isBankProvider = $providerModel && $providerModel->type === 'bank';

        // Validate based on provider type
        if ($isBankProvider) {
            if (!$request->account_no) {
                return back()->withInput()->with('error', 'Account number is required for bank transactions.');
            }
        } else {
            if (!$request->customer_phone) {
                return back()->withInput()->with('error', 'Customer phone number is required for mobile money transactions.');
            }
        }

        $shift = TellerShift::where('teller_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$shift) {
            return back()->with('error', 'You must have an open shift to perform transactions.');
        }

        try {
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
                Auth::user(),
                $shift,
                $request->provider,
                $amount,
                $metadata
            );

            return redirect()->route('money-point.transactions')
                ->with('success', 'Deposit processed successfully.')
                ->with('print_transaction_id', $transaction->id);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * List float providers
     */
    public function floatProviders()
    {
        if (Auth()->user()->cannot('View Accounts')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'FLOAT PROVIDERS',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Float Providers', 'url' => null, 'active' => true]
            ]
        ];

        // Get float providers data
        $providers = FloatProvider::orderBy('sort_order')->orderBy('display_name')->get();

        return view('money-point.float-providers.index', compact('data', 'providers'));
    }

    /**
     * Store float provider
     */
    public function storeFloatProvider(Request $request)
    {
        if (Auth()->user()->cannot('Create Accounts')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:float_providers,name',
            'display_name' => 'required|string|max:100',
            'type' => 'required|in:bank,mobile_money',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        FloatProvider::create([
            'name' => strtolower($request->name),
            'display_name' => $request->display_name,
            'type' => $request->type,
            'description' => $request->description,
            'is_active' => true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return back()->with('success', 'Float provider created successfully.');
    }

    /**
     * Update float provider
     */
    public function updateFloatProvider(Request $request)
    {
        if (Auth()->user()->cannot('Create Accounts')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'id' => 'required|exists:float_providers,id',
            'name' => 'required|string|max:50|unique:float_providers,name,' . $request->id,
            'display_name' => 'required|string|max:100',
            'type' => 'required|in:bank,mobile_money',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $provider = FloatProvider::findOrFail($request->id);
        $provider->update([
            'name' => strtolower($request->name),
            'display_name' => $request->display_name,
            'type' => $request->type,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $provider->is_active,
            'sort_order' => $request->sort_order ?? $provider->sort_order,
        ]);

        return back()->with('success', 'Float provider updated successfully.');
    }

    /**
     * Clean formatted amount string to integer
     * Removes currency symbols, commas, and whitespace
     */
    private function cleanAmount($amount)
    {
        if (empty($amount)) {
            return 0;
        }
        // Remove currency symbols, commas, spaces, and convert to integer
        $cleaned = preg_replace('/[^\d]/', '', $amount);
        return (int) $cleaned;
    }

    /**
     * Toggle float provider status
     */
    public function toggleFloatProvider(Request $request, $id)
    {
        if (Auth()->user()->cannot('Create Accounts')) {
            abort(403, 'Access Denied');
        }

        $provider = FloatProvider::findOrFail($id);
        $provider->is_active = !$provider->is_active;
        $provider->save();

        return response()->json([
            'success' => true,
            'message' => 'Float provider ' . ($provider->is_active ? 'enabled' : 'disabled') . ' successfully.',
            'is_active' => $provider->is_active
        ]);
    }

    /**
     * Show Money Point Reports index page
     */
    public function reports()
    {
        if (Auth()->user()->cannot('View Money Point Reports')) {
            abort(403, 'Access Denied');
        }

        $data = [
            'title' => 'MONEY POINT REPORTS',
            'breadcrumbs' => [
                ['name' => 'Money Point', 'url' => route('money-point.index'), 'icon' => 'uil uil-estate'],
                ['name' => 'Reports', 'url' => null, 'active' => true]
            ]
        ];

        // Get tellers for filters
        $tellers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teller');
        })->get();

        return view('money-point.reports.index', compact('data', 'tellers'));
    }

    /**
     * Generate Shift Summary Report
     */
    public function shiftSummaryReport(Request $request)
    {
        if (Auth()->user()->cannot('View Money Point Reports')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'teller_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:open,submitted,verified,closed,discrepancy',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tellerId = $request->input('teller_id');
        $status = $request->input('status');

        $query = TellerShift::with(['teller', 'treasurer'])
            ->whereBetween('opened_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($tellerId) {
            $query->where('teller_id', $tellerId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $shifts = $query->orderBy('opened_at', 'desc')->get();

        $settings = [
            'site_name' => getSetting('site_name', 'SACCOS'),
            'contact_phone' => getSetting('contact_phone', ''),
            'admin_email' => getSetting('admin_email', ''),
            'address' => getSetting('address', ''),
        ];

        $data = [
            'shifts' => $shifts,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'teller' => $tellerId ? User::find($tellerId) : null,
            'status' => $status,
            'settings' => $settings,
            'report_title' => 'Shift Summary Report'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('money-point.reports.shift-summary-pdf', $data);
        return $pdf->download('shift_summary_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    /**
     * Generate Transaction Report
     */
    public function transactionReport(Request $request)
    {
        if (Auth()->user()->cannot('View Money Point Reports')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|in:withdrawal,deposit,allocation,transfer,reconciliation,adjustment,fee',
            'teller_id' => 'nullable|exists:users,id',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $type = $request->input('type');
        $tellerId = $request->input('teller_id');

        $query = MoneyPointTransaction::with(['user', 'tellerShift.teller'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($type) {
            $query->where('type', $type);
        }

        if ($tellerId) {
            $query->where('user_id', $tellerId);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $settings = [
            'site_name' => getSetting('site_name', 'SACCOS'),
            'contact_phone' => getSetting('contact_phone', ''),
            'admin_email' => getSetting('admin_email', ''),
            'address' => getSetting('address', ''),
        ];

        $data = [
            'transactions' => $transactions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
            'teller' => $tellerId ? User::find($tellerId) : null,
            'settings' => $settings,
            'report_title' => 'Transaction Report'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('money-point.reports.transaction-pdf', $data);
        return $pdf->download('transaction_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    /**
     * Generate Float Balance Report
     */
    public function floatBalanceReport(Request $request)
    {
        if (Auth()->user()->cannot('View Money Point Reports')) {
            abort(403, 'Access Denied');
        }

        $providerId = $request->input('provider_id');
        $tellerId = $request->input('teller_id');

        $query = Account::with(['user', 'lines'])
            ->where('account_type', 'float')
            ->where('is_active', true);

        if ($providerId) {
            $provider = FloatProvider::find($providerId);
            if ($provider) {
                $query->where('provider', $provider->name);
            }
        }

        if ($tellerId) {
            $query->where('user_id', $tellerId);
        }

        $accounts = $query->orderBy('provider')->orderBy('user_id')->get();

        // Group by provider
        $groupedAccounts = $accounts->groupBy('provider');

        $allProviders = FloatProvider::getActive();
        $floatProviders = $allProviders->keyBy('name');

        $settings = [
            'site_name' => getSetting('site_name', 'SACCOS'),
            'contact_phone' => getSetting('contact_phone', ''),
            'admin_email' => getSetting('admin_email', ''),
            'address' => getSetting('address', ''),
        ];

        $data = [
            'groupedAccounts' => $groupedAccounts,
            'floatProviders' => $floatProviders,
            'provider' => $providerId ? FloatProvider::find($providerId) : null,
            'teller' => $tellerId ? User::find($tellerId) : null,
            'settings' => $settings,
            'report_title' => 'Float Balance Report'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('money-point.reports.float-balance-pdf', $data);
        return $pdf->download('float_balance_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Generate Variance/Discrepancy Report
     */
    public function varianceReport(Request $request)
    {
        if (Auth()->user()->cannot('View Money Point Reports')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'teller_id' => 'nullable|exists:users,id',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $tellerId = $request->input('teller_id');

        $query = TellerShift::with(['teller', 'treasurer'])
            ->where('status', 'discrepancy');

        if ($startDate && $endDate) {
            $query->whereBetween('closed_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        if ($tellerId) {
            $query->where('teller_id', $tellerId);
        }

        $shifts = $query->orderBy('closed_at', 'desc')->get();

        $settings = [
            'site_name' => getSetting('site_name', 'SACCOS'),
            'contact_phone' => getSetting('contact_phone', ''),
            'admin_email' => getSetting('admin_email', ''),
            'address' => getSetting('address', ''),
        ];

        $data = [
            'shifts' => $shifts,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'teller' => $tellerId ? User::find($tellerId) : null,
            'settings' => $settings,
            'report_title' => 'Variance/Discrepancy Report'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('money-point.reports.variance-pdf', $data);
        return $pdf->download('variance_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Generate Daily Summary Report
     */
    public function dailySummaryReport(Request $request)
    {
        if (Auth()->user()->cannot('View Money Point Reports')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->input('date');
        $startOfDay = $date . ' 00:00:00';
        $endOfDay = $date . ' 23:59:59';

        // Get all transactions for the day
        $transactions = MoneyPointTransaction::with(['user', 'lines.account'])
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->whereIn('type', ['deposit', 'withdrawal'])
            ->get();

        // Calculate totals
        $totalDeposits = 0;
        $totalWithdrawals = 0;
        $depositCount = 0;
        $withdrawalCount = 0;

        foreach ($transactions as $tx) {
            $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
            if ($cashLine) {
                $amount = abs($cashLine->amount);
                if ($tx->type === 'deposit') {
                    $totalDeposits += $amount;
                    $depositCount++;
                } elseif ($tx->type === 'withdrawal') {
                    $totalWithdrawals += $amount;
                    $withdrawalCount++;
                }
            }
        }

        // Get shifts opened/closed on this day
        $shiftsOpened = TellerShift::whereDate('opened_at', $date)->count();
        $shiftsClosed = TellerShift::whereDate('closed_at', $date)->count();
        $shiftsVerified = TellerShift::whereDate('closed_at', $date)->where('status', 'verified')->count();

        $settings = [
            'site_name' => getSetting('site_name', 'SACCOS'),
            'contact_phone' => getSetting('contact_phone', ''),
            'admin_email' => getSetting('admin_email', ''),
            'address' => getSetting('address', ''),
        ];

        $data = [
            'date' => $date,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'depositCount' => $depositCount,
            'withdrawalCount' => $withdrawalCount,
            'shiftsOpened' => $shiftsOpened,
            'shiftsClosed' => $shiftsClosed,
            'shiftsVerified' => $shiftsVerified,
            'netFlow' => $totalDeposits - $totalWithdrawals,
            'settings' => $settings,
            'report_title' => 'Daily Summary Report'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('money-point.reports.daily-summary-pdf', $data);
        return $pdf->download('daily_summary_report_' . $date . '.pdf');
    }

    /**
     * Generate Teller Performance Report
     */
    public function tellerPerformanceReport(Request $request)
    {
        if (Auth()->user()->cannot('View Money Point Reports')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $tellers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Teller');
        })->get();

        $performance = [];

        foreach ($tellers as $teller) {
            $shifts = TellerShift::where('teller_id', $teller->id)
                ->whereBetween('opened_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();

            $transactions = MoneyPointTransaction::with('lines.account')
                ->where('user_id', $teller->id)
                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->whereIn('type', ['deposit', 'withdrawal'])
                ->get();

            $totalDeposits = 0;
            $totalWithdrawals = 0;

            foreach ($transactions as $tx) {
                $cashLine = $tx->lines->firstWhere('account.account_type', 'cash');
                if ($cashLine) {
                    $amount = abs($cashLine->amount);
                    if ($tx->type === 'deposit') {
                        $totalDeposits += $amount;
                    } elseif ($tx->type === 'withdrawal') {
                        $totalWithdrawals += $amount;
                    }
                }
            }

            $performance[] = [
                'teller' => $teller,
                'shifts_count' => $shifts->count(),
                'shifts_verified' => $shifts->where('status', 'verified')->count(),
                'shifts_discrepancy' => $shifts->where('status', 'discrepancy')->count(),
                'transactions_count' => $transactions->count(),
                'total_deposits' => $totalDeposits,
                'total_withdrawals' => $totalWithdrawals,
                'net_flow' => $totalDeposits - $totalWithdrawals,
            ];
        }

        $settings = [
            'site_name' => getSetting('site_name', 'SACCOS'),
            'contact_phone' => getSetting('contact_phone', ''),
            'admin_email' => getSetting('admin_email', ''),
            'address' => getSetting('address', ''),
        ];

        $data = [
            'performance' => $performance,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'settings' => $settings,
            'report_title' => 'Teller Performance Report'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('money-point.reports.teller-performance-pdf', $data);
        return $pdf->download('teller_performance_report_' . $startDate . '_to_' . $endDate . '.pdf');
    }
}
