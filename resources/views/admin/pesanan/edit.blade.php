{{-- resources/views/admin/pesanan/edit.blade.php --}}
@extends('layouts.admin') {{-- Sesuaikan dengan layout admin Anda --}}

@section('title', 'Edit Status Pesanan')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Status Pesanan #{{ $pesanan->kode_booking }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Status</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pesanan.update', $pesanan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status_pesanan" class="form-label">Status Pesanan</label>
                    <select class="form-select @error('status_pesanan') is-invalid @enderror" id="status_pesanan" name="status_pesanan" required>
                        <option value="menunggu_pembayaran" {{ old('status_pesanan', $pesanan->status_pesanan) == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="diproses" {{ old('status_pesanan', $pesanan->status_pesanan) == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ old('status_pesanan', $pesanan->status_pesanan) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan" {{ old('status_pesanan', $pesanan->status_pesanan) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status_pesanan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                    <select class="form-select @error('status_pembayaran') is-invalid @enderror" id="status_pembayaran" name="status_pembayaran" required>
                        <option value="pending" {{ old('status_pembayaran', $pesanan->status_pembayaran) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('status_pembayaran', $pesanan->status_pembayaran) == 'paid' ? 'selected' : '' }}>Dibayar</option>
                        <option value="failed" {{ old('status_pembayaran', $pesanan->status_pembayaran) == 'failed' ? 'selected' : '' }}>Gagal</option>
                        <option value="refunded" {{ old('status_pembayaran', $pesanan->status_pembayaran) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                    @error('status_pembayaran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Opsi untuk menambahkan catatan admin jika perlu --}}
                {{--
                <div class="mb-3">
                    <label for="catatan_admin" class="form-label">Catatan Admin (Opsional)</label>
                    <textarea class="form-control @error('catatan_admin') is-invalid @enderror" id="catatan_admin" name="catatan_admin" rows="3">{{ old('catatan_admin', $pesanan->catatan_admin ?? '') }}</textarea>
                    @error('catatan_admin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                --}}

                <button type="submit" class="btn btn-primary">Update Status</button>
                <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection