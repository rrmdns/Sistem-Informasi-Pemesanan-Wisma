<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Selamat Datang, {{ Auth::user()->name }} 👋
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto space-y-8">
        @php
            $cardStyles = [
                'reservasi' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700'],
                'diproses'  => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-700'],
                'check_in'  => ['bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'text' => 'text-violet-700'],
                'check_out' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700'],
            ];

            $paymentStyles = [
                'belum' => ['bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-700'],
                'menunggu_konfirmasi' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700'],
                'selesai' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-700'],
            ];

            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];

            $selectedMonth = (int) request('month', $defaultMonth ?? now()->month);
            $selectedYear = (int) request('year', $defaultYear ?? now()->year);
            $yearOptions = $availableYears ?? range(now()->year, now()->year + 1);
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm text-gray-500">Ringkasan aktivitas</p>
                <h1 class="text-2xl font-bold text-gray-900 mt-1">Kelola reservasi Wisma dengan mudah</h1>
                <p class="text-sm text-gray-600 mt-2">Lihat status terbaru pemesanan, konfirmasi pembayaran, dan pantau jadwal check-in secara real-time.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <a href="{{ route('admin.pemesanan') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-indigo-700">
                    Kelola Pemesanan
                </a>
                <a href="{{ route('admin.pembayaran.pending') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-indigo-200 text-indigo-600 text-sm font-semibold rounded-lg hover:bg-indigo-50">
                    Lihat Pembayaran Pending
                </a>
                <a href="{{ route('admin.ekspor.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-emerald-200 text-emerald-600 text-sm font-semibold rounded-lg hover:bg-emerald-50">
                    Dashboard Ekspor
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <p class="text-xs uppercase tracking-wide text-gray-500">Total Pemesanan</p>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $statistik['total'] }}</div>
                <p class="mt-1 text-xs text-gray-500">Seluruh data pemesanan yang tercatat</p>
            </div>

            @foreach($statusLabels as $value => $label)
                @php($styles = $cardStyles[$value] ?? ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700'])
                <div class="{{ $styles['bg'] }} p-5 rounded-xl shadow-sm border {{ $styles['border'] }}">
                    <p class="text-xs uppercase tracking-wide {{ $styles['text'] }}">{{ $label }}</p>
                    <div class="mt-2 text-2xl font-semibold {{ $styles['text'] }}">{{ $statistik[$value] ?? 0 }}</div>
                    <p class="mt-1 text-xs text-gray-500">Pemesanan berstatus {{ strtolower($label) }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($pembayaranLabels as $value => $label)
                @php($styles = $paymentStyles[$value] ?? ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700'])
                <div class="{{ $styles['bg'] }} p-5 rounded-xl shadow-sm border {{ $styles['border'] }}">
                    <div class="flex items-center justify-between">
                        <p class="text-xs uppercase tracking-wide {{ $styles['text'] }}">{{ $label }}</p>
                        @if($value === 'menunggu_konfirmasi' && ($statistikPembayaran[$value] ?? 0) > 0)
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-semibold bg-amber-200 text-amber-800 rounded-full">
                                Butuh perhatian
                            </span>
                        @endif
                    </div>
                    <div class="mt-2 text-2xl font-semibold {{ $styles['text'] }}">{{ $statistikPembayaran[$value] ?? 0 }}</div>
                    <p class="mt-1 text-xs text-gray-500">Status pembayaran {{ strtolower($label) }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Check-In Mendatang</h3>
                    <span class="text-xs text-gray-500">{{ $upcomingCheckins->count() }} Jadwal</span>
                </div>
                @if($upcomingCheckins->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada jadwal check-in terdekat.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($upcomingCheckins as $item)
                            <li class="border border-gray-100 rounded-lg p-3 hover:border-indigo-200 transition">
                                <div class="flex items-center justify-between text-sm">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $item->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->wisma->nama_wisma ?? '-' }}</p>
                                    </div>
                                    <span class="text-xs font-semibold text-indigo-600">
                                        {{ optional($item->check_in_at)->format('d M Y') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Metode: {{ $item->metode_pembayaran ? \Illuminate\Support\Str::title(str_replace('_',' ', $item->metode_pembayaran)) : '-' }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Pembayaran Menunggu Konfirmasi</h3>
                    <span class="text-xs text-gray-500">{{ $pendingPayments->count() }} Data</span>
                </div>
                @if($pendingPayments->isEmpty())
                    <p class="text-sm text-gray-500">Tidak ada pembayaran yang menunggu konfirmasi.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($pendingPayments as $item)
                            <li class="border border-amber-200 bg-amber-50 rounded-lg p-3">
                                <div class="flex items-center justify-between text-sm">
                                    <div>
                                        <p class="font-semibold text-amber-900">{{ $item->user->name ?? '-' }}</p>
                                        <p class="text-xs text-amber-700">{{ $item->wisma->nama_wisma ?? '-' }}</p>
                                    </div>
                                    <a href="{{ route('admin.pemesanan.show', $item->id_pemesanan) }}" class="text-xs text-amber-800 font-semibold underline">
                                        Tinjau
                                    </a>
                                </div>
                                <p class="mt-1 text-xs text-amber-700">
                                    Diunggah: {{ optional($item->updated_at)->format('d M Y H:i') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Aktivitas Pemesanan Terbaru</h3>
                <a href="{{ route('admin.pemesanan') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                    Lihat semua
                </a>
            </div>
            @if($recentBookings->isEmpty())
                <p class="text-sm text-gray-500">Belum ada aktivitas terbaru.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-xs uppercase tracking-wide text-gray-500 border-b">
                                <th class="px-4 py-2">Pemesan</th>
                                <th class="px-4 py-2">Wisma</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Pembayaran</th>
                                <th class="px-4 py-2">Dibuat</th>
                                <th class="px-4 py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentBookings as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2">
                                        <p class="font-semibold text-gray-800">{{ $item->user->name ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->user->departemen ?? 'Tidak ada departemen' }}</p>
                                    </td>
                                    <td class="px-4 py-2">{{ $item->wisma->nama_wisma ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                            {{ $statusLabels[$item->status] ?? \Illuminate\Support\Str::title(str_replace('_',' ', $item->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                            @if($item->status_pembayaran === 'selesai') bg-emerald-100 text-emerald-700
                                            @elseif($item->status_pembayaran === 'menunggu_konfirmasi') bg-amber-100 text-amber-700
                                            @else bg-gray-100 text-gray-600 @endif">
                                            {{ $pembayaranLabels[$item->status_pembayaran] ?? \Illuminate\Support\Str::title(str_replace('_',' ', $item->status_pembayaran)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">{{ optional($item->created_at)->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <a href="{{ route('admin.pemesanan.show', $item->id_pemesanan) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                                            Detail
                                        </a>
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
