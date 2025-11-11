<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ruang', function (Blueprint $table) {
            $table->string('pengguna_default')->nullable()->after('status');
            $table->text('keterangan_penggunaan')->nullable()->after('pengguna_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruang', function (Blueprint $table) {
            $table->dropColumn(['pengguna_default', 'keterangan_penggunaan']);
        });
    }
};
