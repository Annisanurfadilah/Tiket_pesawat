<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'maskapai',
        'bandara_asal',
        'bandara_tujuan',
        'tanggal_keberangkatan',
        'jam_keberangkatan',
        'harga',
        'status',
        'gambar',
    ];

    protected $casts = [
        'tanggal_keberangkatan' => 'date',
        'jam_keberangkatan' => 'datetime:H:i',
        'harga' => 'decimal:2',
    ];

    /**
     * Get all pesanan for this tiket
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    /**
     * Scope untuk tiket yang tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    /**
     * Scope untuk filter berdasarkan rute
     */
    public function scopeFilterRute($query, $asal = null, $tujuan = null)
    {
        if ($asal) {
            $query->where('bandara_asal', 'like', '%' . $asal . '%');
        }
        
        if ($tujuan) {
            $query->where('bandara_tujuan', 'like', '%' . $tujuan . '%');
        }
        
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeFilterTanggal($query, $tanggal = null)
    {
        if ($tanggal) {
            $query->whereDate('tanggal_keberangkatan', $tanggal);
        }
        
        return $query;
    }

    /**
     * Get formatted harga
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    /**
     * Get rute lengkap
     */
    public function getRuteAttribute(): string
    {
        return $this->bandara_asal . ' - ' . $this->bandara_tujuan;
    }

    /**
     * Check if tiket is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'tersedia';
    }

    /**
     * Get total pesanan for this tiket
     */
    public function getTotalPesananAttribute(): int
    {
        return $this->pesanan()->count();
    }

    /**
     * Get total penumpang yang sudah memesan
     */
    public function getTotalPenumpangAttribute(): int
    {
        return $this->pesanan()->sum('jumlah_penumpang');
    }
}