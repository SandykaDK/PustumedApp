<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('min_max', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nama_obat_id')->constrained('nama_obat')->onDelete('cascade');
            $table->decimal('average_daily_usage', 10,2);
            $table->integer('maximum_daily_usage');
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->default(0);
            $table->integer('safety_stock')->default(0);
            $table->integer('reorder_point')->default(0);
            $table->integer('lead_time')->default(7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('min_max');
    }
};
