<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin - Ringkasan
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
        <!-- Statistik Ringkasan -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white p-4 rounded shadow text-center">
                <p class="text-sm text-gray-500">Total</p>
                <h3 class="text-xl font-bold">{{ $statistik['total'] }}</h3>
            </div>
            <div class="bg-blue-100 p-4 rounded shadow text-center">
                <p class="text-sm text-blue-800">Reservasi</p>
                <h3 class="text-xl font-bold text-blue-800">{{ $statistik['reservasi'] }}</h3>
            </div>
            <div class="bg-yellow-100 p-4 rounded shadow text-center">
                <p class="text-sm text-yellow-800">Diproses</p>
                <h3 class="text-xl font-bold text-yellow-800">{{ $statistik['diproses'] }}</h3>
            </div>
            <div class="bg-purple-100 p-4 rounded shadow text-center">
                <p class="text-sm text-purple-800">Check-in</p>
                <h3 class="text-xl font-bold text-purple-800">{{ $statistik['checkin'] }}</h3>
            </div>
            <div class="bg-green-100 p-4 rounded shadow text-center">
                <p class="text-sm text-green-800">Selesai</p>
                <h3 class="text-xl font-bold text-green-800">{{ $statistik['selesai'] }}</h3>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.pemesanan') }}" class="text-blue-600 underline text-sm">
                ➤ Lihat semua data pemesanan
            </a>
        </div>
    </div>
</x-app-layout>
