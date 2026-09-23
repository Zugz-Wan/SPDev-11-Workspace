<?php

use App\Http\Controllers\PetugasReservationController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes untuk pengguna yang telah terautentikasi
Route::middleware('auth')->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/my-reservations', [ReservationController::class, 'index']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

    // Routes khusus petugas
    Route::prefix('petugas')->group(function () {
        Route::get('/reservations', [PetugasReservationController::class, 'index']);
        Route::put('/reservations/{id}/approve', [PetugasReservationController::class, 'approve']);
        Route::put('/reservations/{id}/reject', [PetugasReservationController::class, 'reject']);
        Route::put('/reservations/{id}/cancel', [PetugasReservationController::class, 'cancel']);
    });
});

