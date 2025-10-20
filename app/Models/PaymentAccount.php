<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    protected $fillable = [
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'instruksi',
        'aktif',
    ];
}
