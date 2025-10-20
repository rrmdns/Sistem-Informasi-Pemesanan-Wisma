<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin - Semua Pemesanan
        </h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($semuaPemesanan->isEmpty())
            <div class="text-gray-600">Belum ada data pemesanan.</div>
        @else
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Nama User</th>
                        <th class="border px-4 py-2">Departemen</th>
                        <th class="border px-4 py-2">Jenis User</th>
                        <th class="border px-4 py-2">Wisma</th>
                        <th class="border px-4 py-2">Kegiatan</th>
                        <th class="border px-4 py-2">Lama</th>
                        <th class="border px-4 py-2">Kamar</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">Pembayaran</th>
                        <th class="border px-4 py-2">Dibuat</th>
                        <th class="border px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semuaPemesanan as $item)
                        <tr>
                            <td class="border px-4 py-2">{{ $item->user->name ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $item->user->departemen ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $item->user->jenis_user ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $item->wisma->nama_wisma ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $item->nama_kegiatan }}</td>
                            <td class="border px-4 py-2">{{ $item->lama_menginap }} hari</td>
                            <td class="border px-4 py-2">{{ $item->jumlah_kamar }}</td>
                            <td class="border px-4 py-2">
                                {{ $statusOptions[$item->status] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $item->status)) }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ $paymentStatusOptions[$item->status_pembayaran] ?? \Illuminate\Support\Str::title(str_replace('_', ' ', $item->status_pembayaran)) }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ optional($item->created_at)->format('d-m-Y') }}
                            </td>
                            <td class="border px-4 py-2 text-center space-y-2">
                                <a href="{{ route('admin.pemesanan.show', $item->id_pemesanan) }}"
                                   class="inline-flex items-center justify-center w-full px-3 py-1 bg-slate-200 rounded hover:bg-slate-300">
                                    Detail
                                </a>

                                <form action="{{ route('admin.pemesanan.ubahStatus', $item->id_pemesanan) }}" method="POST" class="mb-1">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="w-full border-gray-300 rounded">
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($item->status === $value)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                <form action="{{ route('admin.pemesanan.batalkan', $item->id_pemesanan) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-full px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                        Batalkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
