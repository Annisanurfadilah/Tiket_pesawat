{{-- resources/views/public/tiket/index.blade.php --}}
@extends('layouts.appp') {{-- Or your public layout, e.g., layouts.guest or layouts.app --}}

@section('title', 'Daftar Tiket Pesawat')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Jelajahi Tiket Pesawat</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    @if ($tikets->isEmpty())
        <p class="text-gray-600">Tidak ada tiket yang tersedia saat ini.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($tikets as $tiket)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('storage/' . $tiket->gambar) }}" alt="Maskapai {{ $tiket->maskapai }}" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800">{{ $tiket->maskapai }}</h2>
                        <p class="text-gray-600 mt-2">{{ $tiket->bandara_asal }} &rarr; {{ $tiket->bandara_tujuan }}</p>
                        <p class="text-gray-500 text-sm mt-1">
                            {{ \Carbon\Carbon::parse($tiket->tanggal_keberangkatan)->format('d M Y') }}
                            pukul {{ \Carbon\Carbon::parse($tiket->jam_keberangkatan)->format('H:i') }} WIB
                        </p>
                        <p class="text-gray-800 font-bold text-2xl mt-3">
                            Rp {{ number_format($tiket->harga, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">Stok: {{ $tiket->stok }}</p>
                        
                        <div class="mt-4 flex justify-between items-center">
                            <a href="{{ route('tiket.show', $tiket->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Lihat Detail
                            </a>
                            @if ($tiket->stok > 0)
                                <a href="{{ route('tiket.pesan', $tiket->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    Pesan Sekarang
                                </a>
                            @else
                                <span class="bg-red-100 text-red-700 px-4 py-2 rounded-lg text-sm">Habis</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $tikets->links() }}
        </div>
    @endif
</div>
@endsection