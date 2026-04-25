<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pengeluaran_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengeluaran_obat_id')->constrained('pengeluaran_obat', 'id');
            $table->foreignId('nama_obat_id')->constrained('nama_obat', 'id');
            $table->integer('jumlah_keluar');
            $table->foreignId('satuan_id')->constrained('satuan_obat', 'id');
            $table->foreignId('stok_obat_id')->nullable()->constrained('stok_obat', 'id');
            $table->string('lokasi_penyimpanan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengeluaran_obat');
    }
};
