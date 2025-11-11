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
            $table->unsignedBigInteger('ruang_asal_id')->nullable()->after('keterangan_penggunaan')
                ->comment('ID ruang asal saat pengguna default dipindah sementara');
            $table->string('pengguna_default_temp')->nullable()->after('ruang_asal_id')
                ->comment('Menyimpan pengguna default sementara yang dipindahkan ke ruang ini');
            $table->boolean('is_temporary_occupied')->default(false)->after('pengguna_default_temp')
                ->comment('Menandakan ruang ini sedang ditempati sementara oleh pengguna default dari ruang lain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruang', function (Blueprint $table) {
            $table->dropColumn(['ruang_asal_id', 'pengguna_default_temp', 'is_temporary_occupied']);
        });
    }
};
