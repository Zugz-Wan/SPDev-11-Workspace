<?php

use App\Http\Controllers\Admin\RekapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.rekap.index');
});

// Modul Admin & Rekap (FR-ADM)
Route::prefix('admin/rekap')->name('admin.rekap.')->group(function () {
    // Dashboard Overview
    Route::get('/', [RekapController::class, 'index'])->name('index');

    // FR-ADM-01: Rekap Okupansi Fasilitas Lintas Periode
    Route::get('/okupansi', [RekapController::class, 'okupansi'])->name('okupansi');
    
    // FR-ADM-02: Rekap Frekuensi Kerusakan per Fasilitas/Lokasi
    Route::get('/kerusakan', [RekapController::class, 'kerusakan'])->name('kerusakan');

    // FR-ADM-03: Export Rekap (CSV, Excel, PDF)
    Route::get('/okupansi/export/{format}', [RekapController::class, 'exportOkupansi'])->name('okupansi.export');
    Route::get('/kerusakan/export/{format}', [RekapController::class, 'exportKerusakan'])->name('kerusakan.export');
});
