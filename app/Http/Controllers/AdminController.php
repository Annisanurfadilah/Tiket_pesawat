<?php

namespace App\Http\Controllers;

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

    // Kelola Produk
    public function produk()
    {
        $produk = Produk::paginate(10);
        return view('admin.produk.index', compact('produk'));
    }

    public function createProduk()
    {
        return view('admin.produk.create');
    }

    public function storeProduk(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        Produk::create($request->all());

        return redirect()->route('admin.produk')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function editProduk($id)
    {
        $produk = Produk::findOrFail($id);
        return view('admin.produk.edit', compact('produk'));
    }

    public function updateProduk(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $produk = Produk::findOrFail($id);
        $produk->update($request->all());

        return redirect()->route('admin.produk')->with('success', 'Produk berhasil diupdate!');
    }

    public function deleteProduk($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('admin.produk')->with('success', 'Produk berhasil dihapus!');
    }

    // Kelola Pesanan
    public function pesanan()
    {
        $pesanan = Pesanan::with('user')->paginate(10);
        return view('admin.pesanan.index', compact('pesanan'));
    }

    public function updateStatusPesanan(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update([
            'status_pesanan' => $request->status_pesanan
        ]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diupdate!');
    }
}
