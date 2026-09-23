<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\FacilityController;
use App\Models\Facility;
use Illuminate\Support\Facades\Route;

// Redirect root ke halaman daftar fasilitas
Route::get('/', function () {
    return redirect('/facilities');
});

// ===== AUTH =====
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== FASILITAS (PUBLIK) =====
Route::get('/facilities', function () {
    $facilities = Facility::where('status', 'tersedia')->latest()->paginate(9);
    return view('facilities.index', compact('facilities'));
})->name('facilities.index');

Route::get('/facilities/{id}', function ($id) {
    $facility = Facility::findOrFail($id);
    return view('facilities.show', compact('facility'));
})->name('facilities.show');

// ===== ADMIN (khusus role:admin) =====
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('facilities', FacilityController::class);
});