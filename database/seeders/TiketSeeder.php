<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Tiket;

class TiketSeeder extends Seeder
{
    public function run()
    {
        Tiket::create([
            'maskapai' => 'Garuda Indonesia',
            'bandara_asal' => 'Soekarno-Hatta',
            'bandara_tujuan' => 'Ngurah Rai',
            'tanggal_keberangkatan' => now()->addDays(10),
            'jam_keberangkatan' => '14:00',
            'harga' => 1,
            'stok' => 10,
            'status' => 'tersedia',
            'gambar' => 'garuda.png'
        ]);
    }
}
