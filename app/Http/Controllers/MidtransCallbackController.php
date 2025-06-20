<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransCallbackController extends Controller
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
     * Handle Midtrans notification callback (Webhook).
     * This method is called by Midtrans servers.
     */
    public function callback(Request $request)
    {
        $notification = new Notification();

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id; // This is kode_booking in our Pesanan model
        $fraudStatus = $notification->fraud_status;

        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if (!$pesanan) {
            // Log this error: order not found
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update Midtrans transaction ID if it's new
        if (empty($pesanan->midtrans_transaction_id) && !empty($notification->transaction_id)) {
            $pesanan->midtrans_transaction_id = $notification->transaction_id;
        }
        $pesanan->midtrans_transaction_status = $transactionStatus; // Always update Midtrans status

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                // TODO: Set pesanan status to pending or challenge (requires manual review)
                $pesanan->status_pembayaran = 'challenge';
                $pesanan->status_pesanan = 'diproses'; // Or 'menunggu_verifikasi'
            } else if ($fraudStatus == 'accept') {
                // TODO: Set pesanan status to success
                $pesanan->status_pembayaran = 'paid';
                $pesanan->status_pesanan = 'diproses'; // Payment successful, order is being processed
            }
        } else if ($transactionStatus == 'settlement') {
            // TODO: Set pesanan status to success
            $pesanan->status_pembayaran = 'paid';
            $pesanan->status_pesanan = 'diproses'; // Payment successful, order is being processed
        } else if ($transactionStatus == 'deny') {
            // TODO: Set pesanan status to denied
            $pesanan->status_pembayaran = 'failed';
            $pesanan->status_pesanan = 'dibatalkan';
            // Optionally, re-add stock if it was initially deducted for a failed payment
            if ($pesanan->tiket && $pesanan->jumlah_tiket > 0) {
                 // Only re-add stock if it hasn't been re-added by a prior cancellation or if you have a robust stock management system
                 // Ensure this logic doesn't double-count if a cancel method already did this.
                 // A flag might be needed on Pesanan, e.g., 'stock_returned_on_cancel'
                 // For simplicity, let's assume if it reached 'deny', we should increment stock.
                $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
            }
        } else if ($transactionStatus == 'expire') {
            // TODO: Set pesanan status to expired
            $pesanan->status_pembayaran = 'expired';
            $pesanan->status_pesanan = 'dibatalkan';
            // Re-add stock
            if ($pesanan->tiket && $pesanan->jumlah_tiket > 0) {
                $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
            }
        } else if ($transactionStatus == 'cancel') {
            // TODO: Set pesanan status to cancel
            $pesanan->status_pembayaran = 'cancelled';
            $pesanan->status_pesanan = 'dibatalkan';
            // Re-add stock
            if ($pesanan->tiket && $pesanan->jumlah_tiket > 0) {
                $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
            }
        } else if ($transactionStatus == 'pending') {
            $pesanan->status_pembayaran = 'pending';
            $pesanan->status_pesanan = 'menunggu_pembayaran';
        }

        $pesanan->save();

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Handle redirection after user finishes payment on Midtrans (for /midtrans-finish).
     */
    public function finish(Request $request)
    {
        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if ($pesanan) {
            // Note: The main status update should come from the callback webhook.
            // This is primarily for user experience redirection.
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                                 ->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
            } elseif ($transactionStatus == 'pending') {
                return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                                 ->with('info', 'Pembayaran Anda sedang menunggu penyelesaian. Silakan selesaikan pembayaran.');
            } else {
                return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                                 ->with('error', 'Pembayaran gagal atau dibatalkan.');
            }
        }

        return redirect()->route('pelanggan.pesanan.index')->with('error', 'Pesanan tidak ditemukan atau terjadi kesalahan.');
    }

    /**
     * Handle redirection after user leaves payment on Midtrans without finishing (for /midtrans-unfinish).
     */
    public function unfinish(Request $request)
    {
        $orderId = $request->input('order_id');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if ($pesanan) {
            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                             ->with('info', 'Pembayaran Anda tidak diselesaikan. Silakan coba lagi.');
        }

        return redirect()->route('pelanggan.pesanan.index')->with('error', 'Pesanan tidak ditemukan atau terjadi kesalahan.');
    }

    /**
     * Handle redirection after payment error on Midtrans (for /midtrans-error).
     */
    public function error(Request $request)
    {
        $orderId = $request->input('order_id');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if ($pesanan) {
            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                             ->with('error', 'Terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi.');
        }

        return redirect()->route('pelanggan.pesanan.index')->with('error', 'Pesanan tidak ditemukan atau terjadi kesalahan.');
    }
}