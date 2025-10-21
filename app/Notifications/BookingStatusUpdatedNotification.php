<?php

namespace App\Notifications;

use App\Models\Pemesanan;
use App\Support\PemesananDictionary;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(private Pemesanan $pemesanan, private string $oldStatus)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = PemesananDictionary::statusLabels();
        $statusGuidance = PemesananDictionary::statusGuidance();

        $old = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $new = $statusLabels[$this->pemesanan->status] ?? $this->pemesanan->status;
        $guidance = $statusGuidance[$this->pemesanan->status] ?? 'Ikuti arahan petugas untuk langkah berikutnya.';

        return (new MailMessage)
            ->subject('Status Pemesanan Wisma Berubah')
            ->greeting('Halo ' . $notifiable->name)
            ->line("Status pemesanan kamu berubah dari **{$old}** menjadi **{$new}**.")
            ->line($guidance)
            ->action('Lihat detail pemesanan', route('pemesanan.show', $this->pemesanan))
            ->line('Hubungi front office bila membutuhkan bantuan lebih lanjut.');
    }
}
