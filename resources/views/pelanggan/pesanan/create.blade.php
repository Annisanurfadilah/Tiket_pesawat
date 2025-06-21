@extends('layouts.appp')

@section('title', 'Buat Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Pesanan Tiket</h1>

            {{-- Ticket Information --}}
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h2 class="text-lg font-semibold text-blue-800 mb-2">Detail Tiket</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Rute</p>
                        <p class="font-medium">{{ $tiket->rute_asal }} → {{ $tiket->rute_tujuan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Maskapai</p>
                        <p class="font-medium">{{ $tiket->maskapai }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Keberangkatan</p>
                        <p class="font-medium">{{ $tiket->tanggal_keberangkatan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Waktu</p>
                        <p class="font-medium">{{ $tiket->jam_keberangkatan }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Harga per Tiket</p>
                        <p class="font-medium text-lg text-green-600">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Stok Tersedia</p>
                        <p class="font-medium">{{ $tiket->stok }} tiket</p>
                    </div>
                </div>
            </div>

            {{-- Order Form --}}
            <form action="{{ route('pelanggan.pesanan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tiket_id" value="{{ $tiket->id }}">

                <div class="mb-6">
                    <label for="jumlah_tiket" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Tiket
                    </label>
                    <select name="jumlah_tiket" id="jumlah_tiket" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updateTotal()">
                        @for($i = 1; $i <= min(5, $tiket->stok); $i++)
                            <option value="{{ $i }}">{{ $i }} tiket</option>
                        @endfor
                    </select>
                    @error('jumlah_tiket')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Price --}}
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium">Total Harga:</span>
                        <span id="total_harga" class="text-xl font-bold text-green-600">
                            Rp {{ number_format($tiket->harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        Buat Pesanan
                    </button>
                    <a href="{{ route('pelanggan.pesanan.index') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 rounded-md text-center transition duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateTotal() {
    const jumlahTiket = document.getElementById('jumlah_tiket').value;
    const hargaPerTiket = {{ $tiket->harga }};
    const total = jumlahTiket * hargaPerTiket;
    
    document.getElementById('total_harga').textContent = 
        'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endsection