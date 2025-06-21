{{-- resources/views/tiket/pesan.blade.php --}}
@extends('layouts.appp')

@section('title', 'Pesan Tiket - ' . $tiket->maskapai)

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- ... (Breadcrumb dan bagian lainnya) ... --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form Section --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Form Pemesanan Tiket</h1>

                <form action="{{ route('pelanggan.tiket.proses-pesan', $tiket) }}" method="POST" id="bookingForm">
                    @csrf
                    
                    {{-- Personal Information --}}
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pemesan</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_pemesan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nama_pemesan" 
                                       name="nama_pemesan" 
                                       value="{{ old('nama_pemesan') }}"
                                       placeholder="Masukkan nama lengkap"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_pemesan') border-red-500 @enderror"
                                       required>
                                @error('nama_pemesan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email"  {{-- Pastikan name ini 'email' --}}
                                       value="{{ old('email') }}"
                                       placeholder="contoh@email.com"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                                       required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" 
                                       id="no_telepon" 
                                       name="no_telepon" {{-- Pastikan name ini 'no_telepon' --}}
                                       value="{{ old('no_telepon') }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('no_telepon') border-red-500 @enderror"
                                       required>
                                @error('no_telepon')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="jumlah_tiket" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Tiket <span class="text-red-500">*</span>
                                </label>
                                <select id="jumlah_tiket" 
                                        name="jumlah_tiket" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jumlah_tiket') border-red-500 @enderror"
                                        required
                                        onchange="updateTotal()">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('jumlah_tiket', 1) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i == 1 ? 'Tiket' : 'Tiket' }}
                                        </option>
                                    @endfor
                                </select>
                                @error('jumlah_tiket')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Terms and Conditions --}}
                    <div class="mb-6">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">Syarat dan Ketentuan</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Nama penumpang harus sesuai dengan identitas yang sah</li>
                                <li>• Tiket yang sudah dibeli tidak dapat dikembalikan</li>
                                <li>• Perubahan jadwal dikenakan biaya tambahan</li>
                                <li>• Penumpang wajib check-in minimal 2 jam sebelum keberangkatan</li>
                                <li>• Bagasi kabin maksimal 7kg per penumpang</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="agree_terms" 
                                       name="agree_terms"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       required>
                                <span class="ml-2 text-sm text-gray-700">
                                    Saya menyetujui <a href="#" class="text-blue-600 hover:underline">syarat dan ketentuan</a> yang berlaku
                                </span>
                            </label>
                            @error('agree_terms')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('pelanggan.tiket.show', $tiket) }}" 
                           class="flex-1 bg-gray-200 text-gray-700 py-3 px-4 rounded-lg hover:bg-gray-300 transition duration-200 text-center font-semibold">
                            Kembali
                        </a>
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                            Lanjutkan Pemesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
                
                {{-- Flight Info --}}
                <div class="border-b pb-4 mb-4">
                    <h3 class="font-semibold text-gray-800 mb-2">{{ $tiket->maskapai }}</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Rute:</span>
                            <span>{{ $tiket->bandara_asal }} → {{ $tiket->bandara_tujuan }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tanggal:</span>
                            <span>{{ $tiket->tanggal_keberangkatan->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Waktu:</span>
                            <span>{{ $tiket->jam_keberangkatan->format('H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                {{-- Price Breakdown --}}
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga per tiket:</span>
                        <span class="font-semibold">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah tiket:</span>
                        <span class="font-semibold" id="ticket-count">1</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Pajak & biaya layanan:</span>
                        <span>Sudah termasuk</span>
                    </div>
                </div>

                <div class="border-t pt-4 mb-6">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total Pembayaran:</span>
                        <span class="text-blue-600" id="total-price">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Security Notice --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-green-700 font-medium">Transaksi Aman</span>
                    </div>
                    <p class="text-xs text-green-600 mt-1">Data Anda dilindungi dengan enkripsi SSL</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ticketPrice = {{ $tiket->harga }};

function updateTotal() {
    const jumlahTiket = document.getElementById('jumlah_tiket').value;
    const totalPrice = ticketPrice * jumlahTiket;
    
    document.getElementById('ticket-count').textContent = jumlahTiket;
    document.getElementById('total-price').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
});

// Form validation (client-side, server-side ada di controller)
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const agreeTerms = document.getElementById('agree_terms').checked;
    
    if (!agreeTerms) {
        e.preventDefault();
        alert('Anda harus menyetujui syarat dan ketentuan terlebih dahulu.');
        return false;
    }
});
</script>

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
        {{ session('error') }}
    </div>
@endif
@endsection