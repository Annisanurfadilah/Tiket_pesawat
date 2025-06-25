@extends('layouts.appp')

@section('title', 'Konfirmasi Pemesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        {{-- Success Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Pemesanan Berhasil!</h1>
            <p class="text-gray-600">Terima kasih telah memesan tiket dengan kami. Detail pemesanan Anda:</p>
            <p class="text-gray-600 font-semibold">Kode Booking: <span class="text-blue-600">{{ $pesananModel->kode_booking }}</span></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Booking Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Flight Information --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-blue-600 text-white p-4">
                        <h2 class="text-xl font-semibold">Detail Penerbangan</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold">{{ $pemesanan['tiket']->maskapai }}</h3>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                Confirmed
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Dari</label>
                                    <p class="text-lg font-semibold text-gray-800">{{ $pemesanan['tiket']->bandara_asal }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Tanggal & Waktu Keberangkatan</label>
                                    <p class="text-lg font-semibold text-gray-800">
                                        {{ $pemesanan['tiket']->tanggal_keberangkatan->format('d F Y') }}
                                    </p>
                                    <p class="text-gray-600">{{ $pemesanan['tiket']->jam_keberangkatan->format('H:i') }} WIB</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Ke</label>
                                    <p class="text-lg font-semibold text-gray-800">{{ $pemesanan['tiket']->bandara_tujuan }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500">Jumlah Penumpang</label>
                                    <p class="text-lg font-semibold text-gray-800">{{ $pemesanan['jumlah_tiket'] }} Orang</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Passenger Information --}}
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-50 p-4 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">Informasi Pemesan</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nama Lengkap</label>
                                <p class="text-lg font-semibold text-gray-800">{{ $pemesanan['nama_pemesan'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email</label>
                                <p class="text-lg font-semibold text-gray-800">{{ $pemesanan['email_pemesan'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nomor Telepon</label>
                                <p class="text-lg font-semibold text-gray-800">{{ $pemesanan['telepon_pemesan'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Alamat</label>
                                <p class="text-lg font-semibold text-gray-800">{{ $pemesanan['alamat_pemesan'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Summary --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden h-fit">
                <div class="bg-gray-50 p-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800">Ringkasan Pembayaran</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-gray-700">
                        <span>Harga Tiket</span>
                        <span>Rp{{ number_format($pemesanan['tiket']->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-700">
                        <span>Jumlah Tiket</span>
                        <span>{{ $pemesanan['jumlah_tiket'] }}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between font-semibold text-lg text-gray-800">
                        <span>Total Pembayaran</span>
                        <span>Rp{{ number_format($pesananModel->total_harga, 0, ',', '.') }}</span>
                    </div>

                    {{-- Tombol untuk melanjutkan ke pembayaran Midtrans --}}
                    <div class="mt-6 text-center">
                        <a href="{{ route('pelanggan.pesanan.show', $pesananModel->id) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Lanjutkan Pembayaran Sekarang
                            <svg class="ml-3 -mr-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-4.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection