@extends('layouts.appp')

@section('title', 'Dashboard Pelanggan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Pelanggan</h1>
                    <p class="text-gray-600 mt-1">Selamat datang kembali, {{ $pelanggan->name }}!</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg shadow-lg">
                        <i class="fas fa-user-circle mr-2"></i>
                        {{ $pelanggan->name }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Pesanan Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Pesanan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalPesanan }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $pesananBulanIni ?? 0 }} bulan ini</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium">
                        <i class="fas fa-arrow-up mr-1"></i>
                        Semua waktu
                    </span>
                </div>
            </div>

            <!-- Pesanan Selesai Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pesanan Selesai</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pesananSelesai ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $pesananProses ?? 0 }} sedang proses</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-blue-600 text-sm font-medium">
                        <i class="fas fa-clock mr-1"></i>
                        @if($pesananTerakhir)
                            {{ $pesananTerakhir->created_at->diffForHumans() }}
                        @else
                            Tidak ada data
                        @endif
                    </span>
                </div>
            </div>

            <!-- Total Pengeluaran Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Pengeluaran</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Pesanan selesai</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-wallet text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('pelanggan.tiket.index') }}" class="text-purple-600 text-sm font-medium hover:text-purple-800 transition-colors">
                        <i class="fas fa-plus mr-1"></i>
                        Pesan Tiket
                    </a>
                </div>
            </div>

            <!-- Status Terakhir Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Status Terakhir</p>
                        @if($pesananTerakhir)
                            <p class="text-lg font-bold text-gray-900 mt-2 capitalize">
                                {{ $pesananTerakhir->status ?? 'Menunggu' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Pesanan #{{ $pesananTerakhir->id }}</p>
                        @else
                            <p class="text-lg font-bold text-gray-500 mt-2">Belum ada pesanan</p>
                        @endif
                    </div>
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-info-circle text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('pelanggan.profile') }}" class="text-orange-600 text-sm font-medium hover:text-orange-800 transition-colors">
                        <i class="fas fa-user mr-1"></i>
                        Kelola Profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Orders -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-history mr-2 text-blue-600"></i>
                            Pesanan Terakhir
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($pesananTerakhir)
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-blue-100 p-2 rounded-full">
                                            <i class="fas fa-receipt text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">
                                                Pesanan #{{ $pesananTerakhir->id }}
                                            </p>
                                            <p class="text-gray-600 text-sm">
                                                {{ $pesananTerakhir->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($pesananTerakhir->status == 'selesai') bg-green-100 text-green-800
                                            @elseif($pesananTerakhir->status == 'proses') bg-yellow-100 text-yellow-800
                                            @elseif($pesananTerakhir->status == 'batal') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            <i class="fas fa-circle mr-1 text-xs"></i>
                                            {{ ucfirst($pesananTerakhir->status ?? 'Menunggu') }}
                                        </span>
                                        @if(isset($pesananTerakhir->total))
                                            <p class="text-lg font-bold text-gray-900 mt-1">
                                                Rp {{ number_format($pesananTerakhir->total, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex justify-center">
                                    <a href="{{ route('pelanggan.pesanan.show', $pesananTerakhir->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        <i class="fas fa-eye mr-2"></i>
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 mb-4">Belum ada pesanan</p>
                                <a href="{{ route('pelanggan.tiket.index') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Cari Tiket Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Sidebar -->
            <div class="space-y-6">
                <!-- Profile Summary -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            Profil Saya
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-user text-white text-2xl"></i>
                            </div>
                            <h4 class="font-semibold text-gray-900">{{ $pelanggan->name }}</h4>
                            <p class="text-gray-600 text-sm">{{ $pelanggan->email }}</p>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('pelanggan.profile') }}" 
                               class="block w-full text-center py-2 px-4 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                <i class="fas fa-edit mr-2"></i>
                                Edit Profil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-bolt mr-2"></i>
                            Aksi Cepat
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('pelanggan.pesanan.index') }}" 
                           class="flex items-center w-full py-3 px-4 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                            <i class="fas fa-list mr-3"></i>
                            Lihat Semua Pesanan
                        </a>
                        <a href="{{ route('pelanggan.tiket.index') }}" 
                            class="flex items-center w-full py-3 px-4 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors duration-200">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Cari & Pesan Tiket
                        </a>
                        <!--  -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .hover\:scale-105:hover {
        transform: scale(1.05);
    }
    
    .transition-transform {
        transition: transform 0.2s ease-in-out;
    }
    
    .transition-colors {
        transition: color 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<script>
    // Add some interactive animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate cards on load
        const cards = document.querySelectorAll('.transform');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endpush
@endsection