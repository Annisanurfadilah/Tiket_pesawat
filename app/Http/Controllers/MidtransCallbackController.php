<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Tiket; // Pastikan ini di-use jika digunakan di incrementTiketStock
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log; // Import Log facade

class MidtransCallbackController extends Controller
{
    public function __construct()
    {
        // Set Midtrans Configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool)config('midtrans.is_production');
        Config::$isSanitized = (bool)config('midtrans.is_sanitized');
        Config::$is3ds = (bool)config('midtrans.is_3ds');

        Log::info('MidtransCallbackController initialized. Production Mode: ' . (Config::$isProduction ? 'true' : 'false'));
    }

    /**
     * Handle Midtrans notification callback (Webhook).
     * This method is called by Midtrans servers.
     */
    public function callback(Request $request)
    {
        // Debugging: Log the entire incoming request body
        Log::info('--- Midtrans Callback START ---');
        Log::info('Midtrans Callback Received:', $request->all());
        Log::info('Midtrans Callback Raw Body:', [$request->getContent()]);

        try {
            // PENTING: Inisialisasi Notification yang benar.
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status;
            $transactionId = $notification->transaction_id;
            $paymentType = $notification->payment_type;

            Log::info("Processing Midtrans callback for order ID: {$orderId}, Status: {$transactionStatus}, Fraud: {$fraudStatus}, Type: {$paymentType}");

            $pesanan = Pesanan::where('kode_booking', $orderId)->first();

            if (!$pesanan) {
                Log::error('Midtrans Callback Error: Order with kode_booking ' . $orderId . ' not found.');
                Log::info('--- Midtrans Callback END (Order Not Found) ---');
                return response()->json(['message' => 'Order not found'], 404);
            }

            // Log current and new status for clarity
            Log::info("Order found: ID {$pesanan->id}, Current status: {$pesanan->status_pembayaran} / {$pesanan->status_pesanan}");

            // Update Midtrans transaction ID if it's new
            if (empty($pesanan->midtrans_transaction_id)) {
                $pesanan->midtrans_transaction_id = $transactionId;
                Log::info("Updated midtrans_transaction_id for order {$orderId} to {$transactionId}.");
            }
            // Always update status transaksi Midtrans dari callback
            $pesanan->midtrans_transaction_status = $transactionStatus;

            // Logika utama untuk menentukan status pesanan dan pembayaran
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $pesanan->status_pembayaran = 'challenge';
                    $pesanan->status_pesanan = 'diproses';
                    Log::info("Order {$orderId}: Status set to challenge (requires manual review).");
                } else if ($fraudStatus == 'accept') {
                    $pesanan->status_pembayaran = 'paid';
                    $pesanan->status_pesanan = 'diproses';
                    Log::info("Order {$orderId}: Status set to paid (capture accept).");
                }
            } else if ($transactionStatus == 'settlement') {
                $pesanan->status_pembayaran = 'paid';
                $pesanan->status_pesanan = 'diproses';
                Log::info("Order {$orderId}: Status set to paid (settlement).");
            } else if ($transactionStatus == 'pending') {
                $pesanan->status_pembayaran = 'pending';
                $pesanan->status_pesanan = 'menunggu_pembayaran';
                Log::info("Order {$orderId}: Status set to pending.");
            } else if ($transactionStatus == 'deny') {
                $pesanan->status_pembayaran = 'failed';
                $pesanan->status_pesanan = 'dibatalkan';
                $this->incrementTiketStock($pesanan); // Will log internally
                Log::info("Order {$orderId}: Status set to failed (deny).");
            } else if ($transactionStatus == 'expire') {
                $pesanan->status_pembayaran = 'expired';
                $pesanan->status_pesanan = 'dibatalkan';
                $this->incrementTiketStock($pesanan); // Will log internally
                Log::info("Order {$orderId}: Status set to expired.");
            } else if ($transactionStatus == 'cancel') {
                $pesanan->status_pembayaran = 'cancelled';
                $pesanan->status_pesanan = 'dibatalkan';
                $this->incrementTiketStock($pesanan); // Will log internally
                Log::info("Order {$orderId}: Status set to cancelled.");
            } else {
                Log::info('Midtrans Callback: Unhandled transaction status: ' . $transactionStatus . ' for order ' . $orderId);
            }

            $pesanan->save();
            Log::info("Order {$orderId} saved. Final status_pembayaran: {$pesanan->status_pembayaran}, status_pesanan: {$pesanan->status_pesanan}");

            Log::info('--- Midtrans Callback END (Success) ---');
            return response()->json(['message' => 'OK'], 200);

        } catch (\Exception $e) {
            Log::error('--- Midtrans Callback EXCEPTION START ---');
            Log::error('Midtrans Callback Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Log::error('--- Midtrans Callback EXCEPTION END ---');
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Helper function to increment ticket stock.
     */
    private function incrementTiketStock(Pesanan $pesanan)
    {
        Log::info("Attempting to increment stock for order {$pesanan->kode_booking}...");
        if ($pesanan->tiket && $pesanan->jumlah_tiket > 0 && $pesanan->status_pesanan !== 'dibatalkan') {
            $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
            Log::info("Stock incremented for order {$pesanan->kode_booking}. Ticket ID: {$pesanan->tiket->id}, Amount: {$pesanan->jumlah_tiket}. New stock: {$pesanan->tiket->stok}.");
        } else {
             Log::info("Stock not incremented for order {$pesanan->kode_booking}. Conditions not met. (Ticket exists: " . ($pesanan->tiket ? 'yes' : 'no') . ", Jumlah Tiket > 0: " . ($pesanan->jumlah_tiket > 0 ? 'yes' : 'no') . ", Current status not 'dibatalkan': " . ($pesanan->status_pesanan !== 'dibatalkan' ? 'yes' : 'no') . ").");
        }
    }

    /**
     * Handle redirection after user finishes payment on Midtrans (for /midtrans-finish).
     */
    public function finish(Request $request)
    {
        Log::info('--- Midtrans Finish Redirect START ---');
        Log::info('Finish Redirect received:', $request->all());

        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status');

        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if ($pesanan) {
            Log::info("Finish Redirect: Order found for kode_booking {$orderId}. Current status: {$pesanan->status_pembayaran}");
            if ($pesanan->status_pembayaran === 'paid' || $pesanan->status_pesanan === 'diproses') {
                Log::info("Finish Redirect: Order {$orderId} paid/processed. Redirecting to show with success.");
                return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                                 ->with('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
            } elseif ($pesanan->status_pembayaran === 'pending' || $pesanan->status_pesanan === 'menunggu_pembayaran') {
                 Log::info("Finish Redirect: Order {$orderId} pending. Redirecting to show with info.");
                 return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                                 ->with('info', 'Pembayaran Anda sedang menunggu penyelesaian. Silakan selesaikan pembayaran atau tunggu konfirmasi.');
            } else {
                Log::info("Finish Redirect: Order {$orderId} failed/cancelled/expired. Redirecting to show with error.");
                return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                                 ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan cek status pesanan.');
            }
        }

        Log::error('Finish Redirect Error: Order not found for kode_booking ' . $orderId);
        Log::info('--- Midtrans Finish Redirect END (Order Not Found) ---');
        return redirect()->route('pelanggan.pesanan.index')->with('error', 'Pesanan tidak ditemukan atau terjadi kesalahan.');
    }

    /**
     * Handle redirection after user leaves payment on Midtrans without finishing (for /midtrans-unfinish).
     */
    public function unfinish(Request $request)
    {
        Log::info('--- Midtrans Unfinish Redirect START ---');
        Log::info('Unfinish Redirect received:', $request->all());

        $orderId = $request->input('order_id');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if ($pesanan) {
            Log::info("Unfinish Redirect: Order found for kode_booking {$orderId}. Redirecting to show with info.");
            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                             ->with('info', 'Pembayaran Anda tidak diselesaikan. Silakan coba lagi.');
        }

        Log::error('Unfinish Redirect Error: Order not found for kode_booking ' . $orderId);
        Log::info('--- Midtrans Unfinish Redirect END (Order Not Found) ---');
        return redirect()->route('pelanggan.pesanan.index')->with('error', 'Pesanan tidak ditemukan atau terjadi kesalahan.');
    }

    /**
     * Handle redirection after payment error on Midtrans (for /midtrans-error).
     */
    public function error(Request $request)
    {
        Log::info('--- Midtrans Error Redirect START ---');
        Log::info('Error Redirect received:', $request->all());

        $orderId = $request->input('order_id');
        $pesanan = Pesanan::where('kode_booking', $orderId)->first();

        if ($pesanan) {
            Log::info("Error Redirect: Order found for kode_booking {$orderId}. Redirecting to show with error.");
            return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
                             ->with('error', 'Terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi.');
        }

        Log::error('Error Redirect Error: Order not found for kode_booking ' . $orderId);
        Log::info('--- Midtrans Error Redirect END (Order Not Found) ---');
        return redirect()->route('pelanggan.pesanan.index')->with('error', 'Pesanan tidak ditemukan atau terjadi kesalahan.');
    }
}