{{-- resources/views/public/tiket/show.blade.php --}}
@extends('layouts.appp') {{-- Adjust if your layout is different, e.g., layouts.guest --}}

@section('title', 'Detail Tiket ' . $tiket->maskapai)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
            <div class="md:flex-shrink-0">
                @if($tiket->gambar)
                    <img class="h-48 w-full object-cover md:w-48" src="{{ asset('storage/' . $tiket->gambar) }}" alt="Gambar Maskapai {{ $tiket->maskapai }}">
                @else
                    <div class="h-48 w-full bg-gray-200 flex items-center justify-center md:w-48">
                        <i class="fas fa-plane-departure text-gray-400 text-6xl"></i>
                    </div>
                @endif
            </div>
            <div class="p-8 flex-grow">
                <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">Detail Tiket</div>
                <h1 class="block mt-1 text-3xl leading-tight font-extrabold text-gray-900">{{ $tiket->maskapai }}</h1>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                    <div>
                        <p class="font-semibold">Rute:</p>
                        <p>{{ $tiket->bandara_asal }} <i class="fas fa-arrow-right mx-2 text-indigo-500"></i> {{ $tiket->bandara_tujuan }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Tanggal Keberangkatan:</p>
                        <p>{{ \Carbon\Carbon::parse($tiket->tanggal_keberangkatan)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Jam Keberangkatan:</p>
                        <p>{{ \Carbon\Carbon::parse($tiket->jam_keberangkatan)->format('H:i') }} WIB</p>
                    </div>
                    <div>
                        <p class="font-semibold">Harga:</p>
                        <p class="text-3xl font-bold text-green-600">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Stok Tersedia:</p>
                        <p class="text-xl font-bold {{ $tiket->stok > 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ $tiket->stok }}
                        </p>
                    </div>
                    <div>
                        <p class="font-semibold">Status Tiket:</p>
                        <p class="capitalize">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($tiket->status === 'tersedia') bg-green-100 text-green-800
                                @elseif($tiket->status === 'habis') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $tiket->status }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('tiket.index') }}" class="px-6 py-3 border border-gray-300 rounded-md text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                    @if ($tiket->stok > 0)
                        <a href="{{ route('tiket.pesan', $tiket->id) }}" class="px-6 py-3 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-ticket-alt mr-2"></i> Pesan Sekarang
                        </a>
                    @else
                        <button class="px-6 py-3 bg-gray-400 text-white rounded-md font-medium cursor-not-allowed" disabled>
                            <i class="fas fa-times-circle mr-2"></i> Tiket Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection