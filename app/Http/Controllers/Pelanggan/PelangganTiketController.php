<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Pesanan; // Import model Pesanan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Menggunakan Auth Facade
use Illuminate\Support\Str; // Untuk Str::upper dan Str::random

class PelangganTiketController extends Controller
{
    /**
     * Menampilkan daftar semua tiket yang tersedia untuk publik.
     */
    public function index()
    {
        $tikets = Tiket::where('status', 'tersedia')->paginate(10);
        return view('pelanggan.tiketpelanggan.index', compact('tikets'));
    }

    /**
     * Menampilkan detail satu tiket.
     */
    public function show(Tiket $tiket)
    {
        return view('pelanggan.tiketpelanggan.show', compact('tiket'));
    }

    /**
     * Menampilkan formulir pemesanan untuk tiket tertentu.
     */
    public function pesan(Tiket $tiket)
    {
        if ($tiket->stok <= 0) {
            return redirect()->back()->with('error', 'Tiket ini sudah habis.');
        }
        return view('pelanggan.tiketpelanggan.pesan', compact('tiket'));
    }

    /**
     * Memproses pemesanan tiket dari publik.
     */
    public function prosesPersan(Request $request, Tiket $tiket)
    {
        // 1. Validasi Input (termasuk detail pemesan)
        $request->validate([
            'jumlah_tiket' => 'required|integer|min:1|max:' . $tiket->stok,
            'nama_pemesan' => 'required|string|max:255',
            'email' => 'required|email|max:255', // Nama input di form adalah 'email'
            'no_telepon' => 'required|string|max:20', // Nama input di form adalah 'no_telepon'
            'agree_terms' => 'required|accepted', // Pastikan user menyetujui syarat & ketentuan
            // 'alamat_pemesan' tidak ada di form Anda, jadi tidak divalidasi di sini.
        ]);

        // 2. Periksa Login
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Silakan login atau daftar untuk melanjutkan pemesanan.');
        }

        $user = Auth::user();

        // 3. Hitung Total Harga
        $totalHarga = $request->jumlah_tiket * $tiket->harga;

        // 4. Buat Entri Pesanan di Database (Hanya data yang ada di database)
        $pesanan = Pesanan::create([
            'user_id' => $user->id,
            'tiket_id' => $tiket->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga' => $totalHarga,
            // 'kode_booking', 'status_pesanan', 'status_pembayaran' akan diisi otomatis oleh Model::boot()
            // Detail pemesan TIDAK DISIMPAN KE DATABASE sesuai permintaan.
        ]);

        // 5. Kurangi Stok Tiket
        $tiket->decrement('stok', $request->jumlah_tiket);

        // 6. Simpan Data Konfirmasi ke Session Flash (Termasuk detail pemesan)
        // Data ini akan digunakan di halaman konfirmasi saja.
        $dataKonfirmasi = [
            'pesanan_id' => $pesanan->id, // ID pesanan yang baru dibuat (dari DB)
            'tiket' => $tiket, // Objek tiket (dari DB)
            'jumlah_tiket' => $request->jumlah_tiket, // Dari form
            'nama_pemesan' => $request->nama_pemesan, // Dari form
            'email_pemesan' => $request->email, // Dari form (nama input 'email')
            'telepon_pemesan' => $request->no_telepon, // Dari form (nama input 'no_telepon')
            'alamat_pemesan' => 'Tidak Tersedia di Form', // Default atau ambil dari user profile jika ada
            'total_harga_display' => $totalHarga, // Total harga (dari perhitungan)
        ];

        // Gunakan session flash untuk meneruskan data ke request berikutnya
        $request->session()->flash('dataKonfirmasi', $dataKonfirmasi);

        // 7. Redirect ke Halaman Konfirmasi
        return redirect()->route('pelanggan.tiket.konfirmasi');
    }

    /**
     * Menampilkan halaman konfirmasi setelah pemesanan.
     */
    public function konfirmasi(Request $request)
    {
        // Ambil data konfirmasi dari session flash
        $dataKonfirmasi = $request->session()->get('dataKonfirmasi');

        // Jika tidak ada data di session (misal diakses langsung atau setelah reload)
        if (!$dataKonfirmasi) {
            // Redirect ke halaman daftar pesanan pelanggan atau dashboard
            return redirect()->route('pelanggan.pesanan.index')
                             ->with('error', 'Tidak ada data konfirmasi yang tersedia. Silakan buat pesanan baru.');
        }

        // Ambil objek model Pesanan yang baru dibuat berdasarkan ID dari session
        // Kita tetap memerlukan ini untuk mendapatkan kode_booking dan total_harga yang disimpan di DB
        $pesananModel = Pesanan::find($dataKonfirmasi['pesanan_id']);

        if (!$pesananModel) {
            // Ini seharusnya tidak terjadi jika alurnya benar, tapi sebagai fallback
            return redirect()->route('pelanggan.pesanan.index')
                             ->with('error', 'Pesanan tidak ditemukan.');
        }

        // Siapkan data untuk view, sesuai dengan yang diharapkan oleh Blade Anda
        // Detail pemesan diambil dari $dataKonfirmasi (dari session)
        $pemesananUntukBlade = [
            'tiket' => $dataKonfirmasi['tiket'],
            'jumlah_tiket' => $dataKonfirmasi['jumlah_tiket'],
            'nama_pemesan' => $dataKonfirmasi['nama_pemesan'],
            'email_pemesan' => $dataKonfirmasi['email_pemesan'],
            'telepon_pemesan' => $dataKonfirmasi['telepon_pemesan'],
            'alamat_pemesan' => $dataKonfirmasi['alamat_pemesan'],
            'total_harga' => $pesananModel->total_harga, // Total harga diambil dari model Pesanan DB
        ];

        return view('pelanggan.tiketpelanggan.konfirmasi', [
            'pemesanan' => $pemesananUntukBlade, // Data pemesanan (tiket + detail pemesan dari session)
            'pesananModel' => $pesananModel,      // Objek model Pesanan (dari DB)
        ]);
    }

    /**
     * Mengambil data bandara (biasanya untuk fitur autocomplete/pencarian).
     */
    public function getBandara(Request $request)
    {
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