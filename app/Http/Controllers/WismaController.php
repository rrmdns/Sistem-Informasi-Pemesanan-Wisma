<?php

namespace App\Http\Controllers;

use App\Models\Wisma;
use Illuminate\Http\Request;

class WismaController extends Controller
{
    public function index(Request $request)
    {
        $allWisma = Wisma::orderBy('id_wisma')->get();
        $selectedId = $request->query('wisma');

        $visibleWisma = $selectedId
            ? $allWisma->where('id_wisma', (int) $selectedId)
            : $allWisma;

        return view('wisma.index', [
            'allWisma' => $allWisma,
            'visibleWisma' => $visibleWisma,
            'selectedWismaId' => $selectedId ? (int) $selectedId : null,
        ]);
    }

    public function show(Wisma $wisma)
    {
        // Layout konten yang bisa diganti dengan data dari database saat tersedia.
        $heroImage = 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1600&q=80';
        $gallery = [
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=1200&q=80',
        ];

        $facilities = [
            'Wi-Fi Cepat',
            'AC & Ventilasi Baik',
            'Kamar Mandi Dalam',
            'Ruang Meeting',
            'Area Parkir Luas',
            'Sarapan Tersedia',
        ];

        $description = 'Deskripsi singkat wisma. Ganti dengan informasi asli seperti jumlah kamar, tipe kamar, kapasitas, dan kebijakan. Tampilkan highlight unik dari wisma untuk membantu user memilih.';

        return view('wisma.show', [
            'wisma' => $wisma,
            'heroImage' => $heroImage,
            'gallery' => $gallery,
            'facilities' => $facilities,
            'description' => $description,
        ]);
    }
}
