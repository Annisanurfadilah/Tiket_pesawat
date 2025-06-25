{{-- resources/views/pelanggan/pesanan/index.blade.php --}}
@extends('layouts.appp')

@section('title', 'Riwayat Pesanan Saya') {{-- Ubah judul --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Riwayat Pesanan Saya</h1> {{-- Ubah judul halaman --}}
        <p class="text-gray-600">Daftar semua pesanan tiket Anda.</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Kode Booking
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Maskapai & Rute
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Jumlah Tiket
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Total Harga
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status Pesanan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Tanggal Pesan
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan as $item) {{-- Ganti $tikets menjadi $pesanan --}}
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $item->kode_booking }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            @if($item->tiket)
                                <p class="text-gray-900 whitespace-no-wrap">{{ $item->tiket->maskapai }}</p>
                                <p class="text-gray-600 text-xs whitespace-no-wrap">{{ $item->tiket->bandara_asal }} &rarr; {{ $item->tiket->bandara_tujuan }}</p>
                                <p class="text-gray-600 text-xs whitespace-no-wrap">
                                    {{ \Carbon\Carbon::parse($item->tiket->tanggal_keberangkatan)->format('d M Y') }}
                                </p>
                            @else
                                <p class="text-gray-600 whitespace-no-wrap">Tiket tidak ditemukan</p>
                            @endif
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $item->jumlah_tiket }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight">
                                <span aria-hidden="true" class="absolute inset-0 opacity-50 rounded-full
                                    @if($item->status_pesanan == 'menunggu_pembayaran') bg-yellow-200
                                    @elseif($item->status_pesanan == 'diproses') bg-blue-200
                                    @elseif($item->status_pesanan == 'selesai') bg-green-200
                                    @elseif($item->status_pesanan == 'dibatalkan') bg-red-200
                                    @else bg-gray-200 @endif"></span>
                                <span class="relative capitalize">
                                    {{ str_replace('_', ' ', $item->status_pesanan) }}
                                </span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-right">
                            <a href="{{ route('pelanggan.pesanan.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                Lihat Detail
                            </a>
                            {{-- Tambahkan tombol batal jika status memungkinkan --}}
                            @if($item->status_pesanan == 'menunggu_pembayaran' || $item->status_pesanan == 'pending')
                                <form action="{{ route('pelanggan.pesanan.cancel', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin membatalkan pesanan ini?');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Batal
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                            Anda belum memiliki pesanan.
                            <a href="{{ route('pelanggan.tiket.index') }}" class="text-blue-600 hover:underline">Pesan tiket sekarang!</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        {{ $pesanan->links() }} {{-- Ganti $tikets->links() menjadi $pesanan->links() --}}
    </div>
</div>
@endsection