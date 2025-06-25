@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    body {
        background: url('{{ asset('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg') }}') no-repeat center center fixed;
        background-size: cover;
    }

    .dashboard-wrapper {
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card.border-left {
        border-left-width: 5px;
        border-radius: 1rem;
    }

    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }

    .rounded-avatar {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .table-responsive {
        max-height: 320px;
        overflow-y: auto;
    }

</style>

<div class="dashboard-wrapper">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark fw-bold">Dashboard Admin</h1>
        <button class="btn btn-outline-primary shadow-sm">
            <i class="bi bi-calendar3 me-2"></i>{{ date('d M Y') }}
        </button>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left border-left-primary shadow h-100 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-primary fw-bold mb-1">Total Pelanggan</div>
                        <div class="h5 fw-bold text-dark">{{ $totalPelanggan }}</div>
                    </div>
                    <i class="bi bi-people fs-2 text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left border-left-success shadow h-100 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-success fw-bold mb-1">Pelanggan Aktif</div>
                        <div class="h5 fw-bold text-dark">{{ $pelangganAktif }}</div>
                    </div>
                    <i class="bi bi-person-check fs-2 text-success"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left border-left-info shadow h-100 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-info fw-bold mb-1">Total Admin</div>
                        <div class="h5 fw-bold text-dark">{{ $totalAdmin }}</div>
                    </div>
                    <i class="bi bi-shield-check fs-2 text-info"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left border-left-warning shadow h-100 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase text-warning fw-bold mb-1">Pelanggan Tidak Aktif</div>
                        <div class="h5 fw-bold text-dark">{{ $totalPelanggan - $pelangganAktif }}</div>
                    </div>
                    <i class="bi bi-person-x fs-2 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pelanggan Terbaru -->
    <div class="card shadow border-0 mb-4 rounded-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-primary mb-0"><i class="bi bi-people me-2"></i>Pelanggan Terbaru</h5>
            <a href="{{ route('admin.pelanggan.index') }}" class="btn btn-sm btn-outline-primary">
                Lihat Semua
            </a>
        </div>
        <div class="card-body">
            @if($pelangganTerbaru->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pelangganTerbaru as $pelanggan)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2 rounded-avatar">
                                            {{ strtoupper(substr($pelanggan->nama, 0, 1)) }}
                                        </div>
                                        {{ $pelanggan->nama }}
                                    </div>
                                </td>
                                <td>{{ $pelanggan->email }}</td>
                                <td>
                                    @if($pelanggan->status)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $pelanggan->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">Belum ada pelanggan terdaftar</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
