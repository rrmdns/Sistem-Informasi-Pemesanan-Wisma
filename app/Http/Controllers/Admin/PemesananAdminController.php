<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class PemesananAdminController extends Controller
{
    // Halaman Statistik Dashboard Admin (RINGKASAN)
    public function overview()
    {
        $semuaPemesanan = Pemesanan::all();

        $statistik = [
            'total'     => $semuaPemesanan->count(),
            'reservasi' => $semuaPemesanan->where('status', 'reservasi')->count(),
            'diproses'  => $semuaPemesanan->where('status', 'diproses')->count(),
            'checkin'   => $semuaPemesanan->where('status', 'check-in')->count(),
            'selesai'   => $semuaPemesanan->where('status', 'selesai')->count(),
        ];

        return view('admin.dashboard', compact('statistik'));
    }

    // Halaman Daftar Semua Pemesanan
    public function index()
    {
        $semuaPemesanan = Pemesanan::with(['user','wisma'])->orderByDesc('created_at')->get();
        return view('admin.pemesanan', compact('semuaPemesanan'));
    }

    // Ubah Status Pemesanan
    public function ubahStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:reservasi,diproses,check-in,selesai',
        ]);

        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->status = $request->status;
        $pemesanan->save();

        return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui.');
    }

    // Batalkan Pesanan
    public function batalkan($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->delete();

        return redirect()->back()->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}
