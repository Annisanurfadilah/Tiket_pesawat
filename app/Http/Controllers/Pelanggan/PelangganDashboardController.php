<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;

class PelangganDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isPelanggan()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Akses ditolak. Anda bukan pelanggan.');
            }
            return $next($request);
        });
    }

    public function index()
{
    $pelanggan = Auth::user();

    $totalPesanan = Pesanan::where('user_id', $pelanggan->id)->count();
    $pesananTerakhir = Pesanan::where('user_id', $pelanggan->id)
                        ->orderBy('created_at', 'desc')
                        ->first();

    return view('pelanggan.dashboard', compact('pelanggan', 'totalPesanan', 'pesananTerakhir'));
}
}