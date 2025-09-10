<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Pemesanan Saya
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($pemesanan->isEmpty())
            <div class="text-gray-600">Belum ada pemesanan.</div>
        @else
            <table class="w-full table-auto border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Wisma</th>
                        <th class="border px-4 py-2">Nama Kegiatan</th>
                        <th class="border px-4 py-2">Lama Menginap</th>
                        <th class="border px-4 py-2">Jumlah Kamar</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">Tanggal Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemesanan as $item)
                        <tr>
                            <td class="border px-4 py-2">{{ $item->wisma->nama_wisma ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $item->nama_kegiatan }}</td>
                            <td class="border px-4 py-2">{{ $item->lama_menginap }} hari</td>
                            <td class="border px-4 py-2">{{ $item->jumlah_kamar }}</td>
                            <td class="border px-4 py-2">{{ ucfirst($item->status) }}</td>
                            <td class="border px-4 py-2">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
