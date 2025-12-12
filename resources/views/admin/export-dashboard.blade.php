<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Ekspor Laporan
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <p class="text-sm text-gray-500">Ekspor laporan pemesanan</p>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">Rekap Pemesanan Selesai (PDF)</h1>
                    <p class="text-sm text-gray-600 mt-2">Pilih periode check-out untuk mengunduh rekap pemesanan yang sudah selesai.</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">
                    &larr; Kembali ke Dashboard Admin
                </a>
            </div>

            <form method="GET" action="{{ route('admin.laporan.pemesananSelesai') }}" class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Bulan</label>
                    <select id="month" name="month" class="mt-1 block w-full rounded border-gray-300 text-sm">
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" @selected(request('month', $defaultMonth) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">Tahun</label>
                    <select id="year" name="year" class="mt-1 block w-full rounded border-gray-300 text-sm">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" @selected(request('year', $defaultYear) == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-emerald-700">
                        Unduh PDF Rekap
                    </button>
                </div>
            </form>

            <div class="mt-6 text-sm text-gray-600 space-y-1">
                <p>Catatan:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Rekap menggunakan data pemesanan dengan status <strong>check_out</strong>.</li>
                    <li>Periode memakai tanggal <em>check_out</em> dari awal sampai akhir bulan yang dipilih.</li>
                    <li>Jika tidak ada data di periode tersebut, PDF tetap dibuat dengan tabel kosong.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
