<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold text-indigo-600 mb-2">
                    Selamat Datang, {{ Auth::user()->name }}!
                </h1>
                <p class="text-gray-700">
                    Anda login sebagai <strong>{{ Auth::user()->jenis_user }}</strong><br>
                    {{ Auth::user()->departemen ? 'Departemen: ' . Auth::user()->departemen : '' }}
                </p>

                <div class="py-10 max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow rounded-lg p-6">
            <p class="text-gray-700 text-lg mb-4">Silakan pilih menu di bawah ini:</p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('pemesanan.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-center">
                    + Buat Pemesanan
                </a>
                <a href="{{ route('pemesanan.index') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-center">
                    Lihat Pemesanan Saya
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

                
