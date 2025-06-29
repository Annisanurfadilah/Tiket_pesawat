@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    body {
        background: url('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg') no-repeat center center fixed;
        background-size: cover;
    }

    .dashboard-wrapper {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
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

    @media (max-width: 576px) {
        .card.border-left {
            margin-bottom: 1rem;
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="dashboard-wrapper px-4" style="max-width: 1200px;">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h1 class="h3 text-dark fw-bold mb-2">Dashboard Admin</h1>
            <button class="btn btn-outline-primary shadow-sm mb-2">
                <i class="bi bi-calendar3 me-2"></i>{{ date('d M Y') }}
            </button>
        </div>

        <!-- Statistik Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
            <div class="col">
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
            <div class="col">
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
            <div class="col">
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
            <div class="col">
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
</div>
@endsection