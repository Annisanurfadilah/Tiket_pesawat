<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Tiket; // Make sure to import Tiket model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // For Str::random() if used for kode_booking

class PesananController extends Controller
{
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
     * This method will now list tickets for selection.
     */
    public function create()
    {
        // This is the generic 'create' from the resource route
        // It should lead to a page where the user can select a ticket first.
        $tikets = Tiket::where('stok', '>', 0)->get(); // Example: get available tickets
        return view('pelanggan.pesanan.create_select_ticket', compact('tikets')); // A new view file
    }

    /**
     * Show the form for creating a new resource (with a specific ticket pre-selected).
     * This method is now called 'createWithTiket' if you chose to rename the route.
     * If you keep the original route name, ensure this is distinct from the resource's create.
     */
    public function createWithTiket(Tiket $tiket) // Laravel will inject the Tiket model
    {
        // This method receives a specific Tiket model
        return view('pelanggan.pesanan.create_form', compact('tiket')); // The actual form to fill out
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tiket_id' => 'required|exists:tikets,id',
            'jumlah_tiket' => 'required|integer|min:1',
            // Add other validation rules as needed
        ]);

        $tiket = Tiket::findOrFail($request->tiket_id);

        if ($tiket->stok < $request->jumlah_tiket) {
            return redirect()->back()->with('error', 'Stok tiket tidak mencukupi.');
        }

        $totalHarga = $request->jumlah_tiket * $tiket->harga;

        $pesanan = Pesanan::create([
            'user_id' => Auth::id(),
            'tiket_id' => $tiket->id,
            'jumlah_tiket' => $request->jumlah_tiket,
            'total_harga' => $totalHarga,
            'kode_booking' => 'BK-' . Str::upper(Str::random(8)), // Example unique code
            'status_pesanan' => 'menunggu_pembayaran',
            'status_pembayaran' => 'pending',
            // Other fields will be null/default
        ]);

        // Reduce ticket stock
        $tiket->decrement('stok', $request->jumlah_tiket);

        // Here, integrate with Midtrans to get the payment URL
        // Example (pseudocode):
        // $midtrans = new MidtransService();
        // $snapToken = $midtrans->getSnapToken($pesanan);
        // $pesanan->update(['url_pembayaran_midtrans' => $snapToken->redirect_url]);

        return redirect()->route('pelanggan.pesanan.show', $pesanan->id)->with('success', 'Pesanan berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pesanan $pesanan)
    {
        // Ensure the authenticated user owns this order
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
        // If customers can edit, add logic here. Otherwise, remove or restrict.
        abort(404); // Not found for customer
    }

    /**
     * Update the specified resource in storage (uncommon for customer to update order directly).
     * This is usually for admin. If customer can update, define logic here.
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        // If customers can update, add logic here. Otherwise, remove or restrict.
        abort(404); // Not found for customer
    }

    /**
     * Remove the specified resource from storage (uncommon for customer to delete order).
     * Customers typically cancel, not delete.
     */
    public function destroy(Pesanan $pesanan)
    {
        // If customers can delete, add logic here. Otherwise, remove or restrict.
        abort(404); // Not found for customer
    }

    /**
     * Display a history of the customer's orders.
     */
    public function history()
    {
        $pesanan = Auth::user()->pesanan()->latest()->paginate(15);
        return view('pelanggan.pesanan.index', compact('pesanan'));
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
                'status_pembayaran' => 'cancelled', // Or a new status like 'refund_pending'
            ]);

            // Return stock to ticket if necessary
            if ($pesanan->tiket) {
                $pesanan->tiket->increment('stok', $pesanan->jumlah_tiket);
            }

            return redirect()->route('pelanggan.pesanan.index')->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan pada status ini.');
    }
}