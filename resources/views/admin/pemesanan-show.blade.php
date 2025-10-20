<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pemesanan
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto space-y-6">
        <a href="{{ route('admin.pemesanan') }}" class="text-sm text-blue-600 underline">&laquo; Kembali ke daftar</a>

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

        @if($errors->any())
            <div class="p-4 bg-red-100 text-red-800 rounded">
                <strong>Terjadi kesalahan:</strong>
                <ul class="list-disc pl-5 mt-2 space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded shadow p-4 space-y-4">
                <h3 class="font-semibold text-gray-700">Informasi Pemesan</h3>
                <div class="text-sm text-gray-600 space-y-2">
                    <div><span class="font-semibold text-gray-800">Nama:</span> {{ $pemesanan->user->name ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-800">Departemen:</span> {{ $pemesanan->user->departemen ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-800">Jenis User:</span> {{ $pemesanan->user->jenis_user ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-800">Email:</span> {{ $pemesanan->user->email ?? '-' }}</div>
                </div>
                <hr>
                <h3 class="font-semibold text-gray-700">Detail Pemesanan</h3>
                <div class="text-sm text-gray-600 space-y-2">
                    <div><span class="font-semibold text-gray-800">Wisma:</span> {{ $pemesanan->wisma->nama_wisma ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-800">Nama Kegiatan:</span> {{ $pemesanan->nama_kegiatan }}</div>
                    <div><span class="font-semibold text-gray-800">Lama Menginap:</span> {{ $pemesanan->lama_menginap }} hari</div>
                    <div><span class="font-semibold text-gray-800">Jumlah Kamar:</span> {{ $pemesanan->jumlah_kamar }}</div>
                    <div><span class="font-semibold text-gray-800">Penanggung Jawab:</span> {{ $pemesanan->penanggung_jawab ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-800">Dibuat:</span> {{ optional($pemesanan->created_at)->format('d-m-Y H:i') }}</div>
                    <div><span class="font-semibold text-gray-800">Terakhir diperbarui:</span> {{ optional($pemesanan->updated_at)->format('d-m-Y H:i') }}</div>
                </div>

                @if($pemesanan->bukti_pembayaran_path)
                    <div class="mt-4 p-3 bg-indigo-50 border border-indigo-100 rounded">
                        <p class="text-sm font-semibold text-indigo-700">Bukti Pembayaran Pengguna</p>
                        <a href="{{ route('admin.pemesanan.downloadBukti', $pemesanan->id_pemesanan) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            Unduh bukti pembayaran
                        </a>
                    </div>
                @else
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-100 rounded text-sm text-yellow-800">
                        Pengguna belum mengunggah bukti pembayaran.
                    </div>
                @endif

                @if($pemesanan->status_pembayaran === 'selesai')
                    <div class="mt-3">
                        <a href="{{ route('admin.pemesanan.kuitansi', $pemesanan->id_pemesanan) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm">
                            Lihat Kuitansi Pembayaran
                        </a>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded shadow p-4 space-y-4">
                <form action="{{ route('admin.pemesanan.update', $pemesanan->id_pemesanan) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="status" value="Status Pemesanan" />
                        <select id="status" name="status" class="mt-1 w-full border-gray-300 rounded">
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $pemesanan->status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="catatan_admin" value="Catatan Admin" />
                        <textarea id="catatan_admin" name="catatan_admin" rows="4" class="mt-1 w-full border-gray-300 rounded">{{ old('catatan_admin', $pemesanan->catatan_admin) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="check_in_at" value="Waktu Check In" />
                            <input id="check_in_at"
                                   name="check_in_at"
                                   type="datetime-local"
                                   value="{{ old('check_in_at', optional($pemesanan->check_in_at)->format('Y-m-d\TH:i')) }}"
                                   class="mt-1 w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <x-input-label for="check_out_at" value="Waktu Check Out" />
                            <input id="check_out_at"
                                   name="check_out_at"
                                   type="datetime-local"
                                   value="{{ old('check_out_at', optional($pemesanan->check_out_at)->format('Y-m-d\TH:i')) }}"
                                   class="mt-1 w-full border-gray-300 rounded" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="metode_pembayaran" value="Metode Pembayaran" />
                            <input id="metode_pembayaran"
                                   name="metode_pembayaran"
                                   type="text"
                                   value="{{ old('metode_pembayaran', $pemesanan->metode_pembayaran) }}"
                                   class="mt-1 w-full border-gray-300 rounded" />
                        </div>
                        <div>
                            <x-input-label for="status_pembayaran" value="Status Pembayaran" />
                            <select id="status_pembayaran" name="status_pembayaran" class="mt-1 w-full border-gray-300 rounded">
                                @foreach($paymentStatusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status_pembayaran', $pemesanan->status_pembayaran) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="total_biaya" value="Total Biaya (Rp)" />
                        <input id="total_biaya"
                               name="total_biaya"
                               type="number"
                               min="0"
                               step="0.01"
                               value="{{ old('total_biaya', $pemesanan->total_biaya) }}"
                               class="mt-1 w-full border-gray-300 rounded" />
                    </div>

                    <div class="text-sm text-gray-500">
                        <div><span class="font-semibold">Pembayaran dikonfirmasi:</span> {{ optional($pemesanan->pembayaran_dikonfirmasi_at)->format('d-m-Y H:i') ?? '-' }}</div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.pemesanan') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </form>

                <div class="border-t border-gray-200 pt-4">
                    <form action="{{ route('admin.pemesanan.konfirmasiPembayaran', $pemesanan->id_pemesanan) }}" method="POST" class="space-y-3">
                        @csrf
                        <p class="text-sm font-semibold text-gray-700">Konfirmasi Pembayaran Cepat</p>
                        <input type="text" name="metode_pembayaran" value="{{ $pemesanan->metode_pembayaran }}" placeholder="Metode pembayaran" class="w-full border-gray-300 rounded text-sm p-2">
                        <input type="number" name="total_biaya" value="{{ $pemesanan->total_biaya }}" step="0.01" min="0" placeholder="Total biaya" class="w-full border-gray-300 rounded text-sm p-2">
                        <label class="inline-flex items-center text-sm text-gray-600">
                            <input type="checkbox" name="tandai_check_out" value="1" class="mr-2">
                            Tandai pemesanan sebagai check out
                        </label>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 text-sm">Konfirmasi Pembayaran</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
