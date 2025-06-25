<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $totalPelanggan = User::pelanggan()->count();
        $totalAdmin = User::admin()->count();
        $pelangganAktif = User::pelanggan()->aktif()->count();
        $pelangganTerbaru = User::pelanggan()->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalPelanggan', 
            'totalAdmin', 
            'pelangganAktif', 
            'pelangganTerbaru'
        ));
    }
}
