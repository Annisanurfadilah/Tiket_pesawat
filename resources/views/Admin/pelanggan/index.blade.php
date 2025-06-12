@extends('layouts.app')

@section('title', 'Kelola Pelanggan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Daftar Pelanggan</h4>
    <form action="{{ route('admin.pelanggan') }}" method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i>
        </button>
    </form>
</div>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover table-bordered align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
             
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelanggan as $index => $user)
            <tr>
                <td>{{ $pelanggan->firstItem() + $index }}</td>
                <td>{{ $user->nama }}</td>
                <td>{{ $user->email }}</td>
     
        
                <td>
                    <a href="{{ route('admin.pelanggan.detail', $user->id) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> Detail
                    </a>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada data pelanggan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $pelanggan->withQueryString()->links() }}
</div>
@endsection
