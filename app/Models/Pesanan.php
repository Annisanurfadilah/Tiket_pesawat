<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'user_id',
        'tiket_id',
        'jumlah_tiket',
        'kode_booking',
        'total_harga',
        'midtrans_transaction_id',
        'midtrans_transaction_status',
        'status_pembayaran',
        'bukti_pembayaran',
        'url_pembayaran_midtrans',
        'status_pesanan',
        // Tidak ada nama_pemesan, email_pemesan, dll. di sini
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            if (empty($pesanan->kode_booking)) {
                do {
                    $kodeBooking = 'BK-' . strtoupper(Str::random(8));
                } while (Pesanan::where('kode_booking', $kodeBooking)->exists());
                $pesanan->kode_booking = $kodeBooking;
            }
            if (empty($pesanan->status_pesanan)) {
                $pesanan->status_pesanan = 'menunggu_pembayaran';
            }
            if (empty($pesanan->status_pembayaran)) {
                $pesanan->status_pembayaran = 'pending';
            }
        });
    }

    public function getHargaTiketAttribute(): ?float
    {
        return $this->tiket->harga ?? null;
    }

    public function getFormattedTotalHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function isPaid(): bool
    {
        return $this->status_pembayaran === 'paid';
    }

    public function isCompleted(): bool
    {
        return $this->status_pesanan === 'selesai';
    }

    public function isCancelled(): bool
    {
        return $this->status_pesanan === 'dibatalkan';
    }

    public function isPendingPayment(): bool
    {
        return $this->status_pembayaran === 'pending' || $this->status_pesanan === 'menunggu_pembayaran';
    }

    public function isFailed(): bool
    {
        return in_array($this->status_pembayaran, ['failed', 'expired', 'cancelled']);
    }
}