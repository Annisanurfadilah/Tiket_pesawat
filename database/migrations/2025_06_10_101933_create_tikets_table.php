<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->string('maskapai');
            $table->string('bandara_asal');
            $table->string('bandara_tujuan');
            $table->date('tanggal_keberangkatan');
            $table->time('jam_keberangkatan');
            $table->decimal('harga', 15, 2);
            $table->string('status')->default('tersedia'); // atau 'habis', dll
            $table->string('gambar')->nullable();
            $table->integer('stok')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tikets');
    }
};
