<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\OfficerReportController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Guest & Authentication routes
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isPetugas()
            ? redirect()->route('officer.reports.index')
            : redirect()->route('reports.index');
    }

    return redirect()->route('facilities.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick-login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Facilities overview (Accessible by all)
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');

// User Reporting Routes (FR-REP-01 & FR-REP-02)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
});

// Officer Queue & Status Management Routes (FR-REP-03 & FR-REP-04 & FR-REP-05)
Route::middleware(['auth', 'role:petugas,admin'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/reports', [OfficerReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{report}/status', [OfficerReportController::class, 'updateStatus'])->name('reports.update-status');
});
