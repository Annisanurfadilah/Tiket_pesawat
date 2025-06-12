<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'pelanggan_id',
        'rute',
        'harga',
        'maskapai',
        'tiket_id',
        'jumlah_penumpang',
        'kode_booking'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the order
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    /**
     * Get the tiket that belongs to the order
     */
    public function tiket(): BelongsTo
    {
        return $this->belongsTo(Tiket::class, 'tiket_id');
    }

    /**
     * Format harga untuk tampilan
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get order status based on creation date (contoh sederhana)
     */
    public function getStatusAttribute(): string
    {
        // Ini contoh sederhana, Anda bisa menambah kolom status di database
        $daysDiff = $this->created_at->diffInDays(now());
        
        if ($daysDiff < 1) {
            return 'pending';
        } elseif ($daysDiff < 7) {
            return 'confirmed';
        } else {
            return 'completed';
        }
    }

    /**
     * Generate kode booking unik saat membuat pesanan baru
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pesanan) {
            $pesanan->kode_booking = 'BK' . strtoupper(uniqid());
        });
    }
}