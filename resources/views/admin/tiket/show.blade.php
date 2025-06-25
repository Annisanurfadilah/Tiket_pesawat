<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Tiket</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: url('{{ asset('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg') }}') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
      color: #fff;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.6);
      min-height: 100vh;
      padding-top: 60px;
      padding-bottom: 60px;
    }

    .card {
      background: rgba(255, 255, 255, 0.95);
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      color: #333;
    }

    .card-header h4 {
      font-weight: 600;
      color: #2c3e50;
    }

    table th {
      color: #555;
    }

    .img-fluid {
      max-height: 250px;
      object-fit: cover;
    }

    .btn {
      min-width: 120px;
    }

    .btn-info {
      background-color: #17a2b8;
      border-color: #17a2b8;
    }

    .btn-info:hover {
      background-color: #138496;
      border-color: #117a8b;
    }

    .btn-outline-light:hover {
      color: #000;
      background-color: #fff;
      border-color: #fff;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <h2 class="text-center text-white mb-4">Detail Tiket</h2>

          <div class="card">
            <div class="card-header">
              <h4>{{ $tiket->maskapai }}</h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <table class="table table-borderless">
                    <tr>
                      <th width="200">Maskapai:</th>
                      <td>{{ $tiket->maskapai }}</td>
                    </tr>
                    <tr>
                      <th>Bandara Asal:</th>
                      <td>{{ $tiket->bandara_asal }}</td>
                    </tr>
                    <tr>
                      <th>Bandara Tujuan:</th>
                      <td>{{ $tiket->bandara_tujuan }}</td>
                    </tr>
                    <tr>
                      <th>Tanggal Keberangkatan:</th>
                      <td>{{ $tiket->tanggal_keberangkatan->format('d F Y') }}</td>
                    </tr>
                    <tr>
                      <th>Jam Keberangkatan:</th>
                      <td>{{ $tiket->jam_keberangkatan }}</td>
                    </tr>
                    <tr>
                      <th>Harga:</th>
                      <td><strong>Rp {{ number_format($tiket->harga, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                      <th>Status:</th>
                      <td>
                        <span class="badge {{ $tiket->status == 'tersedia' ? 'bg-success' : 'bg-danger' }} fs-6">
                          {{ ucfirst($tiket->status) }}
                        </span>
                      </td>
                    </tr>
                    <tr>
                      <th>Dibuat pada:</th>
                      <td>{{ $tiket->created_at->format('d F Y H:i') }}</td>
                    </tr>
                    <tr>
                      <th>Terakhir diupdate:</th>
                      <td>{{ $tiket->updated_at->format('d F Y H:i') }}</td>
                    </tr>
                  </table>
                </div>

                @if($tiket->gambar)
                <div class="col-md-4">
                  <div class="text-center">
                    <h6 class="mb-2">Gambar Tiket:</h6>
                    <img src="{{ asset('storage/' . $tiket->gambar) }}"
                         alt="Gambar {{ $tiket->maskapai }}"
                         class="img-fluid rounded border shadow">
                  </div>
                </div>
                @endif
              </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
              <a href="{{ route('admin.tiket.index') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left"></i> Kembali
              </a>
              <div>
                <a href="{{ route('admin.tiket.edit', $tiket) }}" class="btn btn-info text-white me-2">
                  <i class="bi bi-pencil-square"></i> Edit Tiket
                </a>
                <form action="{{ route('admin.tiket.destroy', $tiket) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin ingin menghapus tiket ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Hapus Tiket
                  </button>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
