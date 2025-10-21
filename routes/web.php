<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\Admin\PemesananAdminController;
use App\Models\PaymentAccount;
use App\Support\PemesananDictionary;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->jenis_user === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    $statusLabels = PemesananDictionary::statusLabels();
    $paymentStatusLabels = PemesananDictionary::paymentStatusLabels();
    $statusGuidance = PemesananDictionary::statusGuidance();

    $pemesananCollection = $user->pemesanan()->with('wisma')->orderByDesc('created_at')->get();

    $statistik = [
        'total' => $pemesananCollection->count(),
    ];
    foreach ($statusLabels as $status => $label) {
        $statistik[$status] = $pemesananCollection->where('status', $status)->count();
    }

    $statistikPembayaran = [];
    foreach ($paymentStatusLabels as $status => $label) {
        $statistikPembayaran[$status] = $pemesananCollection->where('status_pembayaran', $status)->count();
    }

    $activeStatuses = ['reservasi', 'diproses', 'check_in'];
    $activeBooking = $pemesananCollection->first(function ($item) use ($activeStatuses) {
        return in_array($item->status, $activeStatuses, true);
    });

    $recentBookings = $pemesananCollection->take(5);
    $progressSteps = array_keys($statusLabels);

    $paymentAccounts = PaymentAccount::where('aktif', true)->orderBy('nama_bank')->get();

    return view('dashboard', [
        'statistik' => $statistik,
        'statistikPembayaran' => $statistikPembayaran,
        'statusLabels' => $statusLabels,
        'paymentStatusLabels' => $paymentStatusLabels,
        'activeBooking' => $activeBooking,
        'recentBookings' => $recentBookings,
        'statusGuidance' => $statusGuidance,
        'progressSteps' => $progressSteps,
        'paymentAccounts' => $paymentAccounts,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::get('/pemesanan', [PemesananController::class, 'create'])->name('pemesanan.create');
    Route::post('/pemesanan', [PemesananController::class, 'store'])->name('pemesanan.store');

    Route::get('/pemesanan/saya', [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::get('/pemesanan/saya/{pemesanan}', [PemesananController::class, 'show'])->name('pemesanan.show');
    Route::post('/pemesanan/saya/{pemesanan}/upload-bukti', [PemesananController::class, 'uploadBukti'])->name('pemesanan.uploadBukti');
    Route::get('/pemesanan/saya/{pemesanan}/bukti', [PemesananController::class, 'downloadBukti'])->name('pemesanan.downloadBukti');
    Route::get('/pemesanan/saya/{pemesanan}/kuitansi', [PemesananController::class, 'downloadKuitansi'])->name('pemesanan.downloadKuitansi');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [PemesananAdminController::class, 'overview'])->name('dashboard');

    Route::get('/pemesanan', [PemesananAdminController::class, 'index'])->name('pemesanan');
    Route::get('/pemesanan/{id}', [PemesananAdminController::class, 'show'])->name('pemesanan.show');
    Route::get('/pemesanan/{id}/bukti', [PemesananAdminController::class, 'downloadBukti'])->name('pemesanan.downloadBukti');
    Route::get('/pemesanan/{id}/kuitansi', [PemesananAdminController::class, 'kuitansi'])->name('pemesanan.kuitansi');
    Route::get('/pembayaran/menunggu', [PemesananAdminController::class, 'pendingPayments'])->name('pembayaran.pending');
    Route::get('/laporan/pemesanan-selesai', [PemesananAdminController::class, 'rekapSelesai'])->name('laporan.pemesananSelesai');
    Route::put('/pemesanan/{id}', [PemesananAdminController::class, 'updateDetail'])->name('pemesanan.update');
    Route::post('/pemesanan/{id}/konfirmasi-pembayaran', [PemesananAdminController::class, 'konfirmasiPembayaran'])->name('pemesanan.konfirmasiPembayaran');
    Route::post('/pemesanan/{id}/ubah-status', [PemesananAdminController::class, 'ubahStatus'])->name('pemesanan.ubahStatus');
    Route::delete('/pemesanan/{id}/batalkan', [PemesananAdminController::class, 'batalkan'])->name('pemesanan.batalkan');
});
require __DIR__.'/auth.php';

