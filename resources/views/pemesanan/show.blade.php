<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pemesanan
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto space-y-6">
        <a href="{{ route('pemesanan.index') }}" class="text-sm text-blue-600 underline">&laquo; Kembali ke daftar</a>

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

        <div class="bg-white rounded shadow p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $pemesanan->wisma->nama_wisma ?? 'Wisma tidak tersedia' }}</h3>
                    <p class="text-gray-600 text-sm">Nama Kegiatan: {{ $pemesanan->nama_kegiatan }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-700">
                    {{ $statusLabels[$pemesanan->status] ?? $pemesanan->status }}
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
                <div>
                    <p><span class="font-medium text-gray-900">Lama Menginap:</span> {{ $pemesanan->lama_menginap }} hari</p>
                    <p><span class="font-medium text-gray-900">Jumlah Kamar:</span> {{ $pemesanan->jumlah_kamar }}</p>
                    <p><span class="font-medium text-gray-900">Penanggung Jawab:</span> {{ $pemesanan->penanggung_jawab ?? '-' }}</p>
                </div>
                <div>
                    <p><span class="font-medium text-gray-900">Diajukan pada:</span> {{ optional($pemesanan->created_at)->format('d-m-Y H:i') }}</p>
                    <p><span class="font-medium text-gray-900">Check In:</span>
                        @if($pemesanan->check_in_at)
                            {{ $pemesanan->check_in_at->format('d-m-Y') }} pukul 14:00
                        @else
                            -
                        @endif
                    </p>
                    <p><span class="font-medium text-gray-900">Check Out:</span>
                        @if($pemesanan->check_out_at)
                            {{ $pemesanan->check_out_at->format('d-m-Y') }} pukul 12:00
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">Progress Pemesanan</p>
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

            <div class="bg-indigo-50 border border-indigo-100 rounded p-4 text-sm text-indigo-800">
                <p class="font-semibold">Langkah Selanjutnya</p>
                <p class="mt-1">{{ $statusGuidance[$pemesanan->status] ?? 'Ikuti arahan petugas untuk langkah berikutnya.' }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded space-y-2">
                    <p class="text-sm font-semibold text-gray-700">Informasi Pembayaran</p>
                    <dl class="mt-2 text-sm text-gray-600 space-y-1">
                        <div class="flex justify-between">
                            <dt>Status</dt>
                            <dd class="font-semibold text-gray-800">{{ $paymentStatusLabels[$pemesanan->status_pembayaran] ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Metode</dt>
                            <dd>{{ $pemesanan->metode_pembayaran ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Total Biaya</dt>
                            <dd>{{ $pemesanan->total_biaya ? 'Rp ' . number_format($pemesanan->total_biaya, 2, ',', '.') : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Konfirmasi</dt>
                            <dd>{{ optional($pemesanan->pembayaran_dikonfirmasi_at)->format('d-m-Y H:i') ?? '-' }}</dd>
                        </div>
                    </dl>

                    @if($pemesanan->bukti_pembayaran_path)
                        <div class="pt-2 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Bukti pembayaran terakhir:</p>
                            <a href="{{ route('pemesanan.downloadBukti', $pemesanan) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Unduh bukti pembayaran</a>
                        </div>
                    @endif
                    @if($pemesanan->status_pembayaran === 'selesai')
                        <div class="pt-2 border-t border-gray-200 mt-2">
                            <p class="text-xs text-gray-500 mb-1">Kuitansi pembayaran tersedia:</p>
                            <a href="{{ route('pemesanan.downloadKuitansi', $pemesanan) }}" class="text-sm text-emerald-600 hover:text-emerald-800">Unduh kuitansi pembayaran</a>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-sm font-semibold text-gray-700">Catatan Admin</p>
                    <p class="mt-2 text-sm text-gray-600 whitespace-pre-line">
                        {{ $pemesanan->catatan_admin ? $pemesanan->catatan_admin : 'Belum ada catatan tambahan dari admin.' }}
                    </p>
                </div>
            </div>

            @if(! $paymentAccounts->isEmpty())
                <div class="bg-white border border-indigo-100 rounded p-4 space-y-2">
                    <p class="text-sm font-semibold text-indigo-700">Rekening Pembayaran Resmi</p>
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

            @if($pemesanan->status === 'check_in')
                <div class="bg-white border border-gray-200 rounded p-4">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Unggah Bukti Pembayaran</p>
                    <form action="{{ route('pemesanan.uploadBukti', $pemesanan) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-sm" required>
                        <p class="text-xs text-gray-500">Format: JPG/PNG/PDF, maksimal 2MB.</p>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Kirim Bukti</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
