<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satuan_obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_satuan')->unique();
            $table->string('satuan_obat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuan_obat');
    }
};
