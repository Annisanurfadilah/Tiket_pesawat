<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Midtrans\Notification; // Pastikan ini di-import
use DB;

class MidtransCallbackController extends Controller
{
    public function callback(Request $request)
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default is false)
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        // Set sanitization on (default is true)
        \Midtrans\Config::$isSanitized = true;

        // Create Snap object
        $notif = new Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id; // Ini adalah kode_booking yang kita kirim
        $fraud = $notif->fraud_status;

        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        // Update Midtrans status di database
        $pesanan->midtrans_transaction_status = $transaction;

        // Logic berdasarkan status transaksi Midtrans
        if ($transaction == 'capture') {
            // Untuk pembayaran non-3ds, atau 3ds yang sudah settled
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $pesanan->status_pembayaran = 'pending'; // Perlu verifikasi lebih lanjut
                } else {
                    $pesanan->status_pembayaran = 'paid'; // Pembayaran berhasil
                    $pesanan->status_pesanan = 'diproses'; // Update status pesanan di aplikasi
                }
            }
        } elseif ($transaction == 'settlement') {
            // Untuk pembayaran yang sukses (misal: bank transfer, GoPay)
            $pesanan->status_pembayaran = 'paid';
            $pesanan->status_pesanan = 'diproses';
        } elseif ($transaction == 'pending') {
            // Menunggu pembayaran dari user
            $pesanan->status_pembayaran = 'pending';
        } elseif ($transaction == 'deny') {
            // Pembayaran ditolak
            $pesanan->status_pembayaran = 'failed';
        } elseif ($transaction == 'expire') {
            // Pembayaran kadaluarsa
            $pesanan->status_pembayaran = 'failed';
            $pesanan->status_pesanan = 'dibatalkan'; // Otomatis batalkan pesanan
        } elseif ($transaction == 'cancel') {
            // Pembayaran dibatalkan
            $pesanan->status_pembayaran = 'failed';
            $pesanan->status_pesanan = 'dibatalkan';
        }

        $pesanan->save();

        // Jika statusnya 'paid' dan ini adalah update dari 'pending'
        // Kurangi stok tiket jika pembayaran berhasil dan stok belum dikurangi
        if ($pesanan->status_pembayaran == 'paid' && $pesanan->wasChanged('status_pembayaran')) {
            $tiket = $pesanan->tiket;
            if ($tiket && $tiket->stok >= $pesanan->jumlah_tiket) {
                $tiket->decrement('stok', $pesanan->jumlah_tiket);
            }
        } elseif ($pesanan->status_pesanan == 'dibatalkan' && $pesanan->wasChanged('status_pesanan')) {
             // Kembalikan stok jika pesanan dibatalkan (misal karena expire/cancel)
             $tiket = $pesanan->tiket;
             if ($tiket) {
                 $tiket->increment('stok', $pesanan->jumlah_tiket);
             }
        }

        return response()->json(['message' => 'Notification processed successfully'], 200);
    }

    // Metode untuk callback URLs (Finish, Unfinish, Error) - ini biasanya hanya untuk redirect user, bukan webhook utama
    public function finish(Request $request)
    {
        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if ($pesanan) {
            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)->with('success', 'Pembayaran Anda berhasil diproses.');
        }
        return redirect()->route('pelanggan.dashboard')->with('info', 'Terima kasih telah berbelanja!');
    }

    public function unfinish(Request $request)
    {
        $orderId = $request->input('order_id');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();
        if ($pesanan) {
            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)->with('warning', 'Pembayaran belum selesai. Silakan coba lagi.');
        }
        return redirect()->route('pelanggan.dashboard')->with('error', 'Pembayaran tidak selesai.');
    }

    public function error(Request $request)
    {
        $orderId = $request->input('order_id');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();
        if ($pesanan) {
            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)->with('error', 'Terjadi kesalahan saat pembayaran. Silakan coba lagi.');
        }
        return redirect()->route('pelanggan.dashboard')->with('error', 'Terjadi kesalahan pembayaran.');
    }
}