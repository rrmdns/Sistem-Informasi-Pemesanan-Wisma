<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingStatusUpdatedNotification;
use App\Notifications\PaymentConfirmedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PemesananAdminController extends Controller
{
    private const STATUS_OPTIONS = [
        'reservasi' => 'Reservasi',
        'diproses'  => 'Diproses',
        'check_in'  => 'Check In',
        'check_out' => 'Check Out',
    ];

    private const PAYMENT_STATUS_OPTIONS = [
        'belum' => 'Belum Dibayar',
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'selesai' => 'Pembayaran Selesai',
    ];

    public function overview()
    {
        $semuaPemesanan = Pemesanan::with(['user', 'wisma'])->get();

        $statistik = [
            'total' => $semuaPemesanan->count(),
        ];

        foreach (self::STATUS_OPTIONS as $status => $label) {
            $statistik[$status] = $semuaPemesanan->where('status', $status)->count();
        }

        $statistikPembayaran = [];
        foreach (self::PAYMENT_STATUS_OPTIONS as $status => $label) {
            $statistikPembayaran[$status] = $semuaPemesanan->where('status_pembayaran', $status)->count();
        }

        $recentBookings = Pemesanan::with(['user', 'wisma'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $upcomingCheckins = Pemesanan::with(['user', 'wisma'])
            ->whereNotNull('check_in_at')
            ->whereDate('check_in_at', '>=', Carbon::today())
            ->orderBy('check_in_at')
            ->take(5)
            ->get();

        $pendingPayments = Pemesanan::with(['user', 'wisma'])
            ->where('status_pembayaran', 'menunggu_konfirmasi')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'statistik'            => $statistik,
            'statusLabels'         => self::STATUS_OPTIONS,
            'statistikPembayaran'  => $statistikPembayaran,
            'pembayaranLabels'     => self::PAYMENT_STATUS_OPTIONS,
            'recentBookings'       => $recentBookings,
            'upcomingCheckins'     => $upcomingCheckins,
            'pendingPayments'      => $pendingPayments,
        ]);
    }

    public function index()
    {
        $semuaPemesanan = Pemesanan::with(['user', 'wisma'])->orderByDesc('created_at')->get();

        return view('admin.pemesanan', [
            'semuaPemesanan'       => $semuaPemesanan,
            'statusOptions'        => self::STATUS_OPTIONS,
            'paymentStatusOptions' => self::PAYMENT_STATUS_OPTIONS,
        ]);
    }

    public function pendingPayments()
    {
        $pendingPayments = Pemesanan::with(['user', 'wisma'])
            ->where('status_pembayaran', 'menunggu_konfirmasi')
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('admin.pending-payments', [
            'pendingPayments' => $pendingPayments,
            'paymentStatusOptions' => self::PAYMENT_STATUS_OPTIONS,
        ]);
    }

    public function rekapSelesai()
    {
        $pemesananSelesai = Pemesanan::with(['user', 'wisma'])
            ->where('status', 'check_out')
            ->orderByDesc('check_out_at')
            ->get();

        $totalMalam = $pemesananSelesai->sum('lama_menginap');
        $totalKamar = $pemesananSelesai->sum('jumlah_kamar');
        $totalPendapatan = $pemesananSelesai->sum(function ($item) {
            return $item->total_biaya ?? 0;
        });

        $pdf = Pdf::loadView('admin.reports.completed-bookings', [
            'pemesananSelesai' => $pemesananSelesai,
            'totalMalam' => $totalMalam,
            'totalKamar' => $totalKamar,
            'totalPendapatan' => $totalPendapatan,
            'generatedAt' => now(),
        ])->setPaper('A4', 'landscape');

        return $pdf->download('rekap-pemesanan-selesai-' . now()->format('Ymd_His') . '.pdf');
    }

    public function show($id)
    {
        $pemesanan = Pemesanan::with(['user', 'wisma'])->findOrFail($id);

        return view('admin.pemesanan-show', [
            'pemesanan'            => $pemesanan,
            'statusOptions'        => self::STATUS_OPTIONS,
            'paymentStatusOptions' => self::PAYMENT_STATUS_OPTIONS,
        ]);
    }

    public function ubahStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(self::STATUS_OPTIONS)),
        ]);

        $pemesanan = Pemesanan::with('user')->findOrFail($id);
        $pemesanan->loadMissing('wisma');
        $oldStatus = $pemesanan->status;
        $pemesanan->status = $request->status;

        $this->applyStatusTransition($pemesanan, $request->status, true);

        $pemesanan->save();

        if ($oldStatus !== $pemesanan->status && $pemesanan->user && $pemesanan->user->email) {
            $pemesanan->user->notify(new BookingStatusUpdatedNotification($pemesanan, $oldStatus));
        }

        return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui.');
    }

    public function updateDetail(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(self::STATUS_OPTIONS)),
            'catatan_admin' => 'nullable|string',
            'check_in_at' => 'nullable|date',
            'check_out_at' => 'nullable|date|after_or_equal:check_in_at',
            'metode_pembayaran' => 'nullable|string|max:50',
            'status_pembayaran' => 'required|in:' . implode(',', array_keys(self::PAYMENT_STATUS_OPTIONS)),
            'total_biaya' => 'nullable|numeric|min:0',
        ]);

        $pemesanan = Pemesanan::with('user')->findOrFail($id);
        $pemesanan->loadMissing('wisma');
        $oldStatus = $pemesanan->status;
        $oldPaymentStatus = $pemesanan->status_pembayaran;
        $pemesanan->status = $validated['status'];
        $pemesanan->catatan_admin = $validated['catatan_admin'] ?? null;
        if ($pemesanan->metode_pembayaran === null && $request->filled('metode_pembayaran')) {
            $pemesanan->metode_pembayaran = $validated['metode_pembayaran'];
        }
        $pemesanan->status_pembayaran = $validated['status_pembayaran'];
        $pemesanan->total_biaya = $validated['total_biaya'] ?? null;

        if ($request->has('check_in_at') && $pemesanan->check_in_at === null) {
            $pemesanan->check_in_at = $request->filled('check_in_at')
                ? Carbon::parse($validated['check_in_at'])
                : null;
        }

        if ($request->has('check_out_at') && $pemesanan->check_out_at === null) {
            $pemesanan->check_out_at = $request->filled('check_out_at')
                ? Carbon::parse($validated['check_out_at'])
                : null;
        }

        $pemesanan->pembayaran_dikonfirmasi_at = $validated['status_pembayaran'] === 'selesai'
            ? ($pemesanan->pembayaran_dikonfirmasi_at ?? now())
            : null;

        $autoFillTimestamps = !($request->filled('check_in_at') || $request->filled('check_out_at'));
        $this->applyStatusTransition($pemesanan, $validated['status'], $autoFillTimestamps);

        $pemesanan->save();

        if ($oldStatus !== $pemesanan->status && $pemesanan->user && $pemesanan->user->email) {
            $pemesanan->user->notify(new BookingStatusUpdatedNotification($pemesanan, $oldStatus));
        }

        if ($oldPaymentStatus !== $pemesanan->status_pembayaran
            && $pemesanan->status_pembayaran === 'selesai'
            && $pemesanan->user
            && $pemesanan->user->email) {
            $pemesanan->user->notify(new PaymentConfirmedNotification($pemesanan));
        }

        return redirect()
            ->route('admin.pemesanan.show', $pemesanan->id_pemesanan)
            ->with('success', 'Detail pemesanan diperbarui.');
    }

    public function konfirmasiPembayaran(Request $request, $id)
    {
        $validated = $request->validate([
            'metode_pembayaran' => 'nullable|string|max:50',
            'total_biaya' => 'nullable|numeric|min:0',
            'tandai_check_out' => 'nullable|boolean',
        ]);

        $pemesanan = Pemesanan::with('user')->findOrFail($id);
        $pemesanan->loadMissing('wisma');
        $oldPaymentStatus = $pemesanan->status_pembayaran;
        $pemesanan->status_pembayaran = 'selesai';
        if ($pemesanan->metode_pembayaran === null && $request->filled('metode_pembayaran')) {
            $pemesanan->metode_pembayaran = $validated['metode_pembayaran'];
        }
        $pemesanan->total_biaya = $validated['total_biaya'] ?? $pemesanan->total_biaya;
        $pemesanan->pembayaran_dikonfirmasi_at = now();

        if (! empty($validated['tandai_check_out'])) {
            $pemesanan->status = 'check_out';
            $this->applyStatusTransition($pemesanan, 'check_out', true);
        }

        $pemesanan->save();

        if ($oldPaymentStatus !== 'selesai'
            && $pemesanan->user
            && $pemesanan->user->email) {
            $pemesanan->user->notify(new PaymentConfirmedNotification($pemesanan));
        }

        return redirect()
            ->route('admin.pemesanan.show', $pemesanan->id_pemesanan)
            ->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function downloadBukti($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if (! $pemesanan->bukti_pembayaran_path || ! Storage::disk('public')->exists($pemesanan->bukti_pembayaran_path)) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }

        return Storage::disk('public')->download($pemesanan->bukti_pembayaran_path);
    }

    public function kuitansi($id)
    {
        $pemesanan = Pemesanan::with(['user', 'wisma'])->findOrFail($id);

        if ($pemesanan->status_pembayaran !== 'selesai') {
            abort(403, 'Pembayaran belum dikonfirmasi.');
        }

        $receiptNumber = 'KWT-' . str_pad((string) $pemesanan->id_pemesanan, 5, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('admin.pemesanan-receipt', [
            'pemesanan' => $pemesanan,
            'receiptNumber' => $receiptNumber,
        ])->setPaper('A4');

        return $pdf->download("kuitansi-{$receiptNumber}.pdf");
    }

    public function batalkan($id)
    {
        $pemesanan = Pemesanan::with(['user', 'wisma'])->findOrFail($id);

        if ($pemesanan->user && $pemesanan->user->email) {
            $pemesanan->user->notify(new BookingCancelledNotification($pemesanan));
        }

        $pemesanan->delete();

        return redirect()->back()->with('success', 'Pemesanan berhasil dibatalkan.');
    }

    private function applyStatusTransition(Pemesanan $pemesanan, string $status, bool $autoFillTimestamps = true): void
    {
        switch ($status) {
            case 'reservasi':
            case 'diproses':
                // Biarkan tanggal yang ditentukan pengguna tetap tersimpan.
                break;
            case 'check_in':
                if ($autoFillTimestamps && ! $pemesanan->check_in_at) {
                    $pemesanan->check_in_at = now();
                }
                break;
            case 'check_out':
                if ($autoFillTimestamps && ! $pemesanan->check_in_at) {
                    $pemesanan->check_in_at = now();
                }
                if ($autoFillTimestamps && ! $pemesanan->check_out_at) {
                    $pemesanan->check_out_at = now();
                }
                break;
        }
    }
}
