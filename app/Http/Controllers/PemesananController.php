<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemesanan;
use App\Models\Wisma; 

class PemesananController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $pemesanan = $user->pemesanan()->latest()->get();
        return view('pemesanan.index', compact('pemesanan'));
    }

    public function create()
    {
        $daftarWisma = Wisma::orderBy('nama_wisma')->get();
        return view('pemesanan.create', compact('daftarWisma'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_wisma'         => 'required|exists:wisma,id_wisma',
            'nama_kegiatan' => 'required|string|max:100',
            'lama_menginap' => 'required|integer|min:1',
            'jumlah_kamar' => 'required|integer|min:1',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        // Cegah double pemesanan aktif
        $adaPemesanan = Pemesanan::where('id_user', Auth::id())
            ->where('status', '!=', 'selesai')
            ->exists();

        if ($adaPemesanan) {
           return redirect()->back()
           ->with('error', 'Kamu masih memiliki pemesanan yang aktif.')
           ->withInput();
        }

        Pemesanan::create([
            'id_user' => Auth::id(),
            'id_wisma'         => $validated['id_wisma'],
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'lama_menginap' => $validated['lama_menginap'],
            'jumlah_kamar' => $validated['jumlah_kamar'],
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
            'status' => 'reservasi',
        ]);

        return redirect()->route('pemesanan.index')
            ->with('success', 'Pemesanan berhasil dibuat!');
        }

    
    public function destroy($id)
    {
        #$pemesanan = Pemesanan::where('id_pemesanan', $id)
        #->where('id_user', Auth::id())
        #->firstOrFail();
        
        #$pemesanan->delete();
        
        #return redirect()->route('pemesanan.index')->with('success', 'Pemesanan berhasil dibatalkan.');
    }

}