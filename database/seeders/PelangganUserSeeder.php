<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan model User di-import
use Illuminate\Support\Facades\Hash; // Untuk hashing password

class PelangganUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat contoh beberapa pengguna Pelanggan
        // Anda bisa membuat loop untuk membuat lebih banyak data dummy
        if (!User::where('email', 'pelanggan1@example.com')->exists()) {
            User::create([
                'nama' => 'Pelanggan Satu',
                'email' => 'pelanggan1@example.com',
                'password' => Hash::make('password'),
                'role' => 'pelanggan',
                'nomor_telepon' => '081122334455',
                'alamat' => 'Jl. Pelanggan No. 10, Bandar Lampung',
                'status' => 'aktif',
            ]);
            $this->command->info('Pelanggan user 1 created successfully!');
        } else {
            $this->command->info('Pelanggan user 1 already exists.');
        }

        if (!User::where('email', 'pelanggan2@example.com')->exists()) {
            User::create([
                'nama' => 'Pelanggan Dua',
                'email' => 'pelanggan2@example.com',
                'password' => Hash::make('password'),
                'role' => 'pelanggan',
                'nomor_telepon' => '085566778899',
                'alamat' => 'Jl. Contoh Saja No. 25, Bandar Lampung',
                'status' => 'aktif',
            ]);
            $this->command->info('Pelanggan user 2 created successfully!');
        } else {
            $this->command->info('Pelanggan user 2 already exists.');
        }
    }
}