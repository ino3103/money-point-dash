<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\FloatProviderController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/v1/auth/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::get('/profile/login-history', [ProfileController::class, 'loginHistory']);

    // Dashboard
    Route::get('/money-point/dashboard', [DashboardController::class, 'index']);

    // Shifts
    Route::get('/money-point/shifts', [ShiftController::class, 'index']);
    Route::get('/money-point/shifts/create', [ShiftController::class, 'create']);
    Route::get('/money-point/shifts/{id}', [ShiftController::class, 'show']);
    Route::get('/money-point/shifts/{id}/submit', [ShiftController::class, 'submitForm']);
    Route::get('/money-point/shifts/{id}/verify', [ShiftController::class, 'verifyForm']);
    Route::post('/money-point/shifts', [ShiftController::class, 'store']);
    Route::post('/money-point/shifts/{id}/submit', [ShiftController::class, 'submit']);
    Route::post('/money-point/shifts/{id}/verify', [ShiftController::class, 'verify']);
    Route::post('/money-point/shifts/{id}/confirm-funds', [ShiftController::class, 'confirmFunds']);
    Route::post('/money-point/shifts/{id}/accept', [ShiftController::class, 'acceptShift']);
    Route::post('/money-point/shifts/{id}/reject', [ShiftController::class, 'rejectShift']);

    // Accounts
    Route::get('/money-point/accounts', [AccountController::class, 'index']);
    Route::get('/money-point/accounts/{id}/ledger', [AccountController::class, 'ledger']);

    // Transactions
    Route::get('/money-point/transactions', [TransactionController::class, 'index']);
    Route::get('/money-point/transactions/{id}', [TransactionController::class, 'show']);
    Route::get('/money-point/transactions/{id}/print-url', [TransactionController::class, 'printReceipt']);
    Route::post('/money-point/transactions/withdraw', [TransactionController::class, 'withdraw']);
    Route::post('/money-point/transactions/deposit', [TransactionController::class, 'deposit']);

    // Float Providers
    Route::get('/money-point/float-providers', [FloatProviderController::class, 'index']);
    Route::post('/money-point/float-providers', [FloatProviderController::class, 'store']);
    Route::put('/money-point/float-providers/{id}', [FloatProviderController::class, 'update']);
    Route::post('/money-point/float-providers/{id}/toggle', [FloatProviderController::class, 'toggle']);

    // Reports
    Route::get('/money-point/reports/shift-summary', [ReportController::class, 'shiftSummary']);
    Route::get('/money-point/reports/transactions', [ReportController::class, 'transactions']);
    Route::get('/money-point/reports/float-balance', [ReportController::class, 'floatBalance']);
    Route::get('/money-point/reports/variance', [ReportController::class, 'variance']);
    Route::get('/money-point/reports/daily-summary', [ReportController::class, 'dailySummary']);
    Route::get('/money-point/reports/teller-performance', [ReportController::class, 'tellerPerformance']);

    // Users Management (Admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::put('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // Roles Management (Admin)
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    // Settings Management (Admin)
    Route::get('/settings', [SettingController::class, 'index']);
    Route::put('/settings', [SettingController::class, 'update']);
    Route::get('/settings/email', [SettingController::class, 'emailSettings']);
    Route::put('/settings/email', [SettingController::class, 'updateEmailSettings']);
    Route::post('/settings/email/test', [SettingController::class, 'testEmail']);
    Route::get('/settings/sms', [SettingController::class, 'smsSettings']);
    Route::put('/settings/sms', [SettingController::class, 'updateSmsSettings']);
    Route::post('/settings/sms/test', [SettingController::class, 'testSms']);
});
