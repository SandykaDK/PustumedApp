<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penerimaan_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penerimaan_obat_id')->constrained('penerimaan_obat', 'id');
            $table->foreignId('nama_obat_id')->constrained('nama_obat', 'id');
            $table->foreignId('jenis_obat_id')->constrained('jenis_obat', 'id');
            $table->string('no_batch')->nullable();
            $table->date('tanggal_kadaluwarsa')->nullable();
            $table->integer('jumlah_masuk');
            $table->foreignId('satuan_id')->constrained('satuan_obat', 'id');
            $table->string('lokasi_penyimpanan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penerimaan_obat');
    }
};
