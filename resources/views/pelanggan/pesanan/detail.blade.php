@extends('layouts.app')

@section('title', 'Detail Tiket')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Detail Tiket -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Detail Penerbangan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($tiket->gambar)
                                <img src="{{ Storage::url($tiket->gambar) }}" 
                                     alt="{{ $tiket->maskapai }}" 
                                     class="img-fluid rounded mb-3">
                            @else
                                <div class="bg-light p-4 rounded text-center mb-3">
                                    <i class="fas fa-plane fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-3">{{ $tiket->maskapai }}</h4>
                            
                            <div class="flight-details mb-4">
                                <div class="row align-items-center">
                                    <div class="col-4 text-center">
                                        <h5 class="mb-1">{{ $tiket->bandara_asal }}</h5>
                                        <div class="text-muted">Keberangkatan</div>
                                        <div class="fw-bold">{{ date('H:i', strtotime($tiket->jam_keberangkatan)) }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <i class="fas fa-plane fa-2x text-primary"></i>
                                        <div class="small text-muted mt-1">Penerbangan Langsung</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <h5 class="mb-1">{{ $tiket->bandara_tujuan }}</h5>
                                        <div class="text-muted">Tujuan</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="text-muted">Tanggal Keberangkatan</label>
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($tiket->tanggal_keberangkatan)->format('d F Y') }}</div>
                                </div>
                                <div class="col-6">
                                    <label class="text-muted">Status</label>
                                    <div>
                                        @if($tiket->status == 'tersedia')
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Habis</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="price-display">
                                <label class="text-muted">Harga per Orang</label>
                                <h3 class="text-primary mb-0">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pemesanan -->
            @if($tiket->status == 'tersedia')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Form Pemesanan</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pesanan.store') }}" method="POST" id="formPesanan">
                            @csrf
                            <input type="hidden" name="tiket_id" value="{{ $tiket->id }}">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jumlah Penumpang <span class="text-danger">*</span></label>
                                    <input type="number" name="jumlah_penumpang" class="form-control @error('jumlah_penumpang') is-invalid @enderror" 
                                           value="{{ old('jumlah_penumpang', 1) }}" min="1" max="10" required>
                                    @error('jumlah_penumpang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Harga</label>
                                    <input type="text" class="form-control" id="totalHarga" readonly value="Rp {{ number_format($tiket->harga, 0, ',', '.') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3" 
                                          placeholder="Tambahkan catatan khusus untuk pesanan Anda">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Informasi Penting:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Pastikan data yang Anda masukkan sudah benar</li>
                                    <li>Pembayaran dapat dilakukan setelah konfirmasi pesanan</li>
                                    <li>Pesanan dapat dibatalkan maksimal 24 jam sebelum keberangkatan</li>
                                </ul>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                    <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                                </button>
                                <a href="{{ route('tiket.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Maaf!</strong> Tiket untuk penerbangan ini sudah habis.
                    <a href="{{ route('tiket.index') }}" class="alert-link">Cari penerbangan lain</a>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Ringkasan Pesanan -->
            <div class="card sticky-top">
                <div class="card-header">
                    <h6 class="mb-0">Ringkasan Perjalanan</h6>
                </div>
                <div class="card-body">
                    <div class="flight-summary">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Maskapai</span>
                            <span class="fw-bold">{{ $tiket->maskapai }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Rute</span>
                            <span class="fw-bold">{{ $tiket->bandara_asal }} → {{ $tiket->bandara_tujuan }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tanggal</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($tiket->tanggal_keberangkatan)->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Waktu</span>
                            <span class="fw-bold">{{ date('H:i', strtotime($tiket->jam_keberangkatan)) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Harga per Orang</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bantuan -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Butuh Bantuan?</h6>
                    <p class="card-text small text-muted">
                        Hubungi customer service kami untuk bantuan pemesanan.
                    </p>
                    <div class="d-grid">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone"></i> Hubungi CS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahInput = document.querySelector('input[name="jumlah_penumpang"]');
    const totalHargaInput = document.getElementById('totalHarga');
    const hargaPerOrang = {{ $tiket->harga }};

    function updateTotalHarga() {
        const jumlah = parseInt(jumlahInput.value) || 1;
        const total = hargaPerOrang * jumlah;
        totalHargaInput.value = 'Rp ' + total.toLocaleString('id-ID');
    }

    jumlahInput.addEventListener('input', updateTotalHarga);
    
    // Validasi form
    document.getElementById('formPesanan').addEventListener('submit', function(e) {
        const jumlah = parseInt(jumlahInput.value);
        if (jumlah < 1 || jumlah > 10) {
            e.preventDefault();
            alert('Jumlah penumpang harus antara 1-10 orang');
            return false;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.flight-details {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 20px;
    background: #f8f9fa;
}
.sticky-top {
    top: 20px;
}
</style>
@endpush