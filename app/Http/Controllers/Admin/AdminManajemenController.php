<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminManajemenController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

  public function index()
{
    $tiket = Tiket::latest()->paginate(10);
    return view('admin.tiket.index', compact('tiket'));
}

    public function create()
    {
        return view('admin.tiket.create');
    }

 public function store(Request $request)
{
    $request->validate([
        'maskapai' => 'required|string',
        'bandara_asal' => 'required|string',
        'bandara_tujuan' => 'required|string',
        'tanggal_keberangkatan' => 'required|date',
        'jam_keberangkatan' => 'required',
        'harga' => 'required|numeric',
        'status' => 'required|string',
        'deskripsi' => 'nullable|string',
        'gambar' => 'nullable|image|max:2048',
    ]);

    $data = $request->only([
        'maskapai',
        'bandara_asal',
        'bandara_tujuan',
        'tanggal_keberangkatan',
        'jam_keberangkatan',
        'harga',
        'status',
        'deskripsi',
    ]);

    // Jika ada gambar diupload
    if ($request->hasFile('gambar')) {
        $data['gambar'] = $request->file('gambar')->store('gambar-tiket', 'public');
    }

    Tiket::create($data);

    return redirect()->route('admin.admin.index')->with('success', 'Tiket berhasil ditambahkan');
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
        'maskapai' => 'required|string',
        'bandara_asal' => 'required|string',
        'bandara_tujuan' => 'required|string',
        'tanggal_keberangkatan' => 'required|date',
        'jam_keberangkatan' => 'required',
        'harga' => 'required|numeric',
        'status' => 'required|string',
        'deskripsi' => 'nullable|string',
        'gambar' => 'nullable|image|max:2048',
    ]);

    $data = $request->only([
        'maskapai',
        'bandara_asal',
        'bandara_tujuan',
        'tanggal_keberangkatan',
        'jam_keberangkatan',
        'harga',
        'status',
        'deskripsi',
    ]);

    if ($request->hasFile('gambar')) {
        $data['gambar'] = $request->file('gambar')->store('gambar-tiket', 'public');
    }

    $tiket->update($data);

    return redirect()->route('admin.tiket.index')->with('success', 'Tiket berhasil diperbarui');
}

public function destroy(Tiket $tiket)
{
    $tiket->delete();
    return redirect()->route('admin.tiket.index')->with('success', 'Tiket berhasil dihapus');
}

}