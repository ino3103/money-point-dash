<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FloatTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

// Language switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'sw'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    // Redirect back or to login if no referrer
    return redirect()->back()->with('locale_changed', true);
})->name('lang.switch');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/data', [RoleController::class, 'getData'])->name('roles.data');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Branches
    Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/branches/data', [BranchController::class, 'getData'])->name('branches.data');
    Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    Route::patch('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/data', [UserController::class, 'getData'])->name('users.data');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Float Types
    Route::get('/float-types', [FloatTypeController::class, 'index'])->name('float-types.index');
    Route::get('/float-types/data', [FloatTypeController::class, 'getData'])->name('float-types.data');
    Route::get('/float-types/create', [FloatTypeController::class, 'create'])->name('float-types.create');
    Route::post('/float-types', [FloatTypeController::class, 'store'])->name('float-types.store');
    Route::get('/float-types/{floatType}/edit', [FloatTypeController::class, 'edit'])->name('float-types.edit');
    Route::patch('/float-types/{floatType}', [FloatTypeController::class, 'update'])->name('float-types.update');
    Route::delete('/float-types/{floatType}', [FloatTypeController::class, 'destroy'])->name('float-types.destroy');
});

require __DIR__ . '/auth.php';
