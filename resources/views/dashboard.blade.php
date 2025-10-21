<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto space-y-7">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Selamat datang kembali,</p>
                <h1 class="text-2xl font-bold text-indigo-600">{{ Auth::user()->name }}</h1>
                <p class="text-sm text-gray-600">Anda login sebagai <span class="font-semibold">{{ Auth::user()->jenis_user }}</span>{{ Auth::user()->departemen ? ' Departemen ' . Auth::user()->departemen : '' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pemesanan.create') }}" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">+ Buat Pemesanan</a>
                <a href="{{ route('pemesanan.index') }}" class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Pemesanan Saya</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white p-5 rounded shadow text-center">
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total Pemesanan</p>
                <p class="text-2xl font-bold text-gray-800">{{ $statistik['total'] ?? 0 }}</p>
            </div>
            @foreach($statusLabels as $status => $label)
                <div class="bg-indigo-50 p-5 rounded shadow text-center">
                    <p class="text-xs text-indigo-700 uppercase tracking-wide">{{ $label }}</p>
                    <p class="text-2xl font-bold text-indigo-900">{{ $statistik[$status] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($paymentStatusLabels as $status => $label)
                <div class="bg-white p-5 rounded shadow">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $statistikPembayaran[$status] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded shadow p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Pemesanan Aktif</h3>
                    @if($activeBooking)
                        <span class="px-3 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700 font-semibold">
                            {{ $statusLabels[$activeBooking->status] ?? $activeBooking->status }}
                        </span>
                    @endif
                </div>

                @if($activeBooking)
                    @php
                        $currentIndex = array_search($activeBooking->status, $progressSteps, true);
                        $progressPercent = ($currentIndex !== false && count($progressSteps) > 1)
                            ? round(($currentIndex / (count($progressSteps) - 1)) * 100)
                            : 0;
                    @endphp

                    <div>
                        <h4 class="text-base font-semibold text-gray-900">{{ $activeBooking->wisma->nama_wisma ?? 'Wisma tidak tersedia' }}</h4>
                        <p class="text-sm text-gray-600">{{ $activeBooking->nama_kegiatan }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-600 mb-2 uppercase">Progress</p>
                        <div class="h-2 bg-gray-200 rounded">
                            <div class="h-full bg-indigo-500 rounded" style="width: {{ $progressPercent }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            @foreach($progressSteps as $index => $step)
                                <div class="flex-1 text-center {{ $index === $currentIndex ? 'text-indigo-600 font-semibold' : '' }}">
                                    {{ $statusLabels[$step] ?? $step }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-indigo-50 border border-indigo-100 rounded p-4 text-sm text-indigo-700">
                        {{ $statusGuidance[$activeBooking->status] ?? 'Ikuti arahan petugas untuk langkah berikutnya.' }}
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('pemesanan.show', $activeBooking) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Lihat detail&nbsp;&rarr;</a>
                    </div>
                @else
                    <div class="text-sm text-gray-600">
                        Belum ada pemesanan aktif. <a href="{{ route('pemesanan.create') }}" class="text-indigo-600 font-semibold">Mulai buat pemesanan</a> untuk reservasi berikutnya.
                    </div>
                @endif
            </div>

            <div class="bg-white rounded shadow p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
                    <a href="{{ route('pemesanan.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800">Lihat semua</a>
                </div>

                @if($recentBookings->isEmpty())
                    <p class="text-sm text-gray-600">Belum ada riwayat pemesanan.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($recentBookings as $booking)
                            <li class="border border-gray-100 rounded p-3 hover:border-indigo-200 transition">
                                <div class="flex justify-between text-sm">
                                    <p class="font-semibold text-gray-800">{{ $booking->wisma->nama_wisma ?? '-' }}</p>
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">{{ optional($booking->created_at)->format('d/m') }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $booking->nama_kegiatan }}</p>
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                                    </span>
                                    <a href="{{ route('pemesanan.show', $booking) }}" class="text-xs text-indigo-600 hover:text-indigo-800">Detail</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="bg-white rounded shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Rekening Pembayaran Resmi</h3>
            @if($paymentAccounts->isEmpty())
                <p class="text-sm text-gray-600">Belum ada rekening yang tersedia. Silakan hubungi admin.</p>
            @else
                <ul class="space-y-3 text-sm text-gray-700">
                    @foreach($paymentAccounts as $account)
                        <li class="border border-gray-100 rounded p-3">
                            <p class="font-semibold text-gray-900">{{ $account->nama_bank }} &mdash; {{ $account->nomor_rekening }}</p>
                            <p class="text-xs text-gray-600">a.n. {{ $account->atas_nama }}</p>
                            @if($account->instruksi)
                                <p class="text-xs text-gray-500 mt-1">{{ $account->instruksi }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white rounded shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tips Pemesanan</h3>
            <ul class="list-disc pl-5 text-sm text-gray-600 space-y-2">
                <li>Pastikan data kegiatan dan kebutuhan kamar lengkap saat mengajukan pemesanan.</li>
                <li>Setelah menerima konfirmasi admin, siapkan dokumen yang diperlukan untuk check-in.</li>
                <li>Unggah bukti pembayaran atau lapor ke front office segera setelah check-out.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
