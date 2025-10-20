<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pembayaran Menunggu Konfirmasi
        </h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-amber-200 p-6 space-y-4">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-amber-900">Daftar Pembayaran Pending</h3>
                    <p class="text-sm text-amber-700">Tinjau bukti pembayaran yang sudah diunggah pengguna dan lakukan konfirmasi.</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">
                    Total {{ $pendingPayments->total() }} pembayaran menunggu
                </span>
            </div>

            @if($pendingPayments->isEmpty())
                <p class="text-sm text-amber-700">Tidak ada pembayaran yang menunggu konfirmasi.</p>
            @else
                <div class="space-y-3">
                    @foreach($pendingPayments as $item)
                        <div class="border border-amber-200 bg-amber-50 rounded-lg p-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-amber-900">{{ $item->user->name ?? '-' }}</p>
                                    <p class="text-xs text-amber-700">{{ $item->wisma->nama_wisma ?? '-' }} • {{ $item->nama_kegiatan }}</p>
                                    <p class="text-xs text-amber-700 mt-1">Diunggah: {{ optional($item->updated_at)->format('d M Y H:i') }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.pemesanan.downloadBukti', $item->id_pemesanan) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-white border border-amber-300 text-amber-800 rounded hover:bg-amber-100">Unduh Bukti</a>
                                    <a href="{{ route('admin.pemesanan.show', $item->id_pemesanan) }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold bg-amber-600 text-white rounded hover:bg-amber-700">Tinjau & Konfirmasi</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $pendingPayments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
