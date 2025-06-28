@extends('layouts.app') {{-- Ganti dengan layout kamu jika berbeda --}}

@section('title', 'Detail Pesanan')

@section('content')
<div class="min-h-screen bg-cover bg-center flex items-center justify-center px-4 py-10" style="background-image: url('{{ asset('images/bg-airline.png') }}');">
    <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-lg p-8 w-full max-w-4xl">
        <div class="flex justify-between items-start mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Detail Pesanan <span class="text-blue-600">#{{ $pesanan->kode_booking }}</span></h2>
            @if($pesanan->status_pembayaran === 'failed')
                <span class="text-sm bg-red-100 text-red-600 px-3 py-1 rounded-full font-semibold">GAGAL</span>
            @endif
        </div>

        <div class="grid md:grid-cols-2 gap-6 text-sm text-gray-700">
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Informasi Pesanan</h4>
                <p><strong>ID Pesanan:</strong> {{ $pesanan->id }}</p>
                <p><strong>Tanggal Pesan:</strong> {{ $pesanan->created_at->format('d F Y, H:i') }}</p>
                <p><strong>Jumlah Tiket:</strong> {{ $pesanan->jumlah_tiket }}</p>
                <p><strong>Total Harga:</strong> <span class="text-green-600 font-bold">Rp {{ number_format($pesanan->total_harga) }}</span></p>
                <p><strong>Status Pembayaran:</strong> {{ $pesanan->status_pembayaran }}</p>
                <p><strong>Midtrans Status:</strong> {{ $pesanan->midtrans_status ?? 'Pending' }}</p>
            </div>

            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Detail Tiket</h4>
                <p><strong>Maskapai:</strong> {{ $pesanan->maskapai }}</p>
                <p><strong>Rute:</strong> {{ $pesanan->rute }}</p>
                <p><strong>Tanggal Berangkat:</strong> {{ $pesanan->tanggal_berangkat->format('d F Y') }}</p>
                <p><strong>Jam Berangkat:</strong> {{ $pesanan->jam_berangkat }}</p>
                <p><strong>Harga Tiket Satuan:</strong> Rp {{ number_format($pesanan->harga_satuan) }}</p>
            </div>
        </div>

        <hr class="my-6">

        <div class="flex justify-between flex-wrap gap-3">
            <a href="{{ route('pelanggan.pesanan.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100">
                &larr; Kembali ke Riwayat Pesanan
            </a>

            @if($pesanan->status_pembayaran === 'pending')
                <form action="{{ route('pelanggan.pesanan.bayar', $pesanan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Bayar Sekarang
                    </button>
                </form>
                <form action="{{ route('pelanggan.pesanan.batal', $pesanan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
