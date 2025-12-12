<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan Selesai</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1f2937; margin: 30px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 16px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; }
        th { background-color: #f3f4f6; text-align: left; }
        .summary { margin-top: 16px; }
        .summary table { width: 50%; }
        .meta { margin-bottom: 18px; font-size: 12px; }
        .meta span { display: inline-block; margin-right: 16px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Rekap Pemesanan Selesai</h1>
    <div class="meta">
        <span>Periode: {{ $periodLabel ?? 'Semua Periode' }}</span>
        <span>Rentang: {{ isset($periodStart) ? $periodStart->format('d M Y') : '-' }} - {{ isset($periodEnd) ? $periodEnd->format('d M Y') : '-' }}</span>
        <span>Tanggal Cetak: {{ $generatedAt->format('d M Y H:i') }} WIB</span>
        <span>Jumlah Data: {{ $pemesananSelesai->count() }}</span>
    </div>

    <div class="summary">
        <table>
            <tbody>
                <tr>
                    <th>Total Malam Menginap</th>
                    <td>{{ $totalMalam }} malam</td>
                </tr>
                <tr>
                    <th>Total Kamar Digunakan</th>
                    <td>{{ $totalKamar }} kamar</td>
                </tr>
                <tr>
                    <th>Perkiraan Total Pembayaran</th>
                    <td>Rp {{ number_format($totalPendapatan, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Pemesan</th>
                <th>Departemen</th>
                <th>Wisma</th>
                <th>Kegiatan</th>
                <th>Tgl Check-in</th>
                <th>Tgl Check-out</th>
                <th>Kamar</th>
                <th>Malam</th>
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemesananSelesai as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->user->departemen ?? '-' }}</td>
                    <td>{{ $item->wisma->nama_wisma ?? '-' }}</td>
                    <td>{{ $item->nama_kegiatan }}</td>
                    <td>{{ optional($item->check_in_at)->format('d M Y') ?? '-' }}</td>
                    <td>{{ optional($item->check_out_at)->format('d M Y') ?? '-' }}</td>
                    <td>{{ $item->jumlah_kamar }}</td>
                    <td>{{ $item->lama_menginap }}</td>
                    <td>{{ $item->metode_pembayaran ? \Illuminate\Support\Str::title(str_replace('_', ' ', $item->metode_pembayaran)) : '-' }}</td>
                    <td>{{ $item->status_pembayaran ? \Illuminate\Support\Str::title(str_replace('_', ' ', $item->status_pembayaran)) : '-' }}</td>
                    <td class="text-right">{{ $item->total_biaya ? 'Rp ' . number_format($item->total_biaya, 2, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align:center; padding: 12px;">Belum ada pemesanan dengan status selesai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
