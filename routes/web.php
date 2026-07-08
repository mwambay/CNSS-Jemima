<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AffiliationRequestController;
use App\Http\Controllers\ContributionRateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\DeclarationInterfaceController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\EmployerInterfaceController;
use App\Http\Controllers\SdtReportController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkerInterfaceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing');
})->name('landing');

Route::get('/affiliation', [AffiliationRequestController::class, 'create'])->name('affiliation.create');
Route::post('/affiliation', [AffiliationRequestController::class, 'store'])->name('affiliation.store');
Route::get('/affiliation/{affiliationRequest}/soumise', [AffiliationRequestController::class, 'submitted'])->name('affiliation.submitted');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:ADMIN,AGENT_SES')->group(function (): void {
        Route::get('/employeurs', [EmployerInterfaceController::class, 'index'])->name('employers.interface');
        Route::get('/employeurs/{employer}', [EmployerInterfaceController::class, 'show'])->name('employers.show');
        Route::get('/travailleurs', [WorkerInterfaceController::class, 'index'])->name('workers.interface');
        Route::get('/declarations', [DeclarationInterfaceController::class, 'index'])->name('declarations.interface');
        Route::get('/declarations/{declaration}', [DeclarationInterfaceController::class, 'show'])->name('declarations.show');
        Route::get('/affiliations', [AffiliationRequestController::class, 'index'])->name('affiliations.index');
        Route::get('/affiliations/{affiliationRequest}', [AffiliationRequestController::class, 'show'])->name('affiliations.show');
        Route::post('/affiliations/{affiliationRequest}/request-sdt-opinion', [AffiliationRequestController::class, 'requestSdtOpinion'])->name('affiliations.request-sdt-opinion');
        Route::post('/affiliations/{affiliationRequest}/approve', [AffiliationRequestController::class, 'approve'])->name('affiliations.approve');
        Route::post('/affiliations/{affiliationRequest}/reject', [AffiliationRequestController::class, 'reject'])->name('affiliations.reject');
    });

    Route::middleware('role:SDT')->group(function (): void {
        Route::get('/sdt/affiliations', [AffiliationRequestController::class, 'sdtIndex'])->name('sdt.affiliations.index');
        Route::get('/sdt/affiliations/{affiliationRequest}', [AffiliationRequestController::class, 'sdtShow'])->name('sdt.affiliations.show');
        Route::post('/sdt/affiliations/{affiliationRequest}/opinion', [AffiliationRequestController::class, 'submitSdtOpinion'])->name('sdt.affiliations.opinion');
        Route::get('/sdt/rapports/cotisations', [SdtReportController::class, 'contributions'])->name('sdt.reports.contributions');
        Route::get('/sdt/rapports/activite', [SdtReportController::class, 'activity'])->name('sdt.reports.activity');
    });

    Route::middleware('role:ADMIN')->group(function (): void {
        Route::get('/parametres/cotisations', [ContributionRateController::class, 'index'])->name('contribution-rates.index');
        Route::post('/parametres/cotisations', [ContributionRateController::class, 'store'])->name('contribution-rates.store');
        Route::put('/parametres/cotisations/{contributionRate}', [ContributionRateController::class, 'update'])->name('contribution-rates.update');
        Route::get('/utilisateurs', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/utilisateurs', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/utilisateurs/{user}', [UserManagementController::class, 'update'])->name('users.update');
    });

    Route::prefix('api')
        ->middleware('role:ADMIN,AGENT_SES')
        ->group(function (): void {
            Route::apiResource('employers', EmployerController::class);
            Route::apiResource('workers', WorkerController::class);
            Route::apiResource('declarations', DeclarationController::class);
            Route::post('declarations/{declaration}/submit', [DeclarationController::class, 'submit']);
            Route::post('declarations/{declaration}/validate', [DeclarationController::class, 'validateDeclaration']);
            Route::post('declarations/{declaration}/reject', [DeclarationController::class, 'rejectDeclaration']);
            Route::post('declarations/{declaration}/recalculate', [DeclarationController::class, 'recalculate']);
            Route::post('declarations/{declaration}/global-contribution', [DeclarationController::class, 'recordGlobalContribution']);
            Route::get('declarations/{declaration}/global-contribution-preview', [DeclarationController::class, 'previewGlobalContribution']);
            Route::post('declarations/{declaration}/use-detailed-entry', [DeclarationController::class, 'useDetailedEntry']);
            Route::post('declarations/{declaration}/lines', [DeclarationController::class, 'upsertLine']);
            Route::delete('declarations/{declaration}/lines/{declarationLine}', [DeclarationController::class, 'destroyLine']);
        });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
