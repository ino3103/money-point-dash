<?php

use App\Http\Controllers\MoneyPointController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('money-point.index');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password-update', [ProfileController::class, 'passwordUpdate'])->name('profile.password-update');

    // Money Point Routes
    Route::get('/money-point', [MoneyPointController::class, 'index'])->name('money-point.index');

    // Shifts
    Route::get('/money-point/shifts', [MoneyPointController::class, 'shifts'])->name('money-point.shifts');
    Route::get('/money-point/shifts/create', [MoneyPointController::class, 'createShift'])->name('money-point.shifts.create');
    Route::post('/money-point/shifts', [MoneyPointController::class, 'storeShift'])->name('money-point.shifts.store');
    Route::get('/money-point/shifts/{id}', [MoneyPointController::class, 'showShift'])->name('money-point.shifts.show');
    Route::get('/money-point/shifts/{id}/submit', [MoneyPointController::class, 'submitShiftForm'])->name('money-point.shifts.submit');
    Route::post('/money-point/shifts/{id}/submit', [MoneyPointController::class, 'submitShift'])->name('money-point.shifts.submit.store');
    Route::get('/money-point/shifts/{id}/verify', [MoneyPointController::class, 'verifyShiftForm'])->name('money-point.shifts.verify');
    Route::post('/money-point/shifts/{id}/verify', [MoneyPointController::class, 'verifyShift'])->name('money-point.shifts.verify.store');

    // Accounts
    Route::get('/money-point/accounts', [MoneyPointController::class, 'accounts'])->name('money-point.accounts');
    Route::get('/money-point/accounts/{id}/ledger', [MoneyPointController::class, 'accountLedger'])->name('money-point.accounts.ledger');

    // Transactions
    Route::get('/money-point/transactions', [MoneyPointController::class, 'transactions'])->name('money-point.transactions');
    Route::get('/money-point/transactions/{id}', [MoneyPointController::class, 'showTransaction'])->name('money-point.transactions.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/money-point/transactions/withdraw/create', [MoneyPointController::class, 'createWithdrawal'])->name('money-point.transactions.withdraw.create');
    Route::post('/money-point/transactions/withdraw', [MoneyPointController::class, 'storeWithdrawal'])->name('money-point.transactions.withdraw.store');
    Route::get('/money-point/transactions/deposit/create', [MoneyPointController::class, 'createDeposit'])->name('money-point.transactions.deposit.create');
    Route::post('/money-point/transactions/deposit', [MoneyPointController::class, 'storeDeposit'])->name('money-point.transactions.deposit.store');

    // Float Providers
    Route::get('/money-point/float-providers', [MoneyPointController::class, 'floatProviders'])->name('money-point.float-providers');
    Route::post('/money-point/float-providers', [MoneyPointController::class, 'storeFloatProvider'])->name('money-point.float-providers.store');
    Route::put('/money-point/float-providers', [MoneyPointController::class, 'updateFloatProvider'])->name('money-point.float-providers.update');
    Route::post('/money-point/float-providers/{id}/toggle', [MoneyPointController::class, 'toggleFloatProvider'])->name('money-point.float-providers.toggle');

    // Money Point Reports
    Route::get('/money-point/reports', [MoneyPointController::class, 'reports'])->name('money-point.reports');
    Route::get('/money-point/reports/shift-summary', [MoneyPointController::class, 'shiftSummaryReport'])->name('money-point.reports.shift-summary');
    Route::get('/money-point/reports/transactions', [MoneyPointController::class, 'transactionReport'])->name('money-point.reports.transactions');
    Route::get('/money-point/reports/float-balance', [MoneyPointController::class, 'floatBalanceReport'])->name('money-point.reports.float-balance');
    Route::get('/money-point/reports/variance', [MoneyPointController::class, 'varianceReport'])->name('money-point.reports.variance');
    Route::get('/money-point/reports/daily-summary', [MoneyPointController::class, 'dailySummaryReport'])->name('money-point.reports.daily-summary');
    Route::get('/money-point/reports/teller-performance', [MoneyPointController::class, 'tellerPerformanceReport'])->name('money-point.reports.teller-performance');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/destroy', [UserController::class, 'destroy'])->name('users.destroy');

    // Roles Management
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/destroy', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Settings Management
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/update', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/email-settings', [SettingController::class, 'emailSettings'])->name('email-settings');
    Route::post('/email-settings/update', [SettingController::class, 'updateEmailSettings'])->name('email-settings.update');
    Route::post('/send-test-email', [SettingController::class, 'sendTestEmail'])->name('test-email.send');
    Route::get('/sms-settings', [SettingController::class, 'smsSettings'])->name('sms-settings');
    Route::post('/sms-settings/update', [SettingController::class, 'updateSmsSettings'])->name('sms-settings.update');
    Route::post('/send-sms', [SettingController::class, 'sendSms'])->name('sms.send');
});

// Print receipt route - accessible via both web session and API token (for Flutter apps)
Route::middleware(['auth:sanctum'])->get('/money-point/transactions/{id}/print', [MoneyPointController::class, 'printReceipt'])->name('money-point.transactions.print');

require __DIR__ . '/auth.php';
