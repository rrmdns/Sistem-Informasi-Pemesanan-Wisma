<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wisma', function (Blueprint $table) {
            $table->id('id_wisma');
            $table->string('nama_wisma', 100)->unique();
            $table->timestamps();
        });

        // Seed 11 nama wisma
        $now = now();
        DB::table('wisma')->insert([
            ['nama_wisma' => 'Melati',     'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Delima VIP', 'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Delima Biasa','created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Kenanga',    'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Teratai',    'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Cempaka',    'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Gandaria',   'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Anggrek',    'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Sawi 2',     'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Cendana 7',  'created_at' => $now, 'updated_at' => $now],
            ['nama_wisma' => 'Cendana 8',  'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('wisma');
    }
};
