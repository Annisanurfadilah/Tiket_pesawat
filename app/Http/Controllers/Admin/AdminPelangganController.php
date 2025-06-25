<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPelangganController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = User::pelanggan();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('nomor_telepon', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['aktif', 'nonaktif'])) {
            $query->where('status', $request->status);
        }

        $pelanggan = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function show($id)
    {
        $pelanggan = User::pelanggan()->findOrFail($id);
        return view('admin.pelanggan.detail', compact('pelanggan'));
    }

    public function toggleStatus($id)
    {
        $pelanggan = User::pelanggan()->findOrFail($id);
        $pelanggan->aktif = !$pelanggan->aktif;
        $pelanggan->save();

        $status = $pelanggan->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Pelanggan berhasil {$status}");
    }

    public function destroy($id)
    {
        $pelanggan = User::pelanggan()->findOrFail($id);
        $pelanggan->delete();

        return redirect()->back()->with('success', 'Pelanggan berhasil dihapus');
    }
}
