@extends('layouts.appp')

@section('title', 'Detail Tiket - ' . $tiket->maskapai)

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('pelanggan.tiket.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Daftar Tiket
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500">Detail Tiket</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Content --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                    <h1 class="text-2xl font-bold mb-2">{{ $tiket->maskapai }}</h1>
                    <div class="flex items-center space-x-4">
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                            {{ $tiket->status == 'tersedia' ? 'Tersedia' : 'Tidak Tersedia' }}
                        </span>
                    </div>
                </div>

                {{-- Image --}}
                @if($tiket->gambar)
                    <div class="h-64 overflow-hidden">
                        <img src="{{ Storage::url($tiket->gambar) }}" 
                             alt="{{ $tiket->maskapai }}" 
                             class="w-full h-full object-cover">
                    </div>
                @endif

                {{-- Flight Details --}}
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Detail Penerbangan</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Departure --}}
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-800 mb-2">Keberangkatan</h3>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $tiket->bandara_tujuan }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    <div class="mt-8">
                        <h3 class="font-semibold text-gray-800 mb-4">Informasi Tambahan</h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Bagasi cabin 7kg sudah termasuk
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Dapat melakukan reschedule dengan biaya tambahan
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Check-in online tersedia 24 jam sebelum keberangkatan
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold mb-4">Ringkasan Harga</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga tiket per orang:</span>
                        <span class="font-semibold">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Sudah termasuk pajak</span>
                    </div>
                </div>

                <div class="border-t pt-4 mb-6">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span class="text-blue-600">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($tiket->status == 'tersedia')
                    <div class="space-y-3">
                        <a href="{{ route('pelanggan.tiket.pesan', $tiket) }}" 
                           class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 text-center font-semibold block">
                            Pesan Sekarang
                        </a>
                        <button type="button" 
                                onclick="shareTicket()"
                                class="w-full bg-gray-200 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-300 transition duration-200 text-center font-semibold">
                            Bagikan Tiket
                        </button>
                    </div>
                @else
                    <div class="bg-red-100 border border-red-200 rounded-lg p-4 text-center">
                        <p class="text-red-600 font-semibold">Tiket Tidak Tersedia</p>
                        <p class="text-red-500 text-sm mt-1">Maaf, tiket ini sudah habis</p>
                    </div>
                @endif

                {{-- Contact Info --}}
                <div class="mt-8 pt-6 border-t">
                    <h3 class="font-semibold mb-3">Butuh Bantuan?</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-600">+62 21 1234 5678</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-600">support@tiketku.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function shareTicket() {
    if (navigator.share) {
        navigator.share({
            title: 'Tiket {{ $tiket->maskapai }}',
            text: 'Lihat tiket penerbangan {{ $tiket->bandara_asal }} ke {{ $tiket->bandara_tujuan }} dengan harga Rp {{ number_format($tiket->harga, 0, ",", ".") }}',
            url: window.location.href
        });
    } else {
        // Copy to clipboard as fallback
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link tiket berhasil disalin ke clipboard!');
        });
    }
}
</script>
@endsection