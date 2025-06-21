<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTiketController extends Controller
{
    public function index()
    {
        $tikets = Tiket::latest()->paginate(10);
        return view('admin.tiket.index', compact('tikets'));
    }

    public function create()
    {
        return view('admin.tiket.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'maskapai' => 'required|string|max:255',
            'bandara_asal' => 'required|string|max:255',
            'bandara_tujuan' => 'required|string|max:255',
            'tanggal_keberangkatan' => 'required|date',
            'jam_keberangkatan' => 'required',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,habis',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('tiket', 'public');
        }

        Tiket::create($data);

        return redirect()->route('admin.tiket.index')->with('success', 'Tiket berhasil ditambahkan!');
    }

    public function show(Tiket $tiket)
    {
        return view('admin.tiket.show', compact('tiket'));
    }

    public function edit(Tiket $tiket)
    {
        return view('admin.tiket.edit', compact('tiket'));
    }

    public function update(Request $request, Tiket $tiket)
    {
        $request->validate([
            'maskapai' => 'required|string|max:255',
            'bandara_asal' => 'required|string|max:255',
            'bandara_tujuan' => 'required|string|max:255',
            'tanggal_keberangkatan' => 'required|date',
            'jam_keberangkatan' => 'required',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,habis',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($tiket->gambar) {
                Storage::disk('public')->delete($tiket->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('tiket', 'public');
        }

        $tiket->update($data);

        return redirect()->route('admin.tiket.index')->with('success', 'Tiket berhasil diperbarui!');
    }

    public function destroy(Tiket $tiket)
    {
        // Hapus gambar jika ada
        if ($tiket->gambar) {
            Storage::disk('public')->delete($tiket->gambar);
        }

        $tiket->delete();

        return redirect()->route('admin.tiket.index')->with('success', 'Tiket berhasil dihapus!');
    }
}