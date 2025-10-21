<?php

namespace App\Notifications;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentConfirmedNotification extends Notification
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
        return (new MailMessage)
            ->subject('Pembayaran Penginapan Terkonfirmasi')
            ->greeting('Halo ' . $notifiable->name)
            ->line('Terima kasih, pembayaran penginapan kamu sudah dikonfirmasi.')
            ->line('Detail pemesanan:')
            ->line('• Wisma: ' . ($this->pemesanan->wisma->nama_wisma ?? '-'))
            ->line('• Total biaya: ' . ($this->pemesanan->total_biaya ? 'Rp ' . number_format($this->pemesanan->total_biaya, 2, ',', '.') : '-'))
            ->line('• Metode pembayaran: ' . ($this->pemesanan->metode_pembayaran ? \Illuminate\Support\Str::title(str_replace('_', ' ', $this->pemesanan->metode_pembayaran)) : '-'))
            ->action('Unduh kuitansi', route('pemesanan.downloadKuitansi', $this->pemesanan))
            ->line('Selamat menikmati waktu istirahat di wisma kami!');
    }
}
