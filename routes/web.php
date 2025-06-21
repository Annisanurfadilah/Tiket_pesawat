<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPelangganController;
use App\Http\Controllers\Admin\AdminManajemenController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminTiketController;
use App\Http\Controllers\Admin\AdminPesananController;

use App\Http\Controllers\Pelanggan\PelangganTiketController;
use App\Http\Controllers\Pelanggan\PelangganDashboardController;
use App\Http\Controllers\Pelanggan\PelangganProfileController;
use App\Http\Controllers\Pelanggan\PelangganPesananController;

use App\Http\Controllers\MidtransCallbackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================
// PUBLIC ROUTES (No Auth Required)
// ==========================

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Route::prefix('tiket')->name('tiket.')->group(function () {
//     Route::get('/', [PelangganTiketController::class, 'index'])->name('index'); // Browse all tickets
//     Route::get('/show/{tiket}', [PelangganTiketController::class, 'show'])->name('show'); // Show single ticket detail
//     Route::get('/pesan/{tiket}', [PelangganTiketController::class, 'pesan'])->name('pesan'); // Form to start booking for a specific ticket (public view)
//     Route::post('/proses-pesan/{tiket}', [PelangganTiketController::class, 'prosesPersan'])->name('proses-pesan'); // Process public booking
//     Route::get('/konfirmasi', [PelangganTiketController::class, 'konfirmasi'])->name('konfirmasi'); // Public booking confirmation
//     Route::get('/api/bandara', [PelangganTiketController::class, 'getBandara'])->name('api.bandara'); // API for airport data
// });

// ==========================
// AUTHENTICATION ROUTES
// ==========================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // Renamed to avoid conflict with GET /login
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==========================
// ADMIN ROUTES
// Applies 'auth' and 'admin' middleware to all routes in this group
// ==========================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pelanggan
    Route::get('/pelanggan', [AdminPelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/{id}', [AdminPelangganController::class, 'show'])->name('pelanggan.show');
    Route::patch('/pelanggan/{id}/toggle-status', [AdminPelangganController::class, 'toggleStatus'])->name('pelanggan.toggle-status');
    Route::delete('/pelanggan/{id}', [AdminPelangganController::class, 'destroy'])->name('pelanggan.destroy');

    // Manajemen Admin (Resource Controller)
    Route::resource('manajemen-admin', AdminManajemenController::class);

    // Tiket (Management for Admin - Resource Controller)
    Route::resource('tiket', AdminTiketController::class);

    // Profile Admin
    Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');

    // Manajemen Pesanan Admin (Resource Controller for orders)
    Route::resource('pesanan', AdminPesananController::class);

});


// ==========================
// PELANGGAN ROUTES
// Applies 'auth' and 'pelanggan' middleware to all routes in this group
// The nested 'pelanggan' group in ADMIN ROUTES was removed.
// ==========================
Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth', 'pelanggan'])->group(function () {

    // Dashboard Pelanggan
    Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');

    // --- PESANAN ROUTES for Pelanggan ---
    Route::resource('pesanan', PelangganPesananController::class)->except(['edit', 'update', 'destroy']);

    Route::get('/pesanan/create/{tiket}', [PelangganPesananController::class, 'createWithTiket'])->name('pesanan.create.specific');
    Route::post('/pesanan/{pesanan}/cancel', [PelangganPesananController::class, 'cancel'])->name('pesanan.cancel');
    // New route for re-attempting payment
    Route::post('/pesanan/{pesanan}/retry-payment', [PelangganPesananController::class, 'retryPayment'])->name('pesanan.retry-payment');


    // --- TIKET Browse ROUTES for Pelanggan ---
    Route::prefix('tiket')->name('tiket.')->group(function () {
        Route::get('/', [PelangganTiketController::class, 'index'])->name('index');
        Route::get('/show/{tiket}', [PelangganTiketController::class, 'show'])->name('show');
        Route::get('/pesan/{tiket}', [PelangganTiketController::class, 'pesan'])->name('pesan');
        Route::post('/pesan/{tiket}', [PelangganTiketController::class, 'prosesPersan'])->name('proses-pesan');
        Route::get('/konfirmasi', [PelangganTiketController::class, 'konfirmasi'])->name('konfirmasi');
        Route::get('/api/bandara', [PelangganTiketController::class, 'getBandara'])->name('api.bandara');
    });

    // Profile Pelanggan
    Route::get('/profile', [PelangganProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [PelangganProfileController::class, 'update'])->name('profile.update');
});


// ==========================
// MIDTRANS CALLBACK ROUTES - These routes must be accessible by Midtrans
// ==========================
Route::post('/midtrans-callback', [MidtransCallbackController::class, 'callback'])->name('midtrans.callback');

// Callback URLs for user redirection from Midtrans (not the main webhook)
Route::get('/midtrans-finish', [MidtransCallbackController::class, 'finish'])->name('midtrans.finish');
Route::get('/midtrans-unfinish', [MidtransCallbackController::class, 'unfinish'])->name('midtrans.unfinish');
Route::get('/midtrans-error', [MidtransCallbackController::class, 'error'])->name('midtrans.error');