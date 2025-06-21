<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pelanggan yang membuat pesanan

            // Foreign key ke tabel tikets
            $table->foreignId('tiket_id')->nullable()->constrained()->onDelete('set null'); // nullable jika tiket bisa dihapus
            $table->integer('jumlah_tiket'); // Jumlah tiket yang dipesan

            $table->string('kode_booking')->unique(); // Kode booking yang unik

            $table->decimal('total_harga', 15, 2); // Total harga pesanan (jumlah_tiket * harga_per_tiket)

            // Kolom untuk integrasi Midtrans
            $table->string('midtrans_transaction_id')->nullable()->unique();
            $table->string('midtrans_transaction_status')->default('pending'); // Status dari Midtrans
            $table->string('status_pembayaran')->default('pending'); // Status pembayaran internal (pending, paid, failed, refunded)
            $table->string('bukti_pembayaran')->nullable(); // Path ke bukti pembayaran jika ada (misal untuk manual verification)
            $table->text('url_pembayaran_midtrans')->nullable(); // URL Snap/VA dari Midtrans

            // Status pesanan internal aplikasi
            $table->string('status_pesanan')->default('menunggu_pembayaran'); // (menunggu_pembayaran, diproses, selesai, dibatalkan)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};