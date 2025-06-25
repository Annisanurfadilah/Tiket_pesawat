{{-- resources/views/admin/pesanan/show.blade.php --}}
@extends('layouts.admin') {{-- Sesuaikan dengan layout admin Anda --}}

@section('title', 'Detail Pesanan')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Detail Pesanan #{{ $pesanan->kode_booking }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pesanan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Kode Booking:</strong> {{ $pesanan->kode_booking }}</p>
                    <p><strong>Tanggal Pesan:</strong> {{ $pesanan->created_at->format('d M Y H:i') }}</p>
                    <p><strong>Pelanggan:</strong> {{ $pesanan->user->nama ?? 'N/A' }} ({{ $pesanan->user->email ?? 'N/A' }})</p>
                    <p><strong>Status Pesanan:</strong>
                        <span class="badge {{
                            $pesanan->status_pesanan == 'menunggu_pembayaran' ? 'bg-warning' :
                            ($pesanan->status_pesanan == 'diproses' ? 'bg-info' :
                            ($pesanan->status_pesanan == 'selesai' ? 'bg-success' :
                            'bg-danger'))
                        }}">
                            {{ Str::title(str_replace('_', ' ', $pesanan->status_pesanan)) }}
                        </span>
                    </p>
                    <p><strong>Status Pembayaran:</strong>
                        <span class="badge {{
                            $pesanan->status_pembayaran == 'pending' ? 'bg-warning' :
                            ($pesanan->status_pembayaran == 'paid' ? 'bg-success' :
                            'bg-danger')
                        }}">
                            {{ Str::title(str_replace('_', ' ', $pesanan->status_pembayaran)) }}
                        </span>
                    </p>
                    @if($pesanan->bukti_pembayaran)
                        <p><strong>Bukti Pembayaran:</strong> <a href="{{ asset('storage/' . $pesanan->bukti_pembayaran) }}" target="_blank">Lihat Bukti</a></p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p><strong>Total Harga:</strong> {{ $pesanan->formatted_total_harga }}</p>
                    <p><strong>ID Transaksi Midtrans:</strong> {{ $pesanan->midtrans_transaction_id ?? '-' }}</p>
                    <p><strong>Status Transaksi Midtrans:</strong> {{ Str::title(str_replace('_', ' ', $pesanan->midtrans_transaction_status)) ?? '-' }}</p>
                    @if($pesanan->url_pembayaran_midtrans)
                        <p><strong>URL Pembayaran Midtrans:</strong> <a href="{{ $pesanan->url_pembayaran_midtrans }}" target="_blank">Link Pembayaran</a></p>
                    @endif
                </div>
            </div>

            <hr>
            <h5>Informasi Tiket</h5>
            @if ($pesanan->tiket)
                <p><strong>Maskapai:</strong> {{ $pesanan->tiket->maskapai }}</p>
                <p><strong>Rute:</strong> {{ $pesanan->tiket->bandara_asal }} &rarr; {{ $pesanan->tiket->bandara_tujuan }}</p>
                <p><strong>Tanggal Keberangkatan:</strong> {{ \Carbon\Carbon::parse($pesanan->tiket->tanggal_keberangkatan)->format('d M Y H:i') }}</p>
                <p><strong>Harga per Tiket:</strong> Rp{{ number_format($pesanan->tiket->harga, 0, ',', '.') }}</p>
                <p><strong>Jumlah Tiket Dipesan:</strong> {{ $pesanan->jumlah_tiket }}</p>
            @else
                <p>Informasi tiket tidak tersedia (kemungkinan tiket telah dihapus).</p>
            @endif

            <div class="mt-4">
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary">Kembali ke Daftar</a>
                <a href="{{ route('admin.pesanan.edit', $pesanan->id) }}" class="btn btn-primary">Edit Status</a>
            </div>
        </div>
    </div>
</div>
@endsection