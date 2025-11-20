<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruang', function (Blueprint $table) {
            if (!Schema::hasColumn('ruang', 'available_slots')) {
                $table->integer('available_slots')->default(5)->after('kapasitas');
            }
            if (!Schema::hasColumn('ruang', 'is_temporary_occupied')) {
                $table->boolean('is_temporary_occupied')->default(false)->after('status');
            }
            if (!Schema::hasColumn('ruang', 'pengguna_default_temp')) {
                $table->string('pengguna_default_temp', 100)->nullable()->after('pengguna_default');
            }
            if (!Schema::hasColumn('ruang', 'ruang_asal_id')) {
                $table->unsignedBigInteger('ruang_asal_id')->nullable()->after('pengguna_default_temp');
                $table->foreign('ruang_asal_id')->references('id_ruang')->on('ruang')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ruang', function (Blueprint $table) {
            $table->dropForeign(['ruang_asal_id']);
            $table->dropColumn(['available_slots', 'is_temporary_occupied', 'pengguna_default_temp', 'ruang_asal_id']);
        });
    }
};