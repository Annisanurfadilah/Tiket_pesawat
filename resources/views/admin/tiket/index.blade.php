@extends('layouts.app')
@section('title', 'Daftar Transaksi Tiket')

@section('content')
<style>
    body {
        background-image: url('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        min-height: 100vh;
        backdrop-filter: blur(4px);
    }

    .content-wrapper {
        background-color: rgba(255, 255, 255, 0.95);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        max-width: 100%;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(122, 180, 197, 0.3);
    }

    .page-title {
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    footer {
        background-color: rgba(255, 255, 255, 0.8);
    }

    @media (max-width: 768px) {
        table {
            font-size: 0.85rem;
        }

        th, td {
            white-space: nowrap;
        }

        .page-title {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
    }
</style>

<div class="container mb-5" style="max-width: 1200px; margin-left: 100px;">
    <div class="content-wrapper">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center page-title">
            <h4 class="mb-0">Daftar Tiket Pesawat</h4>
            <a href="{{ route('admin.tiket.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Tiket
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Maskapai</th>
                        <th>Rute</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tikets as $tiket)
                        <tr>
                            <td class="text-center">{{ $tiket->id }}</td>
                            <td>{{ $tiket->maskapai }}</td>
                            <td>{{ $tiket->bandara_asal }} → {{ $tiket->bandara_tujuan }}</td>
                            <td>{{ $tiket->tanggal_keberangkatan->format('d/m/Y') }}</td>
                            <td>{{ $tiket->jam_keberangkatan }}</td>
                            <td>Rp {{ number_format($tiket->harga, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $tiket->status == 'tersedia' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($tiket->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.tiket.show', $tiket) }}" class="btn btn-outline-info btn-sm me-1" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.tiket.edit', $tiket) }}" class="btn btn-outline-warning btn-sm me-1" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.tiket.destroy', $tiket) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus tiket ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle"></i> Tidak ada data tiket.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $tikets->links() }}
        </div>
    </div>
</div>

<footer class="text-center text-muted py-3 border-top">
    &copy; {{ date('Y') }} Sistem Pemesanan Tiket Pesawat
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection