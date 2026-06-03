<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pemusnahan_obat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // pengaju (petugas)
            $table->dateTime('tanggal_pengajuan')->nullable();
            $table->dateTime('tanggal_pemusnahan')->nullable();
            $table->string('status')->default('pending'); // pending | approved
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_foto')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Update approved_at for existing approved records that don't have it set
        DB::table('pemusnahan_obat')
            ->where('status', 'approved')
            ->whereNull('approved_at')
            ->update([
                'approved_at' => DB::raw('updated_at')
            ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pemusnahan_obat');
    }
};
