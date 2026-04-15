<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerInterfaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/employeurs', [EmployerInterfaceController::class, 'index'])->name('employers.interface');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
