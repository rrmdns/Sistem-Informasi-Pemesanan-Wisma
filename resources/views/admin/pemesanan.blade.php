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
            <table class="w-full border-collapse border border-gray-300">
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
                        <th class="border px-4 py-2">Tanggal</th>
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
                            <td class="border px-4 py-2">{{ ucfirst($item->status) }}</td>
                            <td class="border px-4 py-2">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                            </td>
                            <td class="border px-4 py-2 text-center space-y-1">
                                <form action="{{ route('admin.pemesanan.ubahStatus', $item->id_pemesanan) }}" method="POST" class="mb-1">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="text-sm border-gray-300 rounded">
                                        @foreach(['reservasi', 'diproses', 'check-in', 'selesai'] as $status)
                                            <option value="{{ $status }}" @if($item->status == $status) selected @endif>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                <!-- ✅ Tombol batalkan tambahan -->
                                <form action="{{ route('admin.pemesanan.batalkan', $item->id_pemesanan) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
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
