<?php

namespace App\Notifications;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(private Pemesanan $pemesanan)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pemesanan = $this->pemesanan;

        return (new MailMessage)
            ->subject('Informasi Pembatalan Pemesanan Wisma')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Mohon maaf, pemesanan wisma kamu tidak dapat kami lanjutkan karena kamar yang dipilih tidak tersedia di tanggal tersebut.')
            ->line('Detail pemesanan yang dibatalkan:')
            ->line('Wisma: ' . ($pemesanan->wisma->nama_wisma ?? '-'))
            ->line('Nama kegiatan: ' . $pemesanan->nama_kegiatan)
            ->line('Tanggal check-in: ' . optional($pemesanan->check_in_at)->format('d M Y'))
            ->line('Tanggal check-out: ' . optional($pemesanan->check_out_at)->format('d M Y'))
            ->line('Silakan coba memilih tanggal lain atau wisma lain yang masih tersedia. Jika membutuhkan bantuan, hubungi Front Office atau Pelayanan Umum.')
            ->action('Ajukan pemesanan baru', route('pemesanan.create'))
            ->line('Terima kasih atas pengertiannya.');
    }
}
