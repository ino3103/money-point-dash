<?php

use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\Loan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!function_exists('isActiveRoute')) {
    function isActiveRoute(array $routes, $output = 'active')
    {
        return in_array(Route::currentRouteName(), $routes) ? $output : '';
    }
}

if (!function_exists('isConnectedToInternet')) {
    function isConnectedToInternet()
    {
        $connected = @fsockopen("www.google.com", 80);
        if ($connected) {
            fclose($connected);
            return true;
        }
        return false;
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null)
    {
        static $settingsCache = null;

        // Check if the settings have already been cached
        if ($settingsCache === null) {
            // Check if the database connection is working
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                return $default;
            }

            // Check if the settings table exists
            if (!Schema::hasTable('settings')) {
                return $default;
            }

            // Cache all settings in memory
            $settingsCache = Setting::pluck('value', 'key')->toArray();
        }

        return $settingsCache[$key] ?? $default;
    }
}


if (!function_exists('human_filesize')) {
    function human_filesize($bytes, $decimals = 2)
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}

if (!function_exists('convertTo24HourFormat')) {
    function convertTo24HourFormat($time)
    {
        return Carbon::createFromFormat('h:i A', $time)->format('H:i');
    }
}

if (!function_exists('getPaymentMethods')) {
    function getPaymentMethods()
    {
        $paymentMethods = env('PAYMENT_METHODS', 'cash,bank,mobile_money');

        return array_map('trim', explode(',', $paymentMethods));
    }
}

if (!function_exists('getTransactionTypes')) {
    function getTransactionTypes()
    {
        $transactionTypes = env('TRANSACTION_TYPES', 'registration,shares,loan,repayment,other');

        return array_map('trim', explode(',', $transactionTypes));
    }
}

if (!function_exists('getTransactionNatures')) {
    function getTransactionNatures()
    {
        $transactionNatures = env('TRANSACTION_NATURES', 'credit,debit');

        return array_map('trim', explode(',', $transactionNatures));
    }
}

if (!function_exists('shareDebitIncomeCategory')) {
    function shareDebitIncomeCategory()
    {
        $shareDebitIncomeCategory = env('SHARE_DEBIT_INCOME_CATEGORY');

        return $shareDebitIncomeCategory;
    }
}

if (!function_exists('loanFeeIncomeCategory')) {
    function loanFeeIncomeCategory()
    {
        $loanFeeIncomeCategory = env('LOAN_FEE_INCOME_CATEGORY');

        return $loanFeeIncomeCategory;
    }
}

if (!function_exists('loanInterestIncomeCategory')) {
    function loanInterestIncomeCategory()
    {
        $loanInterestIncomeCategory = env('LOAN_INTEREST_INCOME_CATEGORY', 6); // Default to category 2 if not set

        return $loanInterestIncomeCategory;
    }
}

if (!function_exists('getLoanSettings')) {
    function getLoanSettings()
    {
        return [
            'loan_fee' => getSetting('loan_fee', 5000),
            'max_loan_multiplier' => getSetting('max_loan_multiplier', 3),
            'loan_overdue_days' => getSetting('loan_overdue_days', 30),
            'default_interest_rate' => getSetting('default_interest_rate', 20),
            'min_loan_amount' => getSetting('min_loan_amount', 10000),
            'max_loan_amount' => getSetting('max_loan_amount', 1000000),
            'grace_period_days' => getSetting('grace_period_days', 7),
        ];
    }
}

if (!function_exists('validateLoanAmount')) {
    function validateLoanAmount($amount)
    {
        $settings = getLoanSettings();

        if ($amount < $settings['min_loan_amount']) {
            return ['valid' => false, 'message' => "Minimum loan amount is " . formatCurrency($settings['min_loan_amount'])];
        }

        if ($amount > $settings['max_loan_amount']) {
            return ['valid' => false, 'message' => "Maximum loan amount is " . formatCurrency($settings['max_loan_amount'])];
        }

        return ['valid' => true, 'message' => 'Amount is valid'];
    }
}

if (!function_exists('isRepaymentOverdue')) {
    function isRepaymentOverdue($dueDate, $paymentDate = null)
    {
        if (!$dueDate) {
            return false;
        }

        $dueDate = \Carbon\Carbon::parse($dueDate);
        $today = \Carbon\Carbon::today();

        // If payment date is provided, check if payment was made after due date
        if ($paymentDate) {
            $paymentDate = \Carbon\Carbon::parse($paymentDate);
            return $paymentDate->gt($dueDate);
        }

        // Otherwise check if today is past the due date
        return $today->gt($dueDate);
    }
}

if (!function_exists('getNextRepaymentDueDate')) {
    function getNextRepaymentDueDate($loan, $repaymentNumber = null)
    {
        if (!$loan->first_repayment_due_date) {
            return null;
        }

        // Ensure we have a Carbon instance
        $firstDueDate = \Carbon\Carbon::parse($loan->first_repayment_due_date);

        if ($repaymentNumber) {
            return $firstDueDate->addMonths($repaymentNumber - 1);
        }

        // Get the next due date based on number of existing repayments
        $existingRepayments = $loan->loanRepayments()->count();
        return $firstDueDate->addMonths($existingRepayments);
    }
}

if (!function_exists('getDaysOverdue')) {
    function getDaysOverdue($dueDate, $paymentDate = null)
    {
        if (!$dueDate) {
            return 0;
        }

        $dueDate = \Carbon\Carbon::parse($dueDate);
        $today = \Carbon\Carbon::today();

        // If payment date is provided, calculate days overdue from payment date
        if ($paymentDate) {
            $paymentDate = \Carbon\Carbon::parse($paymentDate);
            if ($paymentDate->gt($dueDate)) {
                return $paymentDate->diffInDays($dueDate);
            }
            return 0;
        }

        // Otherwise calculate days overdue from today
        if ($today->gt($dueDate)) {
            return $today->diffInDays($dueDate);
        }

        return 0;
    }
}

if (!function_exists('formatDaysOverdue')) {
    function formatDaysOverdue($days)
    {
        if ($days == 0) {
            return '';
        }

        if ($days == 1) {
            return '1 day';
        }

        return $days . ' days';
    }
}

if (!function_exists('calculateBalance')) {
    function calculateBalance()
    {
        $totalIncomes = Income::where('status', 'approved')->sum('amount');

        $totalExpenses = Expense::where('status', 'approved')->sum('amount');

        $totalSharesAmount = Transaction::where('status', 'approved')
            ->where('transaction_type', 'shares')
            ->where('transaction_nature', 'credit')
            ->sum('amount');

        $totalLoan = Loan::where('status', 'approved')->sum('amount');

        $balanceBeforeLoan = ($totalIncomes + $totalSharesAmount) - $totalExpenses;

        $balanceAfterLoan = $balanceBeforeLoan - $totalLoan;

        return [
            'totalIncomes' => $totalIncomes,
            'totalExpenses' => $totalExpenses,
            'totalSharesAmount' => $totalSharesAmount,
            'totalLoan' => $totalLoan,
            'balanceBeforeLoan' => $balanceBeforeLoan,
            'balanceAfterLoan' => $balanceAfterLoan,
        ];
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $decimals = 2, $showSymbol = true)
    {
        $currencySymbol = getSetting('currency_symbol', 'TZS');
        $formattedAmount = number_format($amount, $decimals);

        if ($showSymbol) {
            return $currencySymbol . ' ' . $formattedAmount;
        }

        return $formattedAmount;
    }
}

if (!function_exists('formatAmount')) {
    function formatAmount($amount, $decimals = 2)
    {
        return number_format($amount, $decimals);
    }
}


if (!function_exists('loanPenaltyIncomeCategory')) {
    function loanPenaltyIncomeCategory()
    {
        $loanPenaltyIncomeCategory = env('LOAN_PENALTY_INCOME_CATEGORY', 7);

        return $loanPenaltyIncomeCategory;
    }
}
