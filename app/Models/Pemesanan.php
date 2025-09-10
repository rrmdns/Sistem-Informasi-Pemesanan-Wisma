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
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function wisma()
    {
        return $this->belongsTo(Wisma::class, 'id_wisma', 'id_wisma');
    }
}