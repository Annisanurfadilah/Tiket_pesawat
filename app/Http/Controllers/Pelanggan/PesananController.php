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

class PesananController extends Controller
{
    public function __construct()
    {
        // Set Midtrans Configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
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
        return view('pelanggan.pesanan.create_form', compact('tiket'));
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

        // Check if an existing pending order exists for this user and ticket
        // This prevents duplicate orders if user refreshes page after payment
        $existingPesanan = Pesanan::where('user_id', Auth::id())
                                   ->where('tiket_id', $tiket->id)
                                   ->whereIn('status_pembayaran', ['pending', 'menunggu_pembayaran'])
                                   ->first();

        if ($existingPesanan) {
            return redirect()->route('pelanggan.pesanan.show', $existingPesanan->id)
                             ->with('info', 'Anda sudah memiliki pesanan pending untuk tiket ini. Silakan lanjutkan pembayaran.');
        }

        $pesanan = Pesanan::create([
            'user_id' => Auth::id(),
            'tiket_id' => $tiket->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga' => $totalHarga,
            // 'kode_booking' is generated in Pesanan model's boot method
            'status_pesanan' => 'menunggu_pembayaran',
            'status_pembayaran' => 'pending',
        ]);

        // Reduce ticket stock after successful order creation
        $tiket->decrement('stok', $request->jumlah_tiket);

        // --- Midtrans Integration ---
        try {
            $params = array(
                'transaction_details' => array(
                    'order_id' => $pesanan->kode_booking, // Use kode_booking as Midtrans order ID
                    'gross_amount' => $pesanan->total_harga,
                ),
                'customer_details' => array(
                    'first_name' => Auth::user()->nama,
                    'email' => Auth::user()->email,
                    'phone' => Auth::user()->nomor_telepon,
                    // 'address' => Auth::user()->alamat, // Add if needed
                ),
                'item_details' => [
                    [
                        'id' => $tiket->id,
                        'price' => $tiket->harga,
                        'quantity' => $pesanan->jumlah_tiket,
                        'name' => $tiket->maskapai . ' - ' . $tiket->bandara_asal . ' to ' . $tiket->bandara_tujuan,
                    ]
                ],
                'callbacks' => [
                    'finish' => route('midtrans.finish'),
                    'unfinish' => route('midtrans.unfinish'),
                    'error' => route('midtrans.error'),
                ]
            );

            $snapToken = Snap::getSnapToken($params);
            $pesanan->update([
                'url_pembayaran_midtrans' => 'https://app.sandbox.midtrans.com/snap/v1/pay/' . $snapToken, // Snap URL + Snap Token
                'midtrans_transaction_id' => null, // Will be filled by callback
                'midtrans_transaction_status' => 'pending', // Initial Midtrans status
            ]);

            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)->with('success', 'Pesanan berhasil dibuat. Lanjutkan pembayaran.');

        } catch (\Exception $e) {
            // If Midtrans Snap token generation fails, revert stock and set order status to failed
            $tiket->increment('stok', $request->jumlah_tiket); // Revert stock
            $pesanan->update([
                'status_pesanan' => 'gagal',
                'status_pembayaran' => 'failed_midtrans_init',
            ]);
            return redirect()->back()->with('error', 'Gagal membuat transaksi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pesanan $pesanan)
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403); // Forbidden
        }
        return view('pelanggan.pesanan.show', compact('pesanan'));
    }

    /**
     * Show the form for editing the specified resource (uncommon for customer to edit order directly).
     * This is usually for admin. If customer can edit, define logic here.
     */
    public function edit(Pesanan $pesanan)
    {
        abort(404); // Not found for customer
    }

    /**
     * Update the specified resource in storage (uncommon for customer to update order directly).
     * This is usually for admin. If customer can update, define logic here.
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        abort(404); // Not found for customer
    }

    /**
     * Remove the specified resource from storage (uncommon for customer to delete order).
     * Customers typically cancel, not delete.
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

        // Only allow cancellation if order is in a cancellable state (e.g., 'menunggu_pembayaran', 'pending')
        if ($pesanan->status_pesanan === 'menunggu_pembayaran' || $pesanan->status_pembayaran === 'pending') {
            $pesanan->update([
                'status_pesanan' => 'dibatalkan',
                'status_pembayaran' => 'cancelled',
            ]);

            // Return stock to ticket if necessary
            if ($pesanan->tiket) {
                $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
            }

            return redirect()->route('pelanggan.pesanan.index')->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan pada status ini.');
    }

    /**
     * Re-attempt payment for a pending order.
     */
    public function retryPayment(Pesanan $pesanan)
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403);
        }

        // Only allow re-attempt if the order is still pending payment
        if ($pesanan->status_pembayaran === 'pending' || $pesanan->status_pembayaran === 'failed_midtrans_init') {
            try {
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $pesanan->kode_booking,
                        'gross_amount' => $pesanan->total_harga,
                    ),
                    'customer_details' => array(
                        'first_name' => Auth::user()->nama,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->nomor_telepon,
                    ),
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
                );

                $snapToken = Snap::getSnapToken($params);
                $pesanan->update([
                    'url_pembayaran_midtrans' => 'https://app.sandbox.midtrans.com/snap/v1/pay/' . $snapToken,
                    'midtrans_transaction_id' => null, // Reset if new transaction initiated
                    'midtrans_transaction_status' => 'pending',
                    'status_pembayaran' => 'pending', // Set status back to pending
                    'status_pesanan' => 'menunggu_pembayaran', // Set status back
                ]);

                return redirect()->route('pelanggan.pesanan.show', $pesanan->id)->with('success', 'Silakan lanjutkan pembayaran Anda.');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal memuat ulang pembayaran: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Pesanan tidak dalam status yang dapat dicoba ulang pembayarannya.');
    }
}