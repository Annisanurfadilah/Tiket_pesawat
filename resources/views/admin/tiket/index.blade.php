<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tiket Pesawat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            backdrop-filter: blur(4px);
        }

        .content-wrapper {
            background-color: rgba(255, 255, 255, 0.92);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
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
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Admin SemsestaAirline - Tiket</a>
        </div>
    </nav>

    <div class="container mb-5">
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
                    + Tambah Tiket
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
                                    <a href="{{ route('admin.tiket.show', $tiket) }}" class="btn btn-info btn-sm me-1">Detail</a>
                                    <a href="{{ route('admin.tiket.edit', $tiket) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                                    <form action="{{ route('admin.tiket.destroy', $tiket) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus tiket ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Tidak ada data tiket.
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
