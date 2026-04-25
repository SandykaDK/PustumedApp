<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nama_obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat')->unique();
            $table->string('nama_obat')->unique();
            $table->foreignId('jenis_obat_id')->constrained('jenis_obat', 'id');
            $table->foreignId('satuan_obat_id')->constrained('satuan_obat', 'id');
            $table->string('lokasi_penyimpanan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nama_obat');
    }
};
