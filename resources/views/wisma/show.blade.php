<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $wisma->nama_wisma }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 space-y-6">
        <div class="overflow-hidden rounded-2xl shadow-sm border border-gray-100">
            <div class="relative h-72 md:h-96 bg-gray-200">
                <img src="{{ $heroImage }}" alt="Gambar {{ $wisma->nama_wisma }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    <h1 class="text-2xl md:text-3xl font-bold text-white drop-shadow">{{ $wisma->nama_wisma }}</h1>
                    <p class="text-white/80 text-sm md:text-base"></p>
                </div>
            </div>
            <div class="bg-white p-6 space-y-5">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="md:max-w-3xl space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Gambaran Umum</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $description }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('pemesanan.create', ['wisma' => $wisma->id_wisma]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-indigo-700">
                            Pesan Wisma Ini
                        </a>
                        <a href="{{ route('wisma.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900">Fasilitas Unggulan</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($facilities as $facility)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100">
                                {{ $facility }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-900">Galeri</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($gallery as $image)
                            <div class="aspect-video rounded-lg overflow-hidden border border-gray-100 bg-gray-100">
                                <img src="{{ $image }}" alt="Galeri {{ $wisma->nama_wisma }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500">Layout ini bisa disesuaikan dengan foto kamar, lobi, ruang meeting, atau fasilitas lain.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
