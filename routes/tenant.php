<?php

use App\Http\Controllers\Tenant\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:tenant')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'store'])->name('tenant.login.store');
});

Route::middleware('tenant.auth')->group(function (): void {
    Route::view('/dashboard', 'tenant.dashboard')->name('tenant.dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('tenant.logout');
});
