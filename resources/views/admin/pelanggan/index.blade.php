@extends('layouts.app')

@section('title', 'Kelola Pelanggan')

@section('content')
<style>
    .dashboard-wrapper {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 1rem;
        padding: 1.5rem;
        max-width: 1200px;
    }

    .header-section {
        background: url('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg') center/cover no-repeat;
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        position: relative;
        margin-bottom: 2rem;
    }

    .header-overlay {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 1rem;
        padding: 2rem;
    }

    .filter-form input,
    .filter-form select {
        min-width: 200px;
    }

    @media (max-width: 576px) {
        .filter-form input,
        .filter-form select {
            min-width: 100%;
        }
    }
</style>

<div class="dashboard-wrapper px-3 px-md-4">
    <!-- Header Filter -->
    <div class="header-section shadow">
        <div class="header-overlay">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h3 class="fw-bold text-white mb-0">
                    <i class="bi bi-people-fill me-2"></i> Daftar Pelanggan
                </h3>
                <form action="{{ route('admin.pelanggan.index') }}" method="GET" class="d-flex flex-wrap gap-2 filter-form">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <button type="submit" class="btn btn-light text-primary">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Pelanggan -->
    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pelanggan as $index => $user)
                            <tr class="text-center">
                                <td>{{ $pelanggan->firstItem() + $index }}</td>
                                <td class="text-start">{{ $user->nama }}</td>
                                <td class="text-start">{{ $user->email }}</td>
                                <td>
                                    <a href="{{ route('admin.pelanggan.show', $user->id) }}" class="btn btn-sm btn-outline-info rounded-pill">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle"></i> Tidak ada data pelanggan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $pelanggan->withQueryString()->links() }}
    </div>
</div>
@endsection