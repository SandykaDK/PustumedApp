<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_obat', function (Blueprint $table) {
            $table->id();
            $table->string('no_batch');
            $table->date('tanggal_penerimaan');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_obat');
    }
};
