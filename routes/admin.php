<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ContactController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {
    Route::redirect('/', '/admin/login');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Quotes and Categories removed for now

    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::patch('/mark-all-as-read', [ContactController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::patch('/{contact}/read', [ContactController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/{contact}/reply', [ContactController::class, 'reply'])->name('reply');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });
});
