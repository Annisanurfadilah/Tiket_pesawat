<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    /**
     * Menampilkan daftar tiket untuk pengguna
     */
    public function index(Request $request)
    {
        $query = Tiket::where('status', 'tersedia');

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maskapai', 'like', "%{$search}%")
                  ->orWhere('bandara_asal', 'like', "%{$search}%")
                  ->orWhere('bandara_tujuan', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan bandara asal
        if ($request->has('bandara_asal') && !empty($request->bandara_asal)) {
            $query->where('bandara_asal', 'like', "%{$request->bandara_asal}%");
        }

        // Filter berdasarkan bandara tujuan
        if ($request->has('bandara_tujuan') && !empty($request->bandara_tujuan)) {
            $query->where('bandara_tujuan', 'like', "%{$request->bandara_tujuan}%");
        }

        // Filter berdasarkan tanggal keberangkatan
        if ($request->has('tanggal_keberangkatan') && !empty($request->tanggal_keberangkatan)) {
            $query->whereDate('tanggal_keberangkatan', $request->tanggal_keberangkatan);
        }

        // Filter berdasarkan range harga
        if ($request->has('harga_min') && !empty($request->harga_min)) {
            $query->where('harga', '>=', $request->harga_min);
        }

        if ($request->has('harga_max') && !empty($request->harga_max)) {
            $query->where('harga', '<=', $request->harga_max);
        }

        // Urutkan berdasarkan parameter
        $sortBy = $request->get('sort_by', 'tanggal_keberangkatan');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['harga', 'tanggal_keberangkatan', 'jam_keberangkatan'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('tanggal_keberangkatan', 'asc');
        }

        $tikets = $query->paginate(12)->withQueryString();

        return view('tiket.index', compact('tikets'));
    }

    /**
     * Menampilkan detail tiket
     */
    public function show(Tiket $tiket)
    {
        // Pastikan tiket masih tersedia
        if ($tiket->status !== 'tersedia') {
            return redirect()->route('tiket.index')
                           ->with('error', 'Tiket tidak tersedia!');
        }

        return view('tiket.show', compact('tiket'));
    }

    /**
     * Menampilkan form pemesanan tiket
     */
    public function pesan(Tiket $tiket)
    {
        // Pastikan tiket masih tersedia
        if ($tiket->status !== 'tersedia') {
            return redirect()->route('tiket.index')
                           ->with('error', 'Tiket tidak tersedia!');
        }

        return view('tiket.pesan', compact('tiket'));
    }

    /**
     * Memproses pemesanan tiket
     */
    public function prosesPersan(Request $request, Tiket $tiket)
    {
        // Validasi data pemesanan
        $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_telepon' => 'required|string|max:20',
            'jumlah_tiket' => 'required|integer|min:1|max:10'
        ]);

        // Pastikan tiket masih tersedia
        if ($tiket->status !== 'tersedia') {
            return redirect()->route('tiket.index')
                           ->with('error', 'Tiket tidak tersedia!');
        }

        // Hitung total harga
        $totalHarga = $tiket->harga * $request->jumlah_tiket;

        // Di sini Anda bisa menambahkan logic untuk:
        // 1. Menyimpan data pemesanan ke database
        // 2. Mengirim email konfirmasi
        // 3. Mengintegrasikan dengan payment gateway
        // 4. dll.

        // Contoh: Simpan ke session untuk sementara
        session([
            'pemesanan' => [
                'tiket_id' => $tiket->id,
                'nama_pemesan' => $request->nama_pemesan,
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'jumlah_tiket' => $request->jumlah_tiket,
                'total_harga' => $totalHarga,
                'tiket' => $tiket
            ]
        ]);

        return redirect()->route('tiket.konfirmasi')
                       ->with('success', 'Data pemesanan berhasil diproses!');
    }

    /**
     * Menampilkan halaman konfirmasi pemesanan
     */
    public function konfirmasi()
    {
        $pemesanan = session('pemesanan');
        
        if (!$pemesanan) {
            return redirect()->route('tiket.index')
                           ->with('error', 'Data pemesanan tidak ditemukan!');
        }

        return view('tiket.konfirmasi', compact('pemesanan'));
    }

    /**
     * API untuk mendapatkan daftar bandara (untuk autocomplete)
     */
    public function getBandara(Request $request)
    {
        $term = $request->get('term', '');
        
        $bandaraAsal = Tiket::select('bandara_asal as nama')
                           ->where('bandara_asal', 'like', "%{$term}%")
                           ->distinct()
                           ->get();
                           
        $bandaraTujuan = Tiket::select('bandara_tujuan as nama')
                             ->where('bandara_tujuan', 'like', "%{$term}%")
                             ->distinct()
                             ->get();
        
        $bandara = $bandaraAsal->merge($bandaraTujuan)
                              ->unique('nama')
                              ->pluck('nama')
                              ->toArray();
        
        return response()->json($bandara);
    }
}