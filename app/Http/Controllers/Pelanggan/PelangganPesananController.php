<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Tiket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Midtrans\Snap; // Import Midtrans Snap
use Midtrans\Config; // Import Midtrans Config
use Illuminate\Support\Facades\Log; // Import Log facade untuk debugging

class PelangganPesananController extends Controller
{
    public function __construct()
    {
        // Set Midtrans Configuration
        Config::$serverKey = config('midtrans.server_key');
        // Pastikan ini di-cast ke boolean untuk menghindari masalah tipe data
        Config::$isProduction = (bool)config('midtrans.is_production');
        Config::$isSanitized = (bool)config('midtrans.is_sanitized');
        Config::$is3ds = (bool)config('midtrans.is_3ds');
    }

    /**
     * Display a listing of the resource (customer's orders).
     */
    public function index()
    {
        $pesanan = Auth::user()->pesanan()->latest()->paginate(10);
        return view('pelanggan.pesanan.index', compact('pesanan'));
    }

    /**
     * Show the form for creating a new resource (generic, no specific ticket pre-selected).
     */
    public function create()
    {
        $tikets = Tiket::where('stok', '>', 0)->get();
        return view('pelanggan.pesanan.create_select_ticket', compact('tikets'));
    }

    /**
     * Show the form for creating a new resource (with a specific ticket pre-selected).
     */
    public function createWithTiket(Tiket $tiket)
    {
        // Ensure ticket is available and has stock
        if (!$tiket->isAvailable() || $tiket->stok <= 0) {
            return redirect()->back()->with('error', 'Tiket tidak tersedia atau stok habis.');
        }
        return view('pelanggan.pesanan.create', compact('tiket'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tiket_id' => 'required|exists:tikets,id',
            'jumlah_tiket' => 'required|integer|min:1',
        ]);

        $tiket = Tiket::findOrFail($request->tiket_id);

        if ($tiket->stok < $request->jumlah_tiket) {
            return redirect()->back()->with('error', 'Stok tiket tidak mencukupi.');
        }

        $totalHarga = $request->jumlah_tiket * $tiket->harga;

        // Cek jika ada pesanan pending yang sudah ada untuk user dan tiket ini
        $existingPesanan = Pesanan::where('user_id', Auth::id())
                                   ->where('tiket_id', $tiket->id)
                                   ->whereIn('status_pembayaran', ['pending', 'menunggu_pembayaran', 'failed_midtrans_init', 'challenge'])
                                   ->first();

        if ($existingPesanan) {
            return redirect()->route('pelanggan.pesanan.show', $existingPesanan->id)
                             ->with('info', 'Anda sudah memiliki pesanan yang perlu pembayaran untuk tiket ini. Silakan lanjutkan.');
        }

        $pesanan = Pesanan::create([
            'user_id' => Auth::id(),
            'tiket_id' => $tiket->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga' => $totalHarga,
            'status_pesanan' => 'menunggu_pembayaran',
            'status_pembayaran' => 'pending',
        ]);

        // Kurangi stok tiket setelah pesanan berhasil dibuat
        $tiket->decrement('stok', $request->jumlah_tiket);

        return redirect()->route('pelanggan.pesanan.show', $pesanan->id)->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    /**
     * Display the specified resource.
     * Generates a new Snap token if necessary, but doesn't auto-popup.
     */
    public function show(Pesanan $pesanan)
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403); // Forbidden jika bukan pemilik pesanan
        }

        $snapToken = null;

        // Kondisi untuk generate token:
        // 1. Pesanan masih pending/membutuhkan pembayaran (isPendingPayment())
        // 2. Belum ada URL pembayaran Midtrans ATAU ada URL tapi transaksinya sudah expired/failed (untuk retry)
        //    Asumsi: Jika url_pembayaran_midtrans ada, kita bisa cek statusnya dari Midtrans_transaction_status.
        //    Untuk kesederhanaan, kita akan generate ulang jika url_pembayaran_midtrans kosong atau
        //    jika status Midtransnya adalah 'expire'/'cancel'/'deny' dan kita ingin user bisa coba lagi.

        $shouldGenerateNewToken = false;

        // Check if the order is in a state that requires payment
        if ($pesanan->isPendingPayment()) {
            if (empty($pesanan->url_pembayaran_midtrans) ||
                ($pesanan->midtrans_transaction_status === 'expire' ||
                 $pesanan->midtrans_transaction_status === 'cancel' ||
                 $pesanan->midtrans_transaction_status === 'deny' ||
                 $pesanan->midtrans_transaction_status === 'failed'))
            {
                $shouldGenerateNewToken = true;
            } else {
                // If there's an existing URL and it's still "pending" (not expired/failed/canceled)
                // then we can extract the token from the existing URL.
                $urlParts = explode('/', $pesanan->url_pembayaran_midtrans);
                $snapToken = end($urlParts);
            }
        }

        if ($shouldGenerateNewToken) {
            try {
                if (!$pesanan->tiket) {
                    throw new \Exception('Tiket untuk pesanan ini tidak ditemukan.');
                }

                $params = [
                    'transaction_details' => [
                        'order_id' => $pesanan->kode_booking,
                        'gross_amount' => $pesanan->total_harga,
                    ],
                    'customer_details' => [
                        'first_name' => Auth::user()->nama,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->nomor_telepon,
                    ],
                    'item_details' => [
                        [
                            'id' => $pesanan->tiket->id,
                            'price' => $pesanan->tiket->harga,
                            'quantity' => $pesanan->jumlah_tiket,
                            'name' => $pesanan->tiket->maskapai . ' - ' . $pesanan->tiket->bandara_asal . ' to ' . $pesanan->tiket->bandara_tujuan,
                        ]
                    ],
                    'callbacks' => [
                        'finish' => route('midtrans.finish'),
                        'unfinish' => route('midtrans.unfinish'),
                        'error' => route('midtrans.error'),
                    ]
                ];

                $snapToken = Snap::getSnapToken($params);

                // Selalu simpan URL Snap terbaru ke database
                $pesanan->update([
                    'url_pembayaran_midtrans' => 'https://app.sandbox.midtrans.com/snap/v1/pay/' . $snapToken,
                    'midtrans_transaction_status' => 'pending', // Reset status Midtrans saat token baru digenerate
                ]);

                Log::info('Snap Token generated and saved for order: ' . $pesanan->kode_booking);

            } catch (\Exception $e) {
                Log::error('Midtrans Snap generation failed for order ' . $pesanan->id . ': ' . $e->getMessage());
                $pesanan->update([
                    'status_pesanan' => 'gagal',
                    'status_pembayaran' => 'failed_midtrans_init',
                ]);
                return redirect()->back()->with('error', 'Gagal memuat pembayaran: ' . $e->getMessage());
            }
        }

        return view('pelanggan.pesanan.show', compact('pesanan', 'snapToken'));
    }

    /**
     * Redirects to the show page to trigger payment retry.
     */
    public function retryPayment(Pesanan $pesanan)
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow retry if order is in a state that allows it
        if (!($pesanan->isPendingPayment() ||
              $pesanan->status_pembayaran === 'failed' ||
              $pesanan->status_pembayaran === 'expired' ||
              $pesanan->status_pembayaran === 'cancelled' ||
              $pesanan->status_pembayaran === 'challenge' ||
              $pesanan->status_pesanan === 'gagal' ||
              $pesanan->status_pembayaran === 'failed_midtrans_init'))
        {
             return redirect()->back()->with('error', 'Pesanan tidak dalam status yang dapat dicoba ulang pembayarannya.');
        }

        // Force regeneration of new token by clearing the old URL (optional, show() handles it now)
        // If you want to force a new token explicitly, you can set url_pembayaran_midtrans to null here
        // $pesanan->update(['url_pembayaran_midtrans' => null]);

        return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                         ->with('info', 'Mencoba memuat ulang pembayaran. Silakan klik "Bayar Sekarang".');
    }

    /**
     * Show the form for editing the specified resource (uncommon for customer to edit order directly).
     */
    public function edit(Pesanan $pesanan)
    {
        abort(404); // Not found for customer
    }

    /**
     * Update the specified resource in storage (uncommon for customer to update order directly).
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        abort(404); // Not found for customer
    }

    /**
     * Remove the specified resource from storage (uncommon for customer to delete order).
     */
    public function destroy(Pesanan $pesanan)
    {
        abort(404); // Not found for customer
    }

    /**
     * Handle order cancellation by the customer.
     */
    public function cancel(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403);
        }

        if ($pesanan->status_pesanan === 'menunggu_pembayaran' || $pesanan->status_pembayaran === 'pending') {
            $pesanan->update([
                'status_pesanan' => 'dibatalkan',
                'status_pembayaran' => 'cancelled',
            ]);

            // Return stock to ticket if necessary
            // Pastikan ini tidak duplikasi dengan logika di MidtransCallbackController
            if ($pesanan->tiket) {
                $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
                Log::info('Stock incremented due to manual cancellation for order: ' . $pesanan->kode_booking);
            }

            return redirect()->route('pelanggan.pesanan.index')->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan pada status ini.');
    }
}