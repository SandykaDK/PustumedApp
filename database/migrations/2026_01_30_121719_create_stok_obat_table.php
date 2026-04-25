<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nama_obat_id')
                ->constrained('nama_obat')
                ->onDelete('cascade');
            $table->date('tanggal_kadaluwarsa');
            $table->integer('stok')->default(0);
            $table->string('no_batch')->nullable();
            $table->string('status')->default('kosong'); // 'kosong' atau 'tersedia'
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['nama_obat_id', 'tanggal_kadaluwarsa']);
            $table->unique(['nama_obat_id', 'tanggal_kadaluwarsa', 'no_batch']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nama_obat');
    }
};
