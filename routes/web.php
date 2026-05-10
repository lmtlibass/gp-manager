<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\PackagePageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::view('/', 'welcome');

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');

    Route::get('/forgot-password', [PasswordController::class, 'showForgotForm'])->name('password.request');
    Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
});

// Routes protégées - authentification requise
Route::middleware(['auth', 'active'])->group(function () {
    // Déconnexion
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profil utilisateur
    Route::get('/profile', [UserController::class, 'showProfile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.update.profile');

    // Dashboard
    Route::view('/home', 'dashboard')->name('dashboard');

    Route::middleware(['role:admin,gp_manager,agent'])->group(function () {
        Route::view('/packages', 'packages.index')->name('packages.index');
        Route::view('/packages/create', 'packages.form')->name('packages.create');
        Route::get('/packages/{package}', [PackagePageController::class, 'show'])->name('packages.show');
        Route::get('/packages/{package}/edit', [PackagePageController::class, 'edit'])->name('packages.edit');

        Route::view('/customers', 'customers.index')->name('customers.index');
        Route::view('/withdrawals', 'withdrawals.process')->name('withdrawals.process');
        Route::view('/search', 'search.index')->name('search.index');
        Route::view('/notifications', 'notifications.index')->name('notifications.index');
    });

    // Gestion des utilisateurs - Admin et GP Manager uniquement
    Route::middleware(['role:admin,gp_manager'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
