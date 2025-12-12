<?php

namespace App\Http\Controllers;

use App\Models\Wisma;
use Illuminate\Http\Request;

class WismaController extends Controller
{
    public function index(Request $request)
    {
        $allWisma = Wisma::orderBy('nama_wisma')->get();
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
}
