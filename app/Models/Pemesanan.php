<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $table = 'pemesanan';
    protected $primaryKey = 'id_pemesanan';
    public $timestamps = true;

    protected $fillable = [
        'id_user',
        'id_wisma',
        'nama_kegiatan',
        'lama_menginap',
        'jumlah_kamar',
        'penanggung_jawab',
        'status',
        'catatan_admin',
        'check_in_at',
        'check_out_at',
        'metode_pembayaran',
        'status_pembayaran',
        'total_biaya',
        'bukti_pembayaran_path',
        'pembayaran_dikonfirmasi_at',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'pembayaran_dikonfirmasi_at' => 'datetime',
        'total_biaya' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_pemesanan';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function wisma()
    {
        return $this->belongsTo(Wisma::class, 'id_wisma', 'id_wisma');
    }
}
