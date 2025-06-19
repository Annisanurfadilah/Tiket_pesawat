<?php

namespace App\Http\Controllers; // Pastikan namespace ini benar

use Illuminate\Http\Request;
use App\Models\Tiket; // Impor model Tiket jika sudah ada

class TiketController extends Controller
{
    /**
     * Menampilkan daftar semua tiket yang tersedia untuk publik.
     */
    public function index()
    {
        // Ambil semua tiket atau paginasi tiket yang tersedia
        $tikets = Tiket::where('status', 'tersedia')->paginate(10); // Contoh query
        return view('public.tiket.index', compact('tikets')); // Pastikan view ini ada
    }

    /**
     * Menampilkan detail satu tiket.
     */
    public function show(Tiket $tiket) // Laravel akan otomatis menginject model Tiket berdasarkan ID
    {
        return view('public.tiket.show', compact('tiket')); // Pastikan view ini ada
    }

    /**
     * Menampilkan formulir pemesanan untuk tiket tertentu.
     */
    public function pesan(Tiket $tiket)
    {
        // Periksa stok atau kondisi lain sebelum pemesanan
        if ($tiket->stok <= 0) {
            return redirect()->back()->with('error', 'Tiket ini sudah habis.');
        }
        return view('public.tiket.pesan', compact('tiket')); // Pastikan view ini ada
    }

    /**
     * Memproses pemesanan tiket dari publik.
     */
    public function prosesPersan(Request $request, Tiket $tiket)
    {
        // Validasi input (jumlah tiket, dll)
        $request->validate([
            'jumlah_tiket' => 'required|integer|min:1|max:' . $tiket->stok,
            // Tambahkan validasi lain seperti data penumpang jika ada
        ]);

        // LOGIKA PEMESANAN:
        // 1. Hitung total harga
        // 2. Buat entri pesanan di database (mungkin status 'menunggu_pembayaran')
        // 3. Kurangi stok tiket
        // 4. Redirect ke halaman konfirmasi atau pembayaran (misal Midtrans)

        // Contoh sederhana (sesuaikan dengan alur aplikasi Anda):
        $totalHarga = $request->jumlah_tiket * $tiket->harga;

        // Jika user belum login, Anda bisa mengarahkan ke login/register
        // Atau handle pesanan sebagai "guest" untuk sementara sebelum login/register
        if (!auth()->check()) {
            return redirect()->route('login')->with('info', 'Silakan login atau daftar untuk melanjutkan pemesanan.');
        }

        $user = auth()->user();

        // Membuat pesanan (pastikan Anda memiliki model Pesanan dan relasi yang benar)
        $pesanan = $user->pesanan()->create([
            'tiket_id' => $tiket->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga' => $totalHarga,
            'kode_booking' => 'PBK-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)), // Contoh kode booking
            'status_pesanan' => 'menunggu_pembayaran',
            'status_pembayaran' => 'pending',
            // ... kolom lain yang relevan
        ]);

        $tiket->decrement('stok', $request->jumlah_tiket);

        return redirect()->route('tiket.konfirmasi')->with('success', 'Pemesanan berhasil dibuat! Silakan lanjutkan pembayaran.');
    }

    /**
     * Menampilkan halaman konfirmasi setelah pemesanan.
     */
    public function konfirmasi()
    {
        // Anda mungkin ingin menampilkan detail pesanan terakhir yang baru dibuat
        return view('public.tiket.konfirmasi'); // Pastikan view ini ada
    }

    /**
     * Mengambil data bandara (biasanya untuk fitur autocomplete/pencarian).
     */
    public function getBandara(Request $request)
    {
        // Contoh: ambil data bandara dari tabel `bandara` atau array statis
        // Sesuaikan dengan sumber data bandara Anda
        $search = $request->get('term');
        $bandara = [
            ['id' => 'CGK', 'text' => 'Soekarno Hatta (CGK)'],
            ['id' => 'SUB', 'text' => 'Juanda (SUB)'],
            ['id' => 'DPS', 'text' => 'Ngurah Rai (DPS)'],
            ['id' => 'HLP', 'text' => 'Halim Perdanakusuma (HLP)'],
        ];

        $filteredBandara = array_filter($bandara, function($item) use ($search) {
            return stripos($item['text'], $search) !== false;
        });

        return response()->json(array_values($filteredBandara));
    }
}