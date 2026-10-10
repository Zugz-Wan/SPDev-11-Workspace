<?php

use App\Http\Controllers\Admin\RecapController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\OfficerReportController;
use App\Http\Controllers\PetugasReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Guest & Authentication routes
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.facilities.index');
        }

        return auth()->user()->isPetugas()
            ? redirect()->route('officer.reports.index')
            : redirect()->route('reports.index');
    }

    return redirect()->route('facilities.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// User Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Facilities overview & detail (Accessible by all)
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{id}', [FacilityController::class, 'show'])->name('facilities.show');

// User Reporting Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
});

// User Reservations Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.my');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{id}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

// Officer Queue & Status Management Routes
Route::middleware(['auth', 'role:petugas,admin'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/reports', [OfficerReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{report}/status', [OfficerReportController::class, 'updateStatus'])->name('reports.update-status');
});

// Officer Reservations Routes
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/reservations', [PetugasReservationController::class, 'index'])->name('reservations.index');
    Route::put('/reservations/{id}/approve', [PetugasReservationController::class, 'approve'])->name('reservations.approve');
    Route::put('/reservations/{id}/reject', [PetugasReservationController::class, 'reject'])->name('reservations.reject');
    Route::put('/reservations/{id}/cancel', [PetugasReservationController::class, 'cancel'])->name('reservations.cancel');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('facilities', App\Http\Controllers\Admin\FacilityController::class);

    // User management & verification
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{id}/verify', [UserController::class, 'verify'])->name('users.verify');
    Route::delete('/users/{id}/reject', [UserController::class, 'reject'])->name('users.reject');

    // Rekapitulasi & Export
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export/csv', [RekapController::class, 'exportCsv'])->name('rekap.export.csv');
    Route::get('/rekap/print', [RekapController::class, 'print'])->name('rekap.print');
    Route::get('/recap', [RecapController::class, 'index'])->name('recap.index');
    Route::get('/recap/export', [RecapController::class, 'exportCsv'])->name('recap.export');
});
