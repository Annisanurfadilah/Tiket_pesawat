{{-- resources/views/admin/pesanan/index.blade.php --}}
@extends('layouts.admin') {{-- Sesuaikan dengan layout admin Anda --}}

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manajemen Pesanan</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pesanan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pesanan.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari Kode Booking / Nama Pelanggan" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status_pesanan" class="form-select">
                            <option value="">Filter Status Pesanan</option>
                            <option value="menunggu_pembayaran" {{ request('status_pesanan') == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="diproses" {{ request('status_pesanan') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ request('status_pesanan') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ request('status_pesanan') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status_pembayaran" class="form-select">
                            <option value="">Filter Status Pembayaran</option>
                            <option value="pending" {{ request('status_pembayaran') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status_pembayaran') == 'paid' ? 'selected' : '' }}>Dibayar</option>
                            <option value="failed" {{ request('status_pembayaran') == 'failed' ? 'selected' : '' }}>Gagal</option>
                            <option value="refunded" {{ request('status_pembayaran') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Kode Booking</th>
                            <th>Pelanggan</th>
                            <th>Tiket</th>
                            <th>Jml. Tiket</th>
                            <th>Total Harga</th>
                            <th>Status Pesanan</th>
                            <th>Status Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesanan as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $order->kode_booking }}</td>
                            <td>{{ $order->user->nama ?? 'N/A' }}</td>
                            <td>{{ $order->tiket->maskapai ?? 'N/A' }} ({{ $order->tiket->bandara_asal ?? '' }} -> {{ $order->tiket->bandara_tujuan ?? '' }})</td>
                            <td>{{ $order->jumlah_tiket }}</td>
                            <td>{{ $order->formatted_total_harga }}</td>
                            <td>
                                <span class="badge {{
                                    $order->status_pesanan == 'menunggu_pembayaran' ? 'bg-warning' :
                                    ($order->status_pesanan == 'diproses' ? 'bg-info' :
                                    ($order->status_pesanan == 'selesai' ? 'bg-success' :
                                    'bg-danger'))
                                }}">
                                    {{ Str::title(str_replace('_', ' ', $order->status_pesanan)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{
                                    $order->status_pembayaran == 'pending' ? 'bg-warning' :
                                    ($order->status_pembayaran == 'paid' ? 'bg-success' :
                                    'bg-danger')
                                }}">
                                    {{ Str::title(str_replace('_', ' ', $order->status_pembayaran)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.pesanan.show', $order->id) }}" class="btn btn-info btn-sm mb-1" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.pesanan.edit', $order->id) }}" class="btn btn-primary btn-sm mb-1" title="Edit Status">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.pesanan.destroy', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data pesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $pesanan->links() }}
            </div>
        </div>
    </div>
</div>
@endsection