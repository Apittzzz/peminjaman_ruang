<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('peminjaman', function (Blueprint $table) {
    $table->id('id_peminjaman');
    
    // Manual foreign key definition
    $table->unsignedBigInteger('id_user');
    $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
    
    $table->unsignedBigInteger('id_ruang');
    $table->foreign('id_ruang')->references('id_ruang')->on('ruang')->onDelete('cascade');
    
    $table->date('tanggal_pinjam');
    $table->date('tanggal_kembali');
    $table->time('waktu_mulai');
    $table->time('waktu_selesai');
    $table->text('keperluan');
    $table->string('status')->default('pending');
    $table->text('catatan')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};