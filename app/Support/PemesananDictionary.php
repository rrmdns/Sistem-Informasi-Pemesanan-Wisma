<?php

namespace App\Support;

class PemesananDictionary
{
    public static function statusLabels(): array
    {
        return [
            'reservasi' => 'Reservasi',
            'diproses' => 'Diproses',
            'check_in' => 'Check In',
            'check_out' => 'Check Out',
        ];
    }

    public static function paymentStatusLabels(): array
    {
        return [
            'belum' => 'Belum Dibayar',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'selesai' => 'Pembayaran Selesai',
        ];
    }

    public static function statusGuidance(): array
    {
        return [
            'reservasi' => 'Admin sedang memeriksa ketersediaan kamar. Mohon menunggu untuk konfirmasi selanjutnya.',
            'diproses' => 'Pesanan telah diproses, tamu dipersilahkan untuk datang menginap sesuai waktu yang telah ditentukan.',
            'check_in' => 'Apabila tamu ingin melakukan check-out, dipersilahkan menuju ke front office untuk konfirmasi pembayaran.',
            'check_out' => 'Terima kasih sudah menggunakan jasa penginapan kami. Pesanan ini telah selesai.',
        ];
    }
}
