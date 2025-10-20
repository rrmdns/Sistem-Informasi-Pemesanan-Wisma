<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Pemesanan Wisma
        </h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto space-y-6">
        <div class="bg-gray-50 p-4 rounded border border-gray-200">
            <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800">Nama:</span> {{ Auth::user()->name }}</p>
            <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800">Departemen:</span> {{ Auth::user()->departemen ?? '-' }}</p>
            <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800">Jenis User:</span> {{ Auth::user()->jenis_user }}</p>
        </div>

        @if(! $paymentAccounts->isEmpty())
            <div class="bg-white border border-indigo-100 rounded p-4 space-y-2">
                <p class="text-sm font-semibold text-indigo-700">Rekening Pembayaran Resmi</p>
                <p class="text-xs text-indigo-500">Gunakan salah satu rekening berikut untuk pembayaran non-tunai.</p>
                <ul class="text-sm text-gray-700 space-y-2">
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
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside space-y-1 text-sm">
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

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" class="mt-1 block w-full border-gray-300 rounded" value="{{ old('nama_kegiatan') }}" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lama Menginap (hari)</label>
                    <input type="number" name="lama_menginap" class="mt-1 block w-full border-gray-300 rounded" value="{{ old('lama_menginap') }}" min="1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jumlah Kamar</label>
                    <input type="number" name="jumlah_kamar" class="mt-1 block w-full border-gray-300 rounded" value="{{ old('jumlah_kamar') }}" min="1" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Penanggung Jawab (opsional)</label>
                <input type="text" name="penanggung_jawab" class="mt-1 block w-full border-gray-300 rounded" value="{{ old('penanggung_jawab') }}">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Kirim Pemesanan</button>
            </div>
        </form>
    </div>
</x-app-layout>
