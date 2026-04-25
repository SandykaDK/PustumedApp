<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_pemusnahan_obat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pemusnahan_obat_id');
            $table->unsignedBigInteger('nama_obat_id');
            $table->unsignedBigInteger('stok_obat_id')->nullable();
            $table->integer('jumlah')->default(0);
            $table->unsignedBigInteger('satuan_id')->nullable();
            $table->string('lokasi_penyimpanan')->nullable();
            $table->timestamps();

            $table->foreign('pemusnahan_obat_id')->references('id')->on('pemusnahan_obat')->onDelete('cascade');
            $table->foreign('nama_obat_id')->references('id')->on('nama_obat')->onDelete('cascade');
            $table->foreign('stok_obat_id')->references('id')->on('stok_obat')->onDelete('set null');
            $table->foreign('satuan_id')->references('id')->on('satuan_obat')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pemusnahan_obat');
    }
};