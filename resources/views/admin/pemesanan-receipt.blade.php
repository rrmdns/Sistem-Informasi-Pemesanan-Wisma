<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Pembayaran - {{ $receiptNumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin-bottom: 5px; }
        .meta { margin-bottom: 20px; font-size: 14px; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 0; }
        .section-title { font-weight: bold; margin-top: 24px; margin-bottom: 8px; text-transform: uppercase; font-size: 12px; letter-spacing: .1em; }
        .detail-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .detail-table th, .detail-table td { border: 1px solid #d1d5db; padding: 8px; text-align: left; }
        .totals { margin-top: 20px; width: 100%; }
        .totals td { padding: 6px 0; }
        .footer { margin-top: 40px; font-size: 12px; color: #6b7280; text-align: center; }
        .signature { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Kuitansi Pembayaran</h1>
        <p>Nomor: <strong>{{ $receiptNumber }}</strong></p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td>Tanggal Konfirmasi</td>
                <td>: {{ optional($pemesanan->pembayaran_dikonfirmasi_at)->format('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td>Nama Tamu</td>
                <td>: {{ $pemesanan->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Departemen</td>
                <td>: {{ $pemesanan->user->departemen ?? '-' }}</td>
            </tr>
            <tr>
                <td>Wisma</td>
                <td>: {{ $pemesanan->wisma->nama_wisma ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div>
        <p class="section-title">Detail Pemesanan</p>
        <table class="detail-table">
            <thead>
                <tr>
                    <th>Nama Kegiatan</th>
                    <th>Lama Menginap</th>
                    <th>Jumlah Kamar</th>
                    <th>Metode Pembayaran</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $pemesanan->nama_kegiatan }}</td>
                    <td>{{ $pemesanan->lama_menginap }} hari</td>
                    <td>{{ $pemesanan->jumlah_kamar }}</td>
                    <td>{{ $pemesanan->metode_pembayaran ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <table class="totals">
        <tr>
            <td style="width: 70%; font-weight: bold;">Total Dibayar</td>
            <td style="text-align: right; font-size: 18px; font-weight: bold;">{{ $pemesanan->total_biaya ? 'Rp ' . number_format($pemesanan->total_biaya, 2, ',', '.') : '-' }}</td>
        </tr>
    </table>

    <div class="signature">
        <p>Pelayanan Umum</p>
        <p style="margin-top: 60px; border-top: 1px solid #d1d5db; display: inline-block; padding-top: 4px; min-width: 200px;">Tanda tangan & Nama</p>
    </div>

    <div class="footer">
        <p>Kuitansi ini dicetak secara elektronik dan tidak memerlukan tanda tangan basah.</p>
    </div>
</body>
</html>
