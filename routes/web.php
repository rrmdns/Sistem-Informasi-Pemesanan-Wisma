<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\Admin\PemesananAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Utama (Landing)
Route::redirect('/', '/dashboard');

// Dashboard setelah login
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Jika admin, langsung arahkan ke dashboard admin
    if ($user->jenis_user === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    // Kalau bukan admin, tampilkan dashboard user biasa
    return view('dashboard');})->middleware(['auth', 'verified'])->name('dashboard');

// Grup Route yang hanya bisa diakses jika sudah login
Route::middleware(['auth'])->group(function () {

    // Profil User
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Formulir & Penyimpanan Pemesanan Wisma
    Route::get('/pemesanan', [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan', [PemesananController::class, 'store'])->name('pemesanan.store');

    // ✅ Tambahan: Halaman Daftar Pemesanan Saya
    Route::get('/pemesanan/saya', [PemesananController::class, 'index'])->name('pemesanan.index');
});

// ✅ Rute untuk ADMIN (akses hanya jika jenis_user = 'admin')
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // ✅ Dashboard Statistik Admin
    Route::get('/dashboard', [PemesananAdminController::class, 'overview'])->name('dashboard');

    // ✅ Halaman Daftar Semua Pemesanan (dipindah dari dashboard)
    Route::get('/pemesanan', [PemesananAdminController::class, 'index'])->name('pemesanan');

    // ✅ Ubah status pemesanan
    Route::post('/pemesanan/{id}/ubah-status', [PemesananAdminController::class, 'ubahStatus'])->name('pemesanan.ubahStatus');

    // ✅ Tambahan route untuk pembatalan
    Route::delete('/pemesanan/{id}/batalkan', [PemesananAdminController::class, 'batalkan'])->name('pemesanan.batalkan');
});

// Routing untuk autentikasi bawaan Laravel Breeze
require __DIR__.'/auth.php';
