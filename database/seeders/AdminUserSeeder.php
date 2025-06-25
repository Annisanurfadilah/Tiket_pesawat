<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan model User di-import
use Illuminate\Support\Facades\Hash; // Untuk hashing password

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat pengguna Admin jika belum ada
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'nama' => 'Admin Utama',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'), // Ganti 'password' dengan password yang lebih kuat di produksi
                'role' => 'admin',
                'nomor_telepon' => '081234567890',
                'alamat' => 'Jl. Admin Raya No. 1, Bandar Lampung',
                'status' => 'aktif',
            ]);
            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}