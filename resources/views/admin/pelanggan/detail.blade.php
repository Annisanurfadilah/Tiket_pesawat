@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Pelanggan</h5>
        <a href="{{ route('admin.pelanggan.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <th style="width: 200px;">Nama</th>
                <td>{{ $pelanggan->nama }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $pelanggan->email }}</td>
            </tr>
            <tr>
                <th>Nomor Telepon</th>
                <td>{{ $pelanggan->nomor_telepon ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $pelanggan->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-{{ $pelanggan->aktif ? 'success' : 'secondary' }}">
                        {{ $pelanggan->aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $pelanggan->created_at->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <th>Terakhir Diperbarui</th>
                <td>{{ $pelanggan->updated_at->translatedFormat('d F Y H:i') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
