<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyServiceController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');

// Invitation-based registration
Route::get('/register/{token}', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register/{token}', [RegisterController::class, 'store'])->name('register.store');

// Login / logout
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Password reset
Route::get('password/reset/{token}', [PasswordResetController::class, 'show'])->name('password.reset');
Route::post('password/reset', [PasswordResetController::class, 'store'])->name('password.update');

// Admin-only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/invite', [InviteController::class, 'show'])->name('invite.show');
    Route::post('/invite', [InviteController::class, 'store'])->name('invite.store');

    Route::resource('users', UserController::class);
    Route::resource('services', ServiceController::class)->except('show');

    Route::resource('customers', CustomerController::class)->except('destroy');
    Route::post('customers/{customer}/portal', [CustomerController::class, 'grantPortalAccess'])->name('customers.portal.grant');
    Route::post('customers/{customer}/portal/resend', [CustomerController::class, 'resendPortalInvite'])->name('customers.portal.resend');

    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::resource('customers.properties', PropertyController::class)
        ->shallow()
        ->only(['create', 'store', 'show', 'edit', 'update']);
    Route::post('properties/{property}/status', [PropertyController::class, 'status'])->name('properties.status');

    Route::resource('properties.property-services', PropertyServiceController::class)
        ->shallow()
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});
