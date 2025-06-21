<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Tiket;
use Illuminate\Http\Request;

class AdminPesananController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    // Menampilkan daftar semua pesanan
    public function index(Request $request)
    {
        $query = Pesanan::with(['user', 'tiket']);

        // Filter berdasarkan status pesanan
        if ($request->filled('status_pesanan')) {
            $query->where('status_pesanan', $request->status_pesanan);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // Search berdasarkan kode booking atau nama pelanggan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_booking', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $pesanan = $query->latest()->paginate(10); // Urutkan dari terbaru

        return view('admin.pesanan.index', compact('pesanan'));
    }

    // Menampilkan detail pesanan
    public function show(Pesanan $pesanan)
    {
        // Pastikan relasi user dan tiket di-load
        $pesanan->load('user', 'tiket');
        return view('admin.pesanan.show', compact('pesanan'));
    }

    // Form edit status pesanan/pembayaran (opsional, bisa juga di update langsung dari index)
    public function edit(Pesanan $pesanan)
    {
        $pesanan->load('user', 'tiket');
        return view('admin.pesanan.edit', compact('pesanan'));
    }


    // Mengupdate status pesanan atau pembayaran oleh admin
    public function update(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status_pesanan' => 'required|in:menunggu_pembayaran,diproses,selesai,dibatalkan',
            'status_pembayaran' => 'required|in:pending,paid,failed,refunded',
            // 'catatan_admin' => 'nullable|string', // Kolom tambahan jika diperlukan
        ]);

        $oldStatusPesanan = $pesanan->status_pesanan;
        $oldStatusPembayaran = $pesanan->status_pembayaran;

        $pesanan->update([
            'status_pesanan' => $request->status_pesanan,
            'status_pembayaran' => $request->status_pembayaran,
            // 'catatan_admin' => $request->catatan_admin,
        ]);

        // Logic untuk mengurangi/mengembalikan stok tiket berdasarkan perubahan status
        $tiket = $pesanan->tiket;

        // Jika pesanan berubah menjadi 'dibatalkan' dari status sebelumnya yang bukan 'dibatalkan'
        if ($pesanan->status_pesanan === 'dibatalkan' && $oldStatusPesanan !== 'dibatalkan') {
            if ($tiket) {
                $tiket->increment('stok', $pesanan->jumlah_tiket);
            }
        }
        // Jika pesanan berubah menjadi 'diproses'/'selesai' dan sebelumnya belum diproses/selesai, serta stok belum dikurangi
        // Asumsi stok dikurangi saat pembayaran berhasil melalui webhook. Ini sebagai fallback atau manual.
        elseif (in_array($pesanan->status_pesanan, ['diproses', 'selesai']) && !in_array($oldStatusPesanan, ['diproses', 'selesai'])) {
             // Pastikan stok belum berkurang (misal jika pembayaran manual)
             if ($tiket && $tiket->stok >= $pesanan->jumlah_tiket) {
                 $tiket->decrement('stok', $pesanan->jumlah_tiket);
             }
        }


        return redirect()->route('admin.pesanan.index')->with('success', 'Status pesanan berhasil diperbarui!');
    }

    // Menghapus pesanan
    public function destroy(Pesanan $pesanan)
    {
        // Opsional: kembalikan stok tiket jika pesanan dibatalkan/dihapus
        if ($pesanan->status_pesanan !== 'dibatalkan' && $pesanan->status_pembayaran !== 'failed') {
            $tiket = $pesanan->tiket;
            if ($tiket) {
                $tiket->increment('stok', $pesanan->jumlah_tiket);
            }
        }
        $pesanan->delete();
        return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dihapus!');
    }
}