<?php

use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\OfficerReportController;
use App\Http\Controllers\PetugasReservationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{id}', [FacilityController::class, 'show'])->name('facilities.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');

    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.my');
    Route::get('/reservations/{id}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/reservations', [PetugasReservationController::class, 'index'])->name('reservations.index');
    Route::put('/reservations/{id}/approve', [PetugasReservationController::class, 'approve'])->name('reservations.approve');
    Route::put('/reservations/{id}/reject', [PetugasReservationController::class, 'reject'])->name('reservations.reject');
    Route::put('/reservations/{id}/cancel', [PetugasReservationController::class, 'cancel'])->name('reservations.cancel');
});

Route::middleware(['auth', 'role:petugas,admin'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/reports', [OfficerReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{report}/status', [OfficerReportController::class, 'updateStatus'])->name('reports.update-status');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('facilities', AdminFacilityController::class);

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/verify', [AdminUserController::class, 'verify'])->name('users.verify');
    Route::patch('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');

    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export/csv', [RekapController::class, 'exportCsv'])->name('rekap.export.csv');
    Route::get('/rekap/print', [RekapController::class, 'print'])->name('rekap.print');
});
