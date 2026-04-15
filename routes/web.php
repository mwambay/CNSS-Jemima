<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\EmployerInterfaceController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkerInterfaceController;
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
    Route::get('/employeurs/{employer}', [EmployerInterfaceController::class, 'show'])->name('employers.show');
    Route::get('/travailleurs', [WorkerInterfaceController::class, 'index'])->name('workers.interface');

    Route::prefix('api')
        ->middleware('role:ADMIN')
        ->group(function (): void {
            Route::apiResource('employers', EmployerController::class);
            Route::apiResource('workers', WorkerController::class);
        });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
