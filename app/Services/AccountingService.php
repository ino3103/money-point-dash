<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Allocation;
use App\Models\MoneyPointTransaction;
use App\Models\TellerShift;
use App\Models\TransactionLine;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    /**
     * Open shift and allocate funds
     */
    public function openShift(User $treasurer, User $teller, int $openingCash, array $openingFloats, bool $usePreviousCash = false, array $usePreviousFloats = []): TellerShift
    {
        return DB::transaction(function () use ($treasurer, $teller, $openingCash, $openingFloats, $usePreviousCash, $usePreviousFloats) {
            // Check if teller has an open shift
            $existingShift = TellerShift::where('teller_id', $teller->id)
                ->where('status', 'open')
                ->first();

            if ($existingShift) {
                throw new \Exception('Teller already has an open shift');
            }

            $shift = TellerShift::create([
                'teller_id' => $teller->id,
                'treasurer_id' => $treasurer->id,
                'opened_at' => now(),
                'status' => 'open',
                'opening_cash' => $openingCash,
                'opening_floats' => $openingFloats,
            ]);

            // Apply cash allocation
            $cashAccount = Account::firstOrCreate(
                [
                    'user_id' => $teller->id,
                    'account_type' => 'cash',
                    'provider' => 'cash',
                ],
                [
                    'balance' => 0,
                    'currency' => 'TZS',
                    'is_active' => true,
                ]
            );

            $cashAccount->lockForUpdate();

            // Check if treasurer wants to use previous closing cash
            if ($usePreviousCash) {
                $previousShift = TellerShift::where('teller_id', $teller->id)
                    ->where('id', '!=', $shift->id)
                    ->whereIn('status', ['verified', 'closed'])
                    ->whereNotNull('closing_cash')
                    ->orderByRaw('COALESCE(closed_at, updated_at) DESC')
                    ->orderBy('id', 'desc') // Add this as fallback
                    ->first();

                if ($previousShift && $previousShift->closing_cash !== null) {
                    // Reset to previous closing cash
                    $cashAccount->balance = $previousShift->closing_cash;
                    Log::info("Using previous closing cash for shift {$shift->id}: {$previousShift->closing_cash} from shift {$previousShift->id}");
                } else {
                    // No previous shift, reset to zero
                    $cashAccount->balance = 0;
                    Log::info("No previous shift found for cash, resetting to 0 for shift {$shift->id}");
                }
            } else {
                // Treasurer wants to set new amount, reset to zero first
                $cashAccount->balance = 0;
                Log::info("Not using previous cash, resetting to 0 for shift {$shift->id}");
            }

            // Now add the new allocation
            $cashAccount->balance += $openingCash;
            $cashAccount->save();

            // Create transaction and lines for cash allocation
            $tx = MoneyPointTransaction::create([
                'type' => 'allocation',
                'teller_shift_id' => $shift->id,
                'user_id' => $treasurer->id,
                'metadata' => ['allocation_type' => 'cash'],
            ]);

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $cashAccount->id,
                'amount' => $openingCash,
                'balance_after' => $cashAccount->balance,
                'description' => 'Opening cash allocation',
            ]);

            // Create operations settlement account if it doesn't exist
            $opsAccount = Account::firstOrCreate(
                [
                    'user_id' => null,
                    'account_type' => 'bank',
                    'provider' => 'operations_settlement',
                ],
                [
                    'balance' => 0,
                    'currency' => 'TZS',
                    'is_active' => true,
                ]
            );

            $opsAccount->lockForUpdate();
            $opsAccount->balance -= $openingCash;
            $opsAccount->save();

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $opsAccount->id,
                'amount' => -$openingCash,
                'balance_after' => $opsAccount->balance,
                'description' => 'Settlement balancing - cash allocation',
            ]);

            // Apply float allocations
            foreach ($openingFloats as $provider => $amount) {
                // Amount should be negative for floats (system stores negative)
                $floatAmount = -abs($amount);

                $floatAccount = Account::firstOrCreate(
                    [
                        'user_id' => $teller->id,
                        'account_type' => 'float',
                        'provider' => $provider,
                    ],
                    [
                        'balance' => 0,
                        'currency' => 'TZS',
                        'is_active' => true,
                    ]
                );

                $floatAccount->lockForUpdate();

                // Store old balance for adjustment tracking
                $oldBalance = $floatAccount->balance;
                $previousClosingBalance = null;

                // Check if treasurer wants to use previous closing balance for this provider
                $usePrevious = isset($usePreviousFloats[$provider]) && $usePreviousFloats[$provider];

                if ($usePrevious) {
                    // Check if there's a previous closed/verified shift for this teller
                    $previousShift = TellerShift::where('teller_id', $teller->id)
                        ->where('id', '!=', $shift->id)
                        ->whereIn('status', ['verified', 'closed'])
                        ->whereNotNull('closing_floats')
                        ->orderByRaw('COALESCE(closed_at, updated_at) DESC')
                        ->orderBy('id', 'desc') // Add this as fallback
                        ->first();

                    if ($previousShift && isset($previousShift->closing_floats[$provider]) && $previousShift->closing_floats[$provider] !== null) {
                        // Reset account to previous shift's closing balance (convert display to system)
                        // closing_floats stores display values (positive), convert to system (negative)
                        $previousClosingBalance = -abs($previousShift->closing_floats[$provider]);
                        $floatAccount->balance = $previousClosingBalance;
                        Log::info("Using previous closing float for shift {$shift->id}, provider {$provider}: {$previousShift->closing_floats[$provider]} (system: {$previousClosingBalance}) from shift {$previousShift->id}");
                    } else {
                        // No previous shift or no closing balance recorded, reset to zero
                        $floatAccount->balance = 0;
                        Log::info("No previous shift found for float {$provider}, resetting to 0 for shift {$shift->id}");
                    }
                } else {
                    // Treasurer wants to set new amount, reset to zero first
                    $floatAccount->balance = 0;
                    Log::info("Not using previous float {$provider}, resetting to 0 for shift {$shift->id}");
                }

                // Now add the new allocation
                $floatAccount->balance += $floatAmount; // Adding negative makes it more negative
                $floatAccount->save();

                // Create transaction for float allocation
                $floatTx = MoneyPointTransaction::create([
                    'type' => 'allocation',
                    'teller_shift_id' => $shift->id,
                    'user_id' => $treasurer->id,
                    'metadata' => ['allocation_type' => 'float', 'provider' => $provider],
                ]);

                // If we reset the balance from a previous shift, create an adjustment line to record it
                if ($previousClosingBalance !== null && $oldBalance != $previousClosingBalance) {
                    $adjustmentAmount = $previousClosingBalance - $oldBalance;
                    TransactionLine::create([
                        'transaction_id' => $floatTx->id,
                        'account_id' => $floatAccount->id,
                        'amount' => $adjustmentAmount,
                        'balance_after' => $previousClosingBalance,
                        'description' => "Reset to previous shift closing balance ({$provider})",
                    ]);
                }

                TransactionLine::create([
                    'transaction_id' => $floatTx->id,
                    'account_id' => $floatAccount->id,
                    'amount' => $floatAmount,
                    'balance_after' => $floatAccount->balance,
                    'description' => "Opening float allocation ({$provider})",
                ]);

                // Settlement balancing
                $opsAccount->lockForUpdate();
                $opsAccount->balance -= $floatAmount; // Subtract negative = add positive
                $opsAccount->save();

                TransactionLine::create([
                    'transaction_id' => $floatTx->id,
                    'account_id' => $opsAccount->id,
                    'amount' => -$floatAmount,
                    'balance_after' => $opsAccount->balance,
                    'description' => "Settlement balancing - float allocation ({$provider})",
                ]);
            }

            // Create allocation records
            Allocation::create([
                'from_user_id' => $treasurer->id,
                'to_user_id' => $teller->id,
                'account_id' => $cashAccount->id,
                'amount' => $openingCash,
                'teller_shift_id' => $shift->id,
            ]);

            return $shift;
        });
    }

    /**
     * Process withdrawal (customer takes cash funded by float)
     */
    public function processWithdrawal(User $teller, TellerShift $shift, string $provider, int $amount, array $meta = [])
    {
        return DB::transaction(function () use ($teller, $shift, $provider, $amount, $meta) {
            // Lock rows to avoid race conditions
            $cash = Account::where('user_id', $teller->id)
                ->where('account_type', 'cash')
                ->where('provider', 'cash')
                ->lockForUpdate()
                ->first();

            $float = Account::where('user_id', $teller->id)
                ->where('account_type', 'float')
                ->where('provider', $provider)
                ->lockForUpdate()
                ->first();

            if (!$cash || !$float) {
                throw new \Exception('Account not found');
            }

            // Check cash available
            if ($cash->balance < $amount) {
                throw new \Exception('Insufficient cash');
            }

            // Create transaction
            $tx = MoneyPointTransaction::create([
                'type' => 'withdrawal',
                'teller_shift_id' => $shift->id,
                'user_id' => $teller->id,
                'reference' => $meta['reference'] ?? null,
                'metadata' => $meta,
            ]);

            // Cash decreases (teller gives cash to customer)
            $cash->balance -= $amount;
            $cash->save();

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $cash->id,
                'amount' => -$amount,
                'balance_after' => $cash->balance,
                'description' => 'Customer withdrawal cash out',
            ]);

            // Float increases (customer sends money TO teller's phone, so float display value increases)
            // Since float is stored negative, to increase display value we make system value more negative
            $float->balance -= $amount;
            $float->save();

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $float->id,
                'amount' => -$amount,
                'balance_after' => $float->balance,
                'description' => "Float withdrawal ({$provider}) - customer sent money to teller",
            ]);

            // Create balancing line to operations_settlement
            $opsAccount = Account::firstOrCreate(
                [
                    'user_id' => null,
                    'account_type' => 'bank',
                    'provider' => 'operations_settlement',
                ],
                [
                    'balance' => 0,
                    'currency' => 'TZS',
                    'is_active' => true,
                ]
            );

            $opsAccount->lockForUpdate();
            $opsAccount->balance += (2 * $amount);
            $opsAccount->save();

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $opsAccount->id,
                'amount' => 2 * $amount,
                'balance_after' => $opsAccount->balance,
                'description' => 'Settlement balancing - withdrawal',
            ]);

            return $tx;
        });
    }

    /**
     * Process deposit (customer gives cash; teller sends money to float)
     */
    public function processDeposit(User $teller, TellerShift $shift, string $provider, int $amount, array $meta = [])
    {
        return DB::transaction(function () use ($teller, $shift, $provider, $amount, $meta) {
            // Lock rows to avoid race conditions
            $cash = Account::where('user_id', $teller->id)
                ->where('account_type', 'cash')
                ->where('provider', 'cash')
                ->lockForUpdate()
                ->first();

            $float = Account::where('user_id', $teller->id)
                ->where('account_type', 'float')
                ->where('provider', $provider)
                ->lockForUpdate()
                ->first();

            if (!$cash || !$float) {
                throw new \Exception('Account not found');
            }

            // Create transaction
            $tx = MoneyPointTransaction::create([
                'type' => 'deposit',
                'teller_shift_id' => $shift->id,
                'user_id' => $teller->id,
                'reference' => $meta['reference'] ?? null,
                'metadata' => $meta,
            ]);

            // Cash increases (customer gives cash to teller)
            $cash->balance += $amount;
            $cash->save();

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $cash->id,
                'amount' => $amount,
                'balance_after' => $cash->balance,
                'description' => 'Customer deposit cash in',
            ]);

            // Float decreases (teller sends money FROM phone TO customer, so float display value decreases)
            // Since float is stored negative, to decrease display value we make system value less negative
            $float->balance += $amount;
            $float->save();

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $float->id,
                'amount' => $amount,
                'balance_after' => $float->balance,
                'description' => "Float deposit ({$provider}) - teller sent money to customer",
            ]);

            // Create balancing line to operations_settlement
            $opsAccount = Account::firstOrCreate(
                [
                    'user_id' => null,
                    'account_type' => 'bank',
                    'provider' => 'operations_settlement',
                ],
                [
                    'balance' => 0,
                    'currency' => 'TZS',
                    'is_active' => true,
                ]
            );

            $opsAccount->lockForUpdate();
            $opsAccount->balance -= (2 * $amount);
            $opsAccount->save();

            TransactionLine::create([
                'transaction_id' => $tx->id,
                'account_id' => $opsAccount->id,
                'amount' => -2 * $amount,
                'balance_after' => $opsAccount->balance,
                'description' => 'Settlement balancing - deposit',
            ]);

            return $tx;
        });
    }

    /**
     * Submit shift closing counts
     */
    public function submitShift(TellerShift $shift, int $closingCash, array $closingFloats, string $notes = null): TellerShift
    {
        return DB::transaction(function () use ($shift, $closingCash, $closingFloats, $notes) {
            if (!$shift->canSubmit()) {
                throw new \Exception('Shift cannot be submitted');
            }

            // Calculate expected closing balances
            $expectedCash = $this->calculateExpectedCash($shift);
            $expectedFloats = $this->calculateExpectedFloats($shift);

            // Calculate variances
            $varianceCash = $closingCash - $expectedCash;
            $varianceFloats = [];
            foreach ($expectedFloats as $provider => $expected) {
                $reported = $closingFloats[$provider] ?? 0;
                // Both reported and expected are in display format (positive)
                // Variance = reported - expected (in display format)
                $varianceFloats[$provider] = $reported - $expected;
            }

            // Calculate Mtaji (Opening Capital) = Opening Cash + Sum of Opening Floats
            $mtaji = $shift->opening_cash;
            $openingFloats = $shift->opening_floats ?? [];
            foreach ($openingFloats as $amount) {
                $mtaji += abs($amount); // Opening floats are stored negative, use abs for display
            }

            // Calculate Balanced (Closing Capital) = Closing Cash + Sum of Closing Floats
            $balanced = $closingCash;
            foreach ($closingFloats as $amount) {
                $balanced += abs($amount); // Closing floats are in display format (positive)
            }

            // Determine status:
            // If total Balanced equals Mtaji, it's balanced (submitted) even if individual variances exist
            // Otherwise, check if individual variances are all zero
            $isBalanced = ($balanced == $mtaji);
            $noIndividualVariances = ($varianceCash == 0 && array_sum(array_map('abs', $varianceFloats)) == 0);
            $status = ($isBalanced || $noIndividualVariances) ? 'submitted' : 'discrepancy';

            $shift->update([
                'closing_cash' => $closingCash,
                'closing_floats' => $closingFloats,
                'expected_closing_cash' => $expectedCash,
                'expected_closing_floats' => $expectedFloats,
                'variance_cash' => $varianceCash,
                'variance_floats' => $varianceFloats,
                'notes' => $notes,
                'status' => $status,
            ]);

            return $shift->fresh();
        });
    }

    /**
     * Verify shift
     */
    public function verifyShift(TellerShift $shift, string $action, array $adjustments = [], string $notes = null): TellerShift
    {
        return DB::transaction(function () use ($shift, $action, $adjustments, $notes) {
            if (!$shift->canVerify()) {
                throw new \Exception('Shift cannot be verified');
            }

            if ($action === 'approve') {
                $shift->update([
                    'status' => 'verified',
                    'closed_at' => now(),
                    'notes' => $notes,
                ]);
            } elseif ($action === 'request_adjustment') {
                // Create adjustment transactions
                foreach ($adjustments as $adjustment) {
                    $this->createAdjustment($shift, $adjustment);
                }
                $shift->update([
                    'status' => 'discrepancy',
                    'notes' => $notes,
                ]);
            }

            return $shift->fresh();
        });
    }

    /**
     * Calculate expected closing cash from transactions
     */
    private function calculateExpectedCash(TellerShift $shift): int
    {
        $openingCash = $shift->opening_cash;

        // Sum all cash transaction lines for this shift (excluding allocation)
        $cashAccount = Account::where('user_id', $shift->teller_id)
            ->where('account_type', 'cash')
            ->where('provider', 'cash')
            ->first();

        if (!$cashAccount) {
            return $openingCash;
        }

        // Calculate cash changes DURING the shift (excluding the opening allocation)
        $cashChanges = TransactionLine::whereHas('transaction', function ($query) use ($shift) {
            $query->where('teller_shift_id', $shift->id)
                ->where('type', '!=', 'allocation'); // Exclude allocation transactions
        })
            ->where('account_id', $cashAccount->id)
            ->sum('amount');

        return $openingCash + $cashChanges;
    }

    /**
     * Calculate expected closing floats from transactions
     */
    private function calculateExpectedFloats(TellerShift $shift): array
    {
        $openingFloats = $shift->opening_floats ?? [];
        $expectedFloats = [];

        foreach ($openingFloats as $provider => $openingFloat) {
            $floatAccount = Account::where('user_id', $shift->teller_id)
                ->where('account_type', 'float')
                ->where('provider', $provider)
                ->first();

            if (!$floatAccount) {
                $expectedFloats[$provider] = $openingFloat;
                continue;
            }

            // Calculate expected closing float
            // Opening float is stored as positive value in opening_floats JSON (e.g., 50,000,000)
            // We need to calculate changes DURING the shift (excluding the opening allocation)
            // With new business logic:
            //   - Withdrawal: customer sends money TO teller's phone → float INCREASES (display)
            //     System: -1,000,000 (becomes more negative) → Display: +1,000,000
            //   - Deposit: teller sends money FROM phone TO customer → float DECREASES (display)
            //     System: +500,000 (becomes less negative) → Display: -500,000
            // So: system change needs to be negated to get display change
            $floatChangesSystem = TransactionLine::whereHas('transaction', function ($query) use ($shift) {
                $query->where('teller_shift_id', $shift->id)
                    ->where('type', '!=', 'allocation'); // Exclude allocation transactions
            })
                ->where('account_id', $floatAccount->id)
                ->sum('amount');

            // Convert system changes to display changes (negate because system is stored negative)
            // System -1,000,000 → Display +1,000,000 (float increases)
            // System +500,000 → Display -500,000 (float decreases)
            $floatChangesDisplay = -$floatChangesSystem;

            // Expected = Opening (display) + Changes (display)
            $expectedFloats[$provider] = $openingFloat + $floatChangesDisplay;
        }

        return $expectedFloats;
    }

    /**
     * Create adjustment transaction
     */
    private function createAdjustment(TellerShift $shift, array $adjustment)
    {
        $account = Account::find($adjustment['account_id']);
        if (!$account) {
            throw new \Exception('Account not found for adjustment');
        }

        $account->lockForUpdate();
        $account->balance += $adjustment['amount'];
        $account->save();

        $tx = MoneyPointTransaction::create([
            'type' => 'adjustment',
            'teller_shift_id' => $shift->id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'metadata' => ['reason' => $adjustment['reason'] ?? 'Reconciliation adjustment'],
        ]);

        TransactionLine::create([
            'transaction_id' => $tx->id,
            'account_id' => $account->id,
            'amount' => $adjustment['amount'],
            'balance_after' => $account->balance,
            'description' => $adjustment['reason'] ?? 'Reconciliation adjustment',
        ]);

        // Settlement balancing
        $opsAccount = Account::firstOrCreate(
            [
                'user_id' => null,
                'account_type' => 'bank',
                'provider' => 'operations_settlement',
            ],
            [
                'balance' => 0,
                'currency' => 'TZS',
                'is_active' => true,
            ]
        );

        $opsAccount->lockForUpdate();
        $opsAccount->balance -= $adjustment['amount'];
        $opsAccount->save();

        TransactionLine::create([
            'transaction_id' => $tx->id,
            'account_id' => $opsAccount->id,
            'amount' => -$adjustment['amount'],
            'balance_after' => $opsAccount->balance,
            'description' => 'Settlement balancing - adjustment',
        ]);
    }
}
