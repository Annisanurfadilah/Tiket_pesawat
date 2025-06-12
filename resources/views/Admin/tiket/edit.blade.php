<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Tiket</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <!-- Custom Styling -->
  <style>
    body {
      background: url('{{ asset('https://i.pinimg.com/736x/bc/70/19/bc701962a87056f2928e7ca53162aa33.jpg') }}') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', sans-serif;
    }

    .overlay {
      background-color: rgba(0, 0, 0, 0.6);
      min-height: 100vh;
      padding-top: 60px;
      padding-bottom: 60px;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    }

    .form-label {
      font-weight: 600;
    }

    h2 {
      color: #fff;
      margin-bottom: 30px;
      text-align: center;
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    .img-thumbnail {
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <div class="container">
      <h2>Edit Tiket</h2>
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card p-4">
            <div class="card-body">
              <form action="{{ route('admin.tiket.update', $tiket) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="maskapai" class="form-label">Maskapai</label>
                    <input type="text" id="maskapai" name="maskapai" class="form-control @error('maskapai') is-invalid @enderror" value="{{ old('maskapai', $tiket->maskapai) }}" required>
                    @error('maskapai')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                      <option value="">Pilih Status</option>
                      <option value="tersedia" {{ old('status', $tiket->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                      <option value="habis" {{ old('status', $tiket->status) == 'habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                    @error('status')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="bandara_asal" class="form-label">Bandara Asal</label>
                    <input type="text" id="bandara_asal" name="bandara_asal" class="form-control @error('bandara_asal') is-invalid @enderror" value="{{ old('bandara_asal', $tiket->bandara_asal) }}" required>
                    @error('bandara_asal')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="bandara_tujuan" class="form-label">Bandara Tujuan</label>
                    <input type="text" id="bandara_tujuan" name="bandara_tujuan" class="form-control @error('bandara_tujuan') is-invalid @enderror" value="{{ old('bandara_tujuan', $tiket->bandara_tujuan) }}" required>
                    @error('bandara_tujuan')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="tanggal_keberangkatan" class="form-label">Tanggal Keberangkatan</label>
                    <input type="date" id="tanggal_keberangkatan" name="tanggal_keberangkatan" class="form-control @error('tanggal_keberangkatan') is-invalid @enderror" value="{{ old('tanggal_keberangkatan', $tiket->tanggal_keberangkatan->format('Y-m-d')) }}" required>
                    @error('tanggal_keberangkatan')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="jam_keberangkatan" class="form-label">Jam Keberangkatan</label>
                    <input type="time" id="jam_keberangkatan" name="jam_keberangkatan" class="form-control @error('jam_keberangkatan') is-invalid @enderror" value="{{ old('jam_keberangkatan', $tiket->jam_keberangkatan) }}" required>
                    @error('jam_keberangkatan')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" id="harga" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $tiket->harga) }}" required min="0">
                    @error('harga')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="gambar" class="form-label">Gambar (Opsional)</label>
                    <input type="file" id="gambar" name="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                    @if($tiket->gambar)
                      <small class="text-muted">Gambar saat ini: {{ basename($tiket->gambar) }}</small>
                    @endif
                    @error('gambar')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                @if($tiket->gambar)
                <div class="mb-3">
                  <label class="form-label">Gambar Saat Ini:</label>
                  <div>
                    <img src="{{ asset('storage/' . $tiket->gambar) }}" alt="Gambar Tiket" class="img-thumbnail" style="max-width: 200px;">
                  </div>
                </div>
                @endif

                <div class="d-flex justify-content-between">
                  <a href="{{ route('admin.tiket.index') }}" class="btn btn-secondary">Kembali</a>
                  <button type="submit" class="btn btn-primary">Update Tiket</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
