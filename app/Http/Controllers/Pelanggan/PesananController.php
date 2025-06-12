<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    /**
     * Display a listing of available tickets
     */
    public function index()
    {
        $tikets = Tiket::where('status', 'tersedia')
                      ->where('stok', '>', 0)
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
        
        return view('pelanggan.pesanan.index', compact('tikets'));
    }

    /**
     * Show the form for creating a new order
     * Parameter $tiket akan di-inject oleh Route Model Binding
     */
    public function create(Tiket $tiket)
    {
        // Check if ticket is still available
        if ($tiket->stok <= 0 || $tiket->status != 'tersedia') {
            return redirect()->route('pelanggan.pesanan.index')
                ->with('error', 'Tiket tidak tersedia atau sudah habis.');
        }

        return view('pelanggan.pesanan.create', compact('tiket'));
    }

    /**
     * Alternative method jika ingin menggunakan ID manual
     */
    public function createById($tiketId)
    {
        $tiket = Tiket::findOrFail($tiketId);

        // Check if ticket is still available
        if ($tiket->stok <= 0 || $tiket->status != 'tersedia') {
            return redirect()->route('pelanggan.pesanan.index')
                ->with('error', 'Tiket tidak tersedia atau sudah habis.');
        }

        return view('pelanggan.pesanan.create', compact('tiket'));
    }

    /**
     * Store a newly created order in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'tiket_id' => 'required|exists:tikets,id',
            'jumlah_tiket' => 'required|integer|min:1|max:10',
            'nama_penumpang' => 'required|string|max:255',
            'nomor_identitas' => 'required|string|max:50',
            'nomor_telepon' => 'required|string|max:20',
        ]);

        $tiket = Tiket::findOrFail($request->tiket_id);

        // Check availability
        if ($tiket->stok < $request->jumlah_tiket || $tiket->status != 'tersedia') {
            return redirect()->back()
                ->with('error', 'Stok tiket tidak mencukupi atau tiket tidak tersedia.')
                ->withInput();
        }

        // Calculate total price
        $totalHarga = $tiket->harga * $request->jumlah_tiket;

        // Create order
        $pesanan = Pesanan::create([
            'user_id' => Auth::id(),
            'tiket_id' => $tiket->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga' => $totalHarga,
            'nama_penumpang' => $request->nama_penumpang,
            'nomor_identitas' => $request->nomor_identitas,
            'nomor_telepon' => $request->nomor_telepon,
            'status_pesanan' => 'pending',
            'kode_booking' => $this->generateBookingCode(),
        ]);

        // Update ticket stock
        $tiket->decrement('stok', $request->jumlah_tiket);

        // Update status if stock is empty
        if ($tiket->stok <= 0) {
            $tiket->update(['status' => 'habis']);
        }

        return redirect()->route('pelanggan.pesanan.show', $pesanan->id)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    /**
     * Display the specified order
     */
    public function show(Pesanan $pesanan)
    {
        // Ensure user can only view their own orders
        if ($pesanan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('pelanggan.pesanan.show', compact('pesanan'));
    }

    /**
     * Display user's order history
     */
    public function history()
    {
        $pesanans = Pesanan::where('user_id', Auth::id())
                           ->with('tiket')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

        return view('pelanggan.pesanan.history', compact('pesanans'));
    }

    /**
     * Cancel an order
     */
    public function cancel(Pesanan $pesanan)
    {
        // Ensure user can only cancel their own orders
        if ($pesanan->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancellation for pending orders
        if ($pesanan->status_pesanan !== 'pending') {
            return redirect()->back()
                ->with('error', 'Pesanan tidak dapat dibatalkan.');
        }

        // Restore ticket stock
        $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
        
        // Update ticket status if it was 'habis'
        if ($pesanan->tiket->status === 'habis') {
            $pesanan->tiket->update(['status' => 'tersedia']);
        }

        // Update order status
        $pesanan->update(['status_pesanan' => 'dibatalkan']);

        return redirect()->back()
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Generate unique booking code
     */
    private function generateBookingCode()
    {
        do {
            $code = 'TKT' . strtoupper(uniqid());
        } while (Pesanan::where('kode_booking', $code)->exists());

        return $code;
    }
}