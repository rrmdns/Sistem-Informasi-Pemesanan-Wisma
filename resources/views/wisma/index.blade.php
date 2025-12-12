<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Jelajahi Wisma
        </h2>
    </x-slot>

    @php
        // Placeholder URL dapat diganti dengan URL gambar wisma nyata.
        $placeholderImages = [
            'https://images.unsplash.com/photo-1501117716987-c8e1ecb210af?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1505693416388-7e3d345394d4?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1501117716987-c8e1ecb210af?auto=format&fit=crop&w=1200&q=80',
        ];
    @endphp

    <div class="py-8 max-w-7xl mx-auto space-y-6">
        <div class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-sky-500 text-white rounded-2xl shadow-lg p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="md:col-span-2 space-y-3">
                    <p class="text-sm uppercase tracking-wide text-indigo-100">Pilih Wisma</p>
                    <h1 class="text-3xl font-bold">Temukan Wisma yang Tepat</h1>
                    <p class="text-indigo-100 text-sm md:text-base">Lihat daftar wisma yang tersedia dan pilih yang paling sesuai untuk kegiatanmu. Setiap kartu berisi nama wisma dan tombol cepat untuk mulai membuat pemesanan.</p>
                    <div class="flex gap-3">
                        <a href="{{ route('pemesanan.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-indigo-700 font-semibold rounded-lg shadow hover:bg-indigo-50 text-sm">
                            Buat Pemesanan
                        </a>
                        <a href="{{ route('pemesanan.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-500 border border-indigo-200 text-white font-semibold rounded-lg hover:bg-indigo-600 text-sm">
                            Pemesanan Saya
                        </a>
                    </div>
                </div>
                <div class="bg-white bg-opacity-10 border border-indigo-200 rounded-xl p-4 space-y-3">
                    <form method="GET" action="{{ route('wisma.index') }}" class="space-y-3">
                        <label for="wisma" class="text-sm font-semibold text-white">Tampilkan Wisma</label>
                        <select id="wisma" name="wisma" class="w-full rounded-lg border-indigo-100 bg-white text-gray-800 placeholder-gray-500 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300">
                            <option value="">Semua Wisma</option>
                            @foreach($allWisma as $wisma)
                                <option value="{{ $wisma->id_wisma }}" @selected($selectedWismaId === $wisma->id_wisma)>
                                    {{ $wisma->nama_wisma }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-white text-indigo-700 font-semibold rounded-lg hover:bg-indigo-50 text-sm">
                            Terapkan Filter
                        </button>
                    </form>
                    <p class="text-xs text-indigo-100">Filter ini hanya mengubah wisma yang ditampilkan di bawah. Kamu bisa ganti gambar sesuai kebutuhan.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Daftar Wisma</h3>
                    <p class="text-sm text-gray-500">Pilih wisma untuk lanjut membuat pemesanan.</p>
                </div>
                <span class="text-xs px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 font-semibold">
                    {{ $visibleWisma->count() }} Wisma
                </span>
            </div>

            @if($visibleWisma->isEmpty())
                <div class="text-sm text-gray-500">Belum ada wisma terdaftar.</div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($visibleWisma as $index => $wisma)
                        @php
                            $imageUrl = $placeholderImages[$index % count($placeholderImages)];
                        @endphp
                        <div class="border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition group">
                            <div class="relative h-48 bg-gray-200">
                                <img src="{{ $imageUrl }}" alt="Gambar Wisma" class="w-full h-full object-cover transition duration-300 group-hover:scale-[1.02]">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                <div class="absolute bottom-3 left-3 text-white text-xs px-2 py-1 bg-black/40 rounded">
                                    Placeholder Gambar
                                </div>
                            </div>
                            <div class="p-4 space-y-2">
                                <h4 class="text-lg font-semibold text-gray-900">{{ $wisma->nama_wisma }}</h4>
                                <p class="text-sm text-gray-600">Silakan ganti placeholder dengan foto wisma yang sesuai. Pengguna dapat lanjut memilih wisma ini untuk pemesanan.</p>
                                <div class="flex items-center justify-between pt-2">
                                    <a href="{{ route('pemesanan.create', ['wisma' => $wisma->id_wisma]) }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                        Pilih Wisma Ini
                                    </a>
                                    <span class="text-xs text-gray-500">ID: {{ $wisma->id_wisma }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
