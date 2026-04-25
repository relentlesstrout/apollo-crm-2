<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Invitation-based registration
Route::get('/register/{token}', [RegisterController::class, 'show'])->name('register.show');
Route::post('/register/{token}', [RegisterController::class, 'store'])->name('register.store');

// Login / logout
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Admin-only: send invites
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/invite', [InviteController::class, 'show'])->name('invite.show');
    Route::post('/invite', [InviteController::class, 'store'])->name('invite.store');

    Route::resource('users', UserController::class);
});

