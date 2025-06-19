{{-- resources/views/public/tiket/pesan.blade.php --}}
@extends('layouts.appp') {{-- Adjust if your layout is different, e.g., layouts.guest --}}

@section('title', 'Pesan Tiket ' . $tiket->maskapai)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 bg-white rounded-xl shadow-lg p-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6 text-center">Pesan Tiket</h1>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="border-b pb-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $tiket->maskapai }}</h2>
            <p class="text-gray-600">{{ $tiket->bandara_asal }} &rarr; {{ $tiket->bandara_tujuan }}</p>
            <p class="text-gray-500 text-sm mt-1">
                Tanggal: {{ \Carbon\Carbon::parse($tiket->tanggal_keberangkatan)->format('d F Y') }}
                Jam: {{ \Carbon\Carbon::parse($tiket->jam_keberangkatan)->format('H:i') }} WIB
            </p>
            <p class="text-green-600 font-bold text-xl mt-2">Harga per tiket: Rp {{ number_format($tiket->harga, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500">Stok Tersedia: {{ $tiket->stok }}</p>
        </div>

        <form action="{{ route('tiket.proses-pesan', $tiket->id) }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="jumlah_tiket" class="block text-gray-700 text-sm font-bold mb-2">Jumlah Tiket:</label>
                <input type="number" name="jumlah_tiket" id="jumlah_tiket"
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                       min="1" max="{{ $tiket->stok }}" value="1" required>
                @error('jumlah_tiket')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Anda bisa menambahkan input lain di sini, seperti detail penumpang, jika diperlukan --}}
            {{--
            <div class="mb-5">
                <label for="nama_penumpang" class="block text-gray-700 text-sm font-bold mb-2">Nama Penumpang:</label>
                <input type="text" name="nama_penumpang" id="nama_penumpang"
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       required>
                @error('nama_penumpang')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            --}}

            <div class="flex items-center justify-between mt-8">
                <a href="{{ route('tiket.show', $tiket->id) }}" class="inline-flex items-center bg-gray-200 text-gray-700 px-5 py-2 rounded-md font-semibold hover:bg-gray-300 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" class="inline-flex items-center bg-blue-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-check-circle mr-2"></i> Lanjutkan Pemesanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection