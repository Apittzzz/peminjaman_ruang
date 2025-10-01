<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('laporan', function (Blueprint $table) {
    $table->id('id_laporan');
    
    $table->unsignedBigInteger('id_user');
    $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
    
    $table->date('tanggal_laporan');
    $table->string('jenis_laporan');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};