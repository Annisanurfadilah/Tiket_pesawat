@extends('layouts.appp') {{-- Adjust if your layout is different, e.g., layouts.guest --}}

@section('title', 'Konfirmasi Pemesanan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-teal-100 flex items-center justify-center py-10">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="flex flex-col items-center justify-center mb-6">
            <div class="bg-green-100 p-4 rounded-full mb-4">
                <i class="fas fa-check-circle text-green-600 text-5xl"></i>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 mb-3">Pemesanan Berhasil!</h1>
        </div>

        @if(session('success'))
            <p class="text-green-700 text-lg mb-6">{{ session('success') }}</p>
        @else
            <p class="text-gray-700 text-lg mb-6">Pesanan Anda telah berhasil dibuat. Silakan selesaikan pembayaran untuk mengkonfirmasi tiket Anda.</p>
        @endif

        {{-- Contoh menampilkan detail pesanan jika Anda melewatkannya dari controller --}}
        @if(isset($pesanan))
            <div class="bg-gray-50 p-6 rounded-lg mb-6 text-left">
                <h2 class="text-xl font-bold text-gray-800 mb-3">Detail Pesanan Anda:</h2>
                <p class="text-gray-700 mb-1"><strong class="w-2/5 inline-block">Kode Booking:</strong> {{ $pesanan->kode_booking ?? 'N/A' }}</p>
                <p class="text-gray-700 mb-1"><strong class="w-2/5 inline-block">Jumlah Tiket:</strong> {{ $pesanan->jumlah_tiket ?? 'N/A' }}</p>
                <p class="text-gray-700 mb-1"><strong class="w-2/5 inline-block">Total Harga:</strong> Rp {{ number_format($pesanan->total_harga ?? 0, 0, ',', '.') }}</p>
                <p class="text-gray-700 mb-1"><strong class="w-2/5 inline-block">Status:</strong> <span class="capitalize">{{ $pesanan->status_pesanan ?? 'N/A' }}</span></p>
                {{-- Anda bisa menambahkan lebih banyak detail tiket dari $pesanan->tiket --}}
                @if(isset($pesanan->tiket))
                    <p class="text-gray-700 mb-1"><strong class="w-2/5 inline-block">Maskapai:</strong> {{ $pesanan->tiket->maskapai }}</p>
                    <p class="text-gray-700 mb-1"><strong class="w-2/5 inline-block">Rute:</strong> {{ $pesanan->tiket->bandara_asal }} &rarr; {{ $pesanan->tiket->bandara_tujuan }}</p>
                @endif
            </div>

            {{-- Tombol untuk melanjutkan ke pembayaran jika menggunakan Midtrans --}}
            @if(isset($pesanan->url_pembayaran_midtrans) && $pesanan->status_pembayaran === 'pending')
                <a href="{{ $pesanan->url_pembayaran_midtrans }}" target="_blank" class="inline-flex items-center bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold text-lg hover:bg-purple-700 transition-colors duration-200 mb-4">
                    <i class="fas fa-credit-card mr-3"></i> Lanjutkan Pembayaran
                </a>
            @endif
        @endif

        <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4 mt-6">
            <a href="{{ route('tiket.index') }}" class="inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-ticket-alt mr-2"></i> Cari Tiket Lain
            </a>
            @auth
                <a href="{{ route('pelanggan.pesanan.history') }}" class="inline-flex items-center justify-center bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors duration-200">
                    <i class="fas fa-history mr-2"></i> Riwayat Pesanan
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection