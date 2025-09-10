<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->unsignedBigInteger('id_wisma')->nullable()->after('id_user');

            $table->foreign('id_wisma')
                ->references('id_wisma')->on('wisma')
                ->nullOnDelete(); // jika wisma dihapus, set null di pemesanan
        });
    }

    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            $table->dropForeign(['id_wisma']);
            $table->dropColumn('id_wisma');
        });
    }
};
