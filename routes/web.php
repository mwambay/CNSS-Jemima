<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\DeclarationInterfaceController;
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
    Route::get('/declarations', [DeclarationInterfaceController::class, 'index'])->name('declarations.interface');
    Route::get('/declarations/{declaration}', [DeclarationInterfaceController::class, 'show'])->name('declarations.show');

    Route::prefix('api')
        ->middleware('role:ADMIN')
        ->group(function (): void {
            Route::apiResource('employers', EmployerController::class);
            Route::apiResource('workers', WorkerController::class);
            Route::apiResource('declarations', DeclarationController::class);
            Route::post('declarations/{declaration}/submit', [DeclarationController::class, 'submit']);
            Route::post('declarations/{declaration}/validate', [DeclarationController::class, 'validateDeclaration']);
            Route::post('declarations/{declaration}/reject', [DeclarationController::class, 'rejectDeclaration']);
            Route::post('declarations/{declaration}/recalculate', [DeclarationController::class, 'recalculate']);
            Route::post('declarations/{declaration}/lines', [DeclarationController::class, 'upsertLine']);
            Route::delete('declarations/{declaration}/lines/{declarationLine}', [DeclarationController::class, 'destroyLine']);
        });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
