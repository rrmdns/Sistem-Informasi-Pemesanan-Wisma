<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Pemesanan;
use App\Models\Wisma;
use App\Models\PaymentAccount;
use App\Notifications\BookingCreatedNotification;
use App\Support\PemesananDictionary;

class PemesananController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pemesanan = $user->pemesanan()->with('wisma')->latest()->get();

        return view('pemesanan.index', [
            'pemesanan' => $pemesanan,
            'statusLabels' => PemesananDictionary::statusLabels(),
            'paymentStatusLabels' => PemesananDictionary::paymentStatusLabels(),
            'statusGuidance' => PemesananDictionary::statusGuidance(),
            'progressSteps' => array_keys(PemesananDictionary::statusLabels()),
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
            'metode_pembayaran' => 'required|in:transfer,tunai,kartu',
            'jumlah_kamar' => 'required|integer|min:1',
            'penanggung_jawab' => 'nullable|string|max:100',
            'check_in_at' => 'required|date|after_or_equal:today',
            'check_out_at' => 'required|date|after:check_in_at',
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

        $pemesanan = Pemesanan::create([
            'id_user' => Auth::id(),
            'id_wisma' => $validated['id_wisma'],
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'lama_menginap' => max(
                1,
                Carbon::parse($validated['check_in_at'])->startOfDay()->diffInDays(
                    Carbon::parse($validated['check_out_at'])->startOfDay()
                )
            ),
            'jumlah_kamar' => $validated['jumlah_kamar'],
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'check_in_at' => Carbon::parse($validated['check_in_at'])->setTime(14, 0, 0),
            'check_out_at' => Carbon::parse($validated['check_out_at'])->setTime(12, 0, 0),
            'status' => 'reservasi',
        ]);

        $pemesanan->loadMissing('wisma');

        if (Auth::user()->email) {
            Auth::user()->notify(new BookingCreatedNotification($pemesanan));
        }

        return redirect()->route('pemesanan.index')
            ->with('success', 'Pemesanan berhasil dibuat!');
    }

    public function show(Pemesanan $pemesanan)
    {
        if ($pemesanan->id_user !== Auth::id()) {
            abort(403);
        }

        $pemesanan->load('wisma');

        $progressSteps = array_keys(PemesananDictionary::statusLabels());
        $currentIndex = array_search($pemesanan->status, $progressSteps, true);
        $progressPercent = ($currentIndex !== false && count($progressSteps) > 1)
            ? round(($currentIndex / (count($progressSteps) - 1)) * 100)
            : 0;

        $paymentAccounts = PaymentAccount::where('aktif', true)->orderBy('nama_bank')->get();

        return view('pemesanan.show', [
            'pemesanan' => $pemesanan,
            'statusLabels' => PemesananDictionary::statusLabels(),
            'paymentStatusLabels' => PemesananDictionary::paymentStatusLabels(),
            'statusGuidance' => PemesananDictionary::statusGuidance(),
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

        if ($pemesanan->status !== 'check_in') {
            return redirect()->back()->with('error', 'Bukti pembayaran hanya dapat diunggah saat status pemesanan sudah check in.');
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
