<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Semua Pemesanan
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto space-y-6">
        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Daftar Pemesanan</h3>
                    <p class="text-sm text-gray-500">Pantau dan kelola seluruh pemesanan wisma dari satu tempat.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 font-semibold">{{ $semuaPemesanan->count() }} total</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">{{ $semuaPemesanan->where('status_pembayaran', 'menunggu_konfirmasi')->count() }} pembayaran menunggu</span>
                </div>
            </div>

            @if($semuaPemesanan->isEmpty())
                <p class="text-sm text-gray-500">Belum ada data pemesanan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-xs uppercase tracking-wide text-gray-500 border-b bg-gray-50">
                                <th class="px-4 py-3">Pemesan</th>
                                <th class="px-4 py-3">Wisma</th>
                                <th class="px-4 py-3">Tanggal</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Pembayaran</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($semuaPemesanan as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 align-top">
                                        <p class="font-semibold text-gray-800">{{ $item->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->user->departemen ?? 'Tidak ada departemen' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $item->user->jenis_user ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <p class="font-medium text-gray-800">{{ $item->wisma->nama_wisma ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->nama_kegiatan }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $item->jumlah_kamar }} kamar • {{ $item->lama_menginap }} malam</p>
                                    </td>
                                    <td class="px-4 py-3 align-top text-sm text-gray-600">
                                        <div>Check-in: {{ optional($item->check_in_at)->format('d M Y') ?? '-' }}</div>
                                        <div>Check-out: {{ optional($item->check_out_at)->format('d M Y') ?? '-' }}</div>
                                        <div class="text-xs text-gray-400 mt-1">Dibuat: {{ optional($item->created_at)->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                            {{ $statusOptions[$item->status] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 align-top">
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                                @if($item->status_pembayaran === 'selesai') bg-emerald-100 text-emerald-700
                                                @elseif($item->status_pembayaran === 'menunggu_konfirmasi') bg-amber-100 text-amber-700
                                                @else bg-gray-100 text-gray-600 @endif">
                                                {{ $paymentStatusOptions[$item->status_pembayaran] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $item->status_pembayaran)) }}
                                            </span>
                                            <p class="text-xs text-gray-500">Metode: {{ $item->metode_pembayaran ? \Illuminate\Support\Str::title(str_replace('_',' ', $item->metode_pembayaran)) : '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 align-top text-center space-y-2">
                                        <a href="{{ route('admin.pemesanan.show', $item->id_pemesanan) }}"
                                           class="inline-flex items-center justify-center w-full px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded hover:bg-indigo-700">
                                            Lihat Detail
                                        </a>

                                        <form action="{{ route('admin.pemesanan.ubahStatus', $item->id_pemesanan) }}" method="POST" class="mb-1">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()" class="w-full border-gray-300 rounded text-xs">
                                                @foreach($statusOptions as $value => $label)
                                                    <option value="{{ $value }}" @selected($item->status === $value)>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>

                                        <form action="{{ route('admin.pemesanan.batalkan', $item->id_pemesanan) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="w-full px-2 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700">
                                                Batalkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
