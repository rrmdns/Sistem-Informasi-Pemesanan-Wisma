<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pemesanan Saya
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Halo, {{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-600">Kelola dan pantau status permintaan wisma Anda di sini.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pemesanan.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">+ Buat Pemesanan</a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @php
            $activeStatuses = ['reservasi', 'diproses', 'check_in'];
            $activeBooking = $pemesanan->first(function ($item) use ($activeStatuses) {
                return in_array($item->status, $activeStatuses, true);
            });
        @endphp

        @if($activeBooking)
            <div class="bg-indigo-50 border border-indigo-100 rounded p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="text-sm text-indigo-600 font-semibold uppercase">Pemesanan Aktif</p>
                        <p class="text-lg font-semibold text-indigo-900">{{ $activeBooking->wisma->nama_wisma ?? '-' }}</p>
                        <p class="text-sm text-indigo-700">Status: {{ $statusLabels[$activeBooking->status] ?? $activeBooking->status }}</p>
                    </div>
                    <a href="{{ route('pemesanan.show', $activeBooking) }}" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">Lihat Detail</a>
                </div>
                <p class="text-sm text-indigo-800 mt-3">{{ $statusGuidance[$activeBooking->status] ?? '' }}</p>
            </div>
        @endif

        @if($pemesanan->isEmpty())
            <div class="text-gray-600">Belum ada pemesanan. Mulai dengan menekan tombol "Buat Pemesanan" di atas.</div>
        @else
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">Wisma</th>
                            <th class="px-4 py-3 text-left">Kegiatan</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Pembayaran</th>
                            <th class="px-4 py-3 text-left">Diajukan</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pemesanan as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $item->wisma->nama_wisma ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->lama_menginap }} hari • {{ $item->jumlah_kamar }} kamar</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $item->nama_kegiatan }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                        {{ $statusLabels[$item->status] ?? $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        {{ $paymentStatusLabels[$item->status_pembayaran] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ optional($item->created_at)->format('d-m-Y') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('pemesanan.show', $item) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
