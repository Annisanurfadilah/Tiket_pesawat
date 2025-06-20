<?php

namespace App\Http\Controllers\Pelanggan;

use App\Models\Produk;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Dashboard Pelanggan
    public function dashboard()
    {
        $totalPesanan = Pesanan::where('user_id', Auth::id())->count();
        $pesananPending = Pesanan::where('user_id', Auth::id())
                                ->where('status_pesanan', 'pending')
                                ->count();
        $pesananSelesai = Pesanan::where('user_id', Auth::id())
                                ->where('status_pesanan', 'selesai')
                                ->count();

        return view('pelanggan.dashboard', compact(
            'totalPesanan', 
            'pesananPending', 
            'pesananSelesai'
        ));
    }

    // Lihat Produk
    public function produk()
    {
        $produk = Produk::where('status', 'tersedia')->paginate(12);
        return view('pelanggan.produk.index', compact('produk'));
    }

    // Buat Pesanan
    public function createPesanan($produkId)
    {
        $produk = Produk::findOrFail($produkId);
        return view('pelanggan.pesanan.create', compact('produk'));
    }

    public function storePesanan(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        $produk = Produk::findOrFail($request->produk_id);
        $totalHarga = $produk->harga * $request->jumlah;

        Pesanan::create([
            'user_id' => Auth::id(),
            'nomor_pesanan' => Pesanan::generateNomorPesanan(),
            'total_harga' => $totalHarga,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('pelanggan.pesanan.index')->with('success', 'Pesanan berhasil dibuat!');
    }

    // Lihat Pesanan
    public function pesanan()
    {
        $pesanan = Pesanan::where('user_id', Auth::id())->paginate(10);
        return view('pelanggan.pesanan.index', compact('pesanan'));
    }
}
