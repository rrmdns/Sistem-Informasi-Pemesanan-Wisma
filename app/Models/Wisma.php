<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wisma extends Model
{
    protected $table = 'wisma';
    protected $primaryKey = 'id_wisma';
    public $timestamps = true;

    protected $fillable = ['nama_wisma'];

    public function pemesanan()
    {
        return $this->hasMany(Pemesanan::class, 'id_wisma', 'id_wisma');
    }
}
