<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE pemesanan MODIFY status ENUM('reservasi','diproses','check-in','selesai','check_in','check_out') DEFAULT 'reservasi'");
        DB::statement("UPDATE pemesanan SET status = 'check_in' WHERE status = 'check-in'");
        DB::statement("UPDATE pemesanan SET status = 'check_out' WHERE status = 'selesai'");
        DB::statement("ALTER TABLE pemesanan MODIFY status ENUM('reservasi','diproses','check_in','check_out') DEFAULT 'reservasi'");

        Schema::table('pemesanan', function (Blueprint $table) {
            $table->text('catatan_admin')->nullable()->after('status');
            $table->timestamp('check_in_at')->nullable()->after('catatan_admin');
            $table->timestamp('check_out_at')->nullable()->after('check_in_at');
            $table->string('metode_pembayaran', 50)->nullable()->after('check_out_at');
            $table->enum('status_pembayaran', ['belum', 'menunggu_konfirmasi', 'selesai'])->default('belum')->after('metode_pembayaran');
            $table->decimal('total_biaya', 12, 2)->nullable()->after('status_pembayaran');
            $table->string('bukti_pembayaran_path')->nullable()->after('total_biaya');
            $table->timestamp('pembayaran_dikonfirmasi_at')->nullable()->after('bukti_pembayaran_path');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropColumn([
                'catatan_admin',
                'check_in_at',
                'check_out_at',
                'metode_pembayaran',
                'status_pembayaran',
                'total_biaya',
                'bukti_pembayaran_path',
                'pembayaran_dikonfirmasi_at',
            ]);
        });

        DB::statement("ALTER TABLE pemesanan MODIFY status ENUM('reservasi','diproses','check_in','check_out','check-in','selesai') DEFAULT 'reservasi'");
        DB::statement("UPDATE pemesanan SET status = 'selesai' WHERE status = 'check_out'");
        DB::statement("UPDATE pemesanan SET status = 'check-in' WHERE status = 'check_in'");
        DB::statement("ALTER TABLE pemesanan MODIFY status ENUM('reservasi','diproses','check-in','selesai') DEFAULT 'reservasi'");
    }
};
