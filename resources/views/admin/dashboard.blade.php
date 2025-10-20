<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin - Ringkasan
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-8">
        @php
            $cardStyles = [
                'reservasi' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                'diproses'  => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                'check_in'  => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
                'check_out' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
            ];

            $paymentStyles = [
                'belum' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800'],
                'menunggu_konfirmasi' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800'],
                'selesai' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
            ];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white p-4 rounded shadow text-center">
                <p class="text-sm text-gray-500">Total Pemesanan</p>
                <h3 class="text-2xl font-bold">{{ $statistik['total'] }}</h3>
            </div>

            @foreach($statusLabels as $value => $label)
                @php($styles = $cardStyles[$value] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'])
                <div class="{{ $styles['bg'] }} p-4 rounded shadow text-center">
                    <p class="text-sm {{ $styles['text'] }}">{{ $label }}</p>
                    <h3 class="text-2xl font-bold {{ $styles['text'] }}">{{ $statistik[$value] ?? 0 }}</h3>
                </div>
            @endforeach
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Status Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($pembayaranLabels as $value => $label)
                    @php($styles = $paymentStyles[$value] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'])
                    <div class="{{ $styles['bg'] }} p-4 rounded shadow text-center">
                        <p class="text-sm {{ $styles['text'] }}">{{ $label }}</p>
                        <h3 class="text-xl font-bold {{ $styles['text'] }}">{{ $statistikPembayaran[$value] ?? 0 }}</h3>
                    </div>
                @endforeach
            </div>
        </div>

        <div>
            <a href="{{ route('admin.pemesanan') }}" class="text-blue-600 underline text-sm">
                &raquo; Lihat semua data pemesanan
            </a>
        </div>
    </div>
</x-app-layout>
