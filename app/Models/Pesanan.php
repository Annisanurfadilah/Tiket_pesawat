<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str; // Untuk Str::random() atau Str::uuid()

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan'; // Pastikan nama tabel benar

    protected $fillable = [
        'user_id', // Ganti 'pelanggan_id' menjadi 'user_id' sesuai foreignId di migrasi
        'tiket_id',
        'jumlah_tiket', // Tambahkan
        'kode_booking', // Tambahkan
        'total_harga', // Tambahkan
        'midtrans_transaction_id', // Tambahkan
        'midtrans_transaction_status', // Tambahkan
        'status_pembayaran', // Tambahkan
        'bukti_pembayaran', // Tambahkan
        'url_pembayaran_midtrans', // Tambahkan
        'status_pesanan', // Tambahkan
        // 'rute', // Hapus jika data diambil dari tiket_id
        // 'harga', // Hapus jika data diambil dari tiket_id
        // 'maskapai', // Hapus jika data diambil dari tiket_id
        // 'jumlah_penumpang', // Ganti dengan jumlah_tiket, jika jumlah_penumpang adalah detail per orang, perlu tabel terpisah
    ];

    protected $casts = [
        'total_harga' => 'decimal:2', // Sesuaikan cast untuk total_harga
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user (customer) that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id'); // Sesuaikan dengan user_id
    }

    /**
     * Get the tiket that belongs to the order.
     */
    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id');
    }

    /**
     * Generate kode booking unik saat membuat pesanan baru
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            // Pastikan kode booking digenerate hanya jika belum ada (misal dari form)
            if (empty($pesanan->kode_booking)) {
                $pesanan->kode_booking = 'BK-' . strtoupper(Str::random(8)); // Contoh: BK-ABCDEFGH
                // Pastikan unik. Anda bisa menambahkan loop do-while untuk menjamin keunikan
                // atau menggunakan UUID jika lebih disukai
            }
            // Set status awal jika belum diset
            if (empty($pesanan->status_pesanan)) {
                $pesanan->status_pesanan = 'menunggu_pembayaran';
            }
            if (empty($pesanan->status_pembayaran)) {
                $pesanan->status_pembayaran = 'pending';
            }
        });
    }

    /**
     * Accessor untuk harga tiket (jika perlu ditampilkan)
     * Ambil dari relasi tiket, atau dari kolom 'harga' jika disimpan sebagai snapshot
     */
    public function getHargaTiketAttribute(): ?float
    {
        return $this->tiket->harga ?? null; // Mengambil harga dari relasi tiket
        // Atau jika disimpan di kolom 'harga' di tabel pesanan:
        // return $this->harga;
    }

    /**
     * Format total harga untuk tampilan
     */
    public function getFormattedTotalHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    /**
     * Determine if the payment is successful.
     */
    public function isPaid(): bool
    {
        return $this->status_pembayaran === 'paid';
    }

    /**
     * Determine if the order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status_pesanan === 'selesai';
    }

    /**
     * Determine if the order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status_pesanan === 'dibatalkan';
    }
}