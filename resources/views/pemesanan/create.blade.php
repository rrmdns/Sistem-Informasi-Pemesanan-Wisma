<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Pemesanan Wisma
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
            
        @endif

        <div class="mb-4 bg-gray-50 p-4 rounded">
            <p><strong>Nama:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Departemen:</strong> {{ Auth::user()->departemen ?? '-' }}</p>
            <p><strong>Jenis User:</strong> {{ Auth::user()->jenis_user }}</p>
        </div>

            <form action="{{ route('pemesanan.store') }}" method="POST">
        @csrf
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('pemesanan.store') }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Pilih Wisma</label>
                <select name="id_wisma" class="mt-1 block w-full border-gray-300 rounded" required>
                    <option value="">- Pilih Wisma -</option>
                    @foreach($daftarWisma as $wisma)
                        <option value="{{ $wisma->id_wisma }}" {{ old('id_wisma') == $wisma->id_wisma ? 'selected' : '' }}>
                            {{ $wisma->nama_wisma }}
                        </option>
                    @endforeach
                </select>
            </div>

        <div class="mb-4">
            <label class="block font-medium">Nama Kegiatan</label>
            <input type="text" name="nama_kegiatan" class="w-full border rounded p-2"
                value="{{ old('nama_kegiatan') }}" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Lama Menginap (hari)</label>
            <input type="number" name="lama_menginap" class="w-full border rounded p-2"
                value="{{ old('lama_menginap') }}" min="1" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Jumlah Kamar</label>
            <input type="number" name="jumlah_kamar" class="w-full border rounded p-2"
                value="{{ old('jumlah_kamar') }}" min="1" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Penanggung Jawab (opsional)</label>
            <input type="text" name="penanggung_jawab" class="w-full border rounded p-2"
                value="{{ old('penanggung_jawab') }}">
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Kirim Pemesanan
        </button>
    </form>

    </div>
</x-app-layout>
