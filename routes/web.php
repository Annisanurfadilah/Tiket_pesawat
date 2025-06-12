<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPelangganController;
use App\Http\Controllers\Admin\AdminManajemenController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\TiketController;
use App\Http\Controllers\Pelanggan\PelangganDashboardController;
use App\Http\Controllers\Pelanggan\PelangganProfileController;
use App\Http\Controllers\Pelanggan\PesananController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ==========================
// AUTHENTICATION ROUTES
// ==========================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================
// ADMIN ROUTES
// ==========================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pelanggan
    Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan');
    Route::get('/pelanggan/{id}', [AdminPelangganController::class, 'show'])->name('pelanggan.detail');
    Route::patch('/pelanggan/{id}/toggle-status', [AdminPelangganController::class, 'toggleStatus'])->name('pelanggan.toggle-status');
    Route::delete('/pelanggan/{id}', [AdminPelangganController::class, 'destroy'])->name('pelanggan.hapus');

    Route::middleware(['auth', 'pelanggan'])->group(function () {
    Route::get('/pelanggan/dashboard', [PelangganDashboardController::class, 'index'])->name('pelanggan.dashboard');
    });


    // Manajemen Admin
    Route::resource('manajemen-admin', AdminManajemenController::class);

    // Tiket (misalnya untuk manajemen tiket)
    Route::resource('tiket', TiketController::class);

    // Profile Admin
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});

// ==========================
// PELANGGAN ROUTES
// ==========================
Route::middleware(['auth'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::resource('pesanan', PesananController::class);


    // Dashboard
    Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');

   

   Route::get('/pelanggan/pesanan/create/{tiketId}', [PesananController::class, 'create'])->name('pelanggan.pesanan.create');

    Route::prefix('tiket')->name('tiket.')->group(function () {
    Route::get('/', [TiketController::class, 'index'])->name('index');
    Route::get('/show/{tiket}', [TiketController::class, 'show'])->name('show');
    Route::get('/pesan/{tiket}', [TiketController::class, 'pesan'])->name('pesan');
    Route::post('/pesan/{tiket}', [TiketController::class, 'prosesPersan'])->name('proses-pesan');
    Route::get('/konfirmasi', [TiketController::class, 'konfirmasi'])->name('konfirmasi');
    Route::get('/api/bandara', [TiketController::class, 'getBandara'])->name('api.bandara');
});

 Route::prefix('pesanan')->name('pesanan.')->group(function () {
        Route::get('/', [PesananController::class, 'index'])->name('index');
        Route::get('pelanggan/pesanan/create', [PesananController::class, 'create'])->name('pelanggan.pesanan.create');
        Route::post('/', [PesananController::class, 'store'])->name('store');
        Route::get('/{pesanan}', [PesananController::class, 'show'])->name('show');
        Route::get('/history', [PesananController::class, 'history'])->name('history');
    });


// Rute untuk pelanggan (middleware auth & role bisa ditambahkan sesuai kebutuhan)
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    Route::get('pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('pesanan/create/{tiket}', [PesananController::class, 'create'])->name('pesanan.create');
    Route::post('pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::get('pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
    Route::get('riwayat-pesanan', [PesananController::class, 'history'])->name('pesanan.history');
});


    // Profile
    Route::get('/profile', [PelangganProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [PelangganProfileController::class, 'update'])->name('profile.update');
});
