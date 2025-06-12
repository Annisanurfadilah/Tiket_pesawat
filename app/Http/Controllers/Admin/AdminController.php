<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Produk;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    // Dashboard Admin
    public function dashboard()
    {
        $totalPelanggan = User::where('role', 'pelanggan')->count();
        $totalProduk = Produk::count();
        $totalPesanan = Pesanan::count();
        $pesananPending = Pesanan::where('status_pesanan', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalPelanggan', 
            'totalProduk', 
            'totalPesanan', 
            'pesananPending'
        ));
    }

    // Kelola Pelanggan
    public function pelanggan()
    {
        $pelanggan = User::where('role', 'pelanggan')->paginate(10);
        return view('admin.pelanggan.index', compact('pelanggan'));
    }

}