<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pemesanan;
use App\Models\Wisma;
use App\Models\PaymentAccount;

class PemesananController extends Controller
{
    private const STATUS_LABELS = [
        'reservasi' => 'Reservasi',
        'diproses' => 'Diproses',
        'check_in' => 'Check In',
        'check_out' => 'Check Out',
    ];

    private const PAYMENT_STATUS_LABELS = [
        'belum' => 'Belum Dibayar',
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'selesai' => 'Pembayaran Selesai',
    ];

    private const STATUS_GUIDANCE = [
        'reservasi' => 'Menunggu evaluasi dan konfirmasi dari admin.',
        'diproses' => 'Permintaan Anda sedang diproses oleh admin.',
        'check_in' => 'Silakan menuju front office dan siapkan bukti pembayaran.',
        'check_out' => 'Terima kasih, pemesanan ini telah selesai.',
    ];

    public function index()
    {
        $user = Auth::user();
        $pemesanan = $user->pemesanan()->with('wisma')->latest()->get();

        return view('pemesanan.index', [
            'pemesanan' => $pemesanan,
            'statusLabels' => self::STATUS_LABELS,
            'paymentStatusLabels' => self::PAYMENT_STATUS_LABELS,
            'statusGuidance' => self::STATUS_GUIDANCE,
            'progressSteps' => array_keys(self::STATUS_LABELS),
        ]);
    }

    public function create()
    {
        $daftarWisma = Wisma::orderBy('nama_wisma')->get();
        $paymentAccounts = PaymentAccount::where('aktif', true)->orderBy('nama_bank')->get();

        return view('pemesanan.create', compact('daftarWisma', 'paymentAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_wisma' => 'required|exists:wisma,id_wisma',
            'nama_kegiatan' => 'required|string|max:100',
            'lama_menginap' => 'required|integer|min:1',
            'jumlah_kamar' => 'required|integer|min:1',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        $statusAktif = ['reservasi', 'diproses', 'check_in'];

        $adaPemesanan = Pemesanan::where('id_user', Auth::id())
            ->whereIn('status', $statusAktif)
            ->exists();

        if ($adaPemesanan) {
            return redirect()->back()
                ->with('error', 'Kamu masih memiliki pemesanan yang aktif.')
                ->withInput();
        }

        Pemesanan::create([
            'id_user' => Auth::id(),
            'id_wisma' => $validated['id_wisma'],
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'lama_menginap' => $validated['lama_menginap'],
            'jumlah_kamar' => $validated['jumlah_kamar'],
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
            'status' => 'reservasi',
        ]);

        return redirect()->route('pemesanan.index')
            ->with('success', 'Pemesanan berhasil dibuat!');
    }

    public function show(Pemesanan $pemesanan)
    {
        if ($pemesanan->id_user !== Auth::id()) {
            abort(403);
        }

        $pemesanan->load('wisma');

        $progressSteps = array_keys(self::STATUS_LABELS);
        $currentIndex = array_search($pemesanan->status, $progressSteps, true);
        $progressPercent = ($currentIndex !== false && count($progressSteps) > 1)
            ? round(($currentIndex / (count($progressSteps) - 1)) * 100)
            : 0;

        $paymentAccounts = PaymentAccount::where('aktif', true)->orderBy('nama_bank')->get();

        return view('pemesanan.show', [
            'pemesanan' => $pemesanan,
            'statusLabels' => self::STATUS_LABELS,
            'paymentStatusLabels' => self::PAYMENT_STATUS_LABELS,
            'statusGuidance' => self::STATUS_GUIDANCE,
            'progressSteps' => $progressSteps,
            'currentIndex' => $currentIndex === false ? null : $currentIndex,
            'progressPercent' => $progressPercent,
            'paymentAccounts' => $paymentAccounts,
        ]);
    }

    public function uploadBukti(Request $request, Pemesanan $pemesanan)
    {
        if ($pemesanan->id_user !== Auth::id()) {
            abort(403);
        }

        if (! in_array($pemesanan->status, ['diproses', 'check_in', 'check_out'], true)) {
            return redirect()->back()->with('error', 'Bukti pembayaran hanya dapat diunggah setelah pemesanan diproses.');
        }

        $validated = $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($pemesanan->bukti_pembayaran_path) {
            Storage::disk('public')->delete($pemesanan->bukti_pembayaran_path);
        }

        $path = $request->file('bukti_pembayaran')->store('payments', 'public');

        $pemesanan->update([
            'bukti_pembayaran_path' => $path,
            'status_pembayaran' => 'menunggu_konfirmasi',
            'pembayaran_dikonfirmasi_at' => null,
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu konfirmasi admin.');
    }

    public function downloadBukti(Pemesanan $pemesanan)
    {
        if ($pemesanan->id_user !== Auth::id()) {
            abort(403);
        }

        if (! $pemesanan->bukti_pembayaran_path || ! Storage::disk('public')->exists($pemesanan->bukti_pembayaran_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($pemesanan->bukti_pembayaran_path);
    }

    public function downloadKuitansi(Pemesanan $pemesanan)
    {
        if ($pemesanan->id_user !== Auth::id()) {
            abort(403);
        }

        if ($pemesanan->status_pembayaran !== 'selesai') {
            return redirect()->back()->with('error', 'Kuitansi hanya tersedia setelah pembayaran dikonfirmasi.');
        }

        $pemesanan->loadMissing(['wisma', 'user']);

        $receiptNumber = 'KWT-' . str_pad((string) $pemesanan->id_pemesanan, 5, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('admin.pemesanan-receipt', [
            'pemesanan' => $pemesanan,
            'receiptNumber' => $receiptNumber,
        ])->setPaper('A4');

        return $pdf->download("kuitansi-{$receiptNumber}.pdf");
    }

    public function destroy($id)
    {
        // TODO: implementasi pembatalan jika dibutuhkan.
    }
}
