<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('action', 50);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('peminjaman_id')->nullable();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Foreign keys
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('peminjaman_id')->references('id_peminjaman')->on('peminjaman')->onDelete('cascade');
            
            // Indexes
            $table->index('action');
            $table->index('peminjaman_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};