@extends('layouts.appp')

@section('title', 'Daftar Tiket Pesawat')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Cari Tiket Pesawat</h1>
        <p class="text-gray-600">Temukan penerbangan terbaik dengan harga terjangkau</p>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('pelanggan.tiket.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Bandara Asal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari</label>
                    <input type="text" 
                           name="bandara_asal" 
                           value="{{ request('bandara_asal') }}"
                           placeholder="Kota atau Bandara Asal"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Bandara Tujuan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ke</label>
                    <input type="text" 
                           name="bandara_tujuan" 
                           value="{{ request('bandara_tujuan') }}"
                           placeholder="Kota atau Bandara Tujuan"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berangkat</label>
                    <input type="date" 
                           name="tanggal_keberangkatan" 
                           value="{{ request('tanggal_keberangkatan') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Search Button --}}
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        Cari Tiket
                    </button>
                </div>
            </div>

            {{-- Advanced Filters --}}
            <div class="border-t pt-4 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Minimum</label>
                        <input type="number" 
                               name="harga_min" 
                               value="{{ request('harga_min') }}"
                               placeholder="0"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Maksimum</label>
                        <input type="number" 
                               name="harga_max" 
                               value="{{ request('harga_max') }}"
                               placeholder="10000000"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan Berdasarkan</label>
                        <select name="sort_by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="tanggal_keberangkatan" {{ request('sort_by') == 'tanggal_keberangkatan' ? 'selected' : '' }}>Tanggal</option>
                            <option value="harga" {{ request('sort_by') == 'harga' ? 'selected' : '' }}>Harga</option>
                            <option value="jam_keberangkatan" {{ request('sort_by') == 'jam_keberangkatan' ? 'selected' : '' }}>Jam</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if($tikets->count() > 0)
        <div class="mb-4 flex justify-between items-center">
            <p class="text-gray-600">Ditemukan {{ $tikets->total() }} tiket</p>
        </div>

        <div class="space-y-4">
            @foreach($tikets as $tiket)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                    <div class="flex flex-col md:flex-row">
                        {{-- Image --}}
                        @if($tiket->gambar)
                            <div class="md:w-48 h-48 md:h-auto">
                                <img src="{{ Storage::url($tiket->gambar) }}" 
                                     alt="{{ $tiket->maskapai }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        @endif

                        {{-- Content --}}
                        <div class="flex-1 p-6">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $tiket->maskapai }}</h3>
                                    
                                    <div class="flex items-center space-x-4 text-gray-600 mb-4">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ $tiket->bandara_asal }}</span>
                                        </div>
                                        <div class="text-gray-400">→</div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ $tiket->bandara_tujuan }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $tiket->tanggal_keberangkatan->format('d M Y') }}
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $tiket->jam_keberangkatan->format('H:i') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 md:mt-0 md:text-right">
                                    <div class="text-2xl font-bold text-blue-600 mb-2">
                                        Rp {{ number_format($tiket->harga, 0, ',', '.') }}
                                    </div>
                                    <div class="space-y-2">
                                        <a href="{{ route('pelanggan.tiket.show', $tiket) }}" 
                                           class="inline-block bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition duration-200 text-sm">
                                            Detail
                                        </a>
                                        <a href="{{ route('pelanggan.tiket.pesan', $tiket) }}" 
                                           class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 text-sm ml-2">
                                            Pesan Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $tikets->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.291.94-5.709 2.291M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada tiket ditemukan</h3>
            <p class="mt-2 text-gray-500">Coba ubah kriteria pencarian Anda</p>
        </div>
    @endif
</div>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
        {{ session('error') }}
    </div>
@endif
@endsection