<?php

namespace App\Notifications;

use App\Models\Pemesanan;
use App\Support\PemesananDictionary;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingCreatedNotification extends Notification
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
        $statusLabels = PemesananDictionary::statusLabels();

        return (new MailMessage)
            ->subject('Pemesanan Wisma Berhasil Dibuat')
            ->greeting('Halo ' . $notifiable->name . ' 👋')
            ->line('Terima kasih, permintaan pemesanan wisma kamu telah kami terima.')
            ->line('Detail pemesanan:')
            ->line('• Wisma: ' . ($this->pemesanan->wisma->nama_wisma ?? '-'))
            ->line('• Nama kegiatan: ' . $this->pemesanan->nama_kegiatan)
            ->line('• Jadwal check-in: ' . optional($this->pemesanan->check_in_at)->format('d M Y') . ' (14:00 WIB)')
            ->line('• Jadwal check-out: ' . optional($this->pemesanan->check_out_at)->format('d M Y') . ' (12:00 WIB)')
            ->line('• Status awal: ' . ($statusLabels[$this->pemesanan->status] ?? $this->pemesanan->status))
            ->action('Lihat detail pemesanan', route('pemesanan.show', $this->pemesanan))
            ->line('Kami akan mengirim kabar jika ada pembaruan status.');
    }
}
