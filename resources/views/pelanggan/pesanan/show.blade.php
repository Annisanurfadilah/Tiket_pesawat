{{-- resources/views/pelanggan/pesanan/show.blade.php --}}
@extends('layouts.appp')

@section('title', 'Detail Pesanan #' . $pesanan->kode_booking)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan #{{ $pesanan->kode_booking }}</h1>
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                @if($pesanan->status_pesanan == 'menunggu_pembayaran') bg-yellow-100 text-yellow-800
                @elseif($pesanan->status_pesanan == 'diproses') bg-blue-100 text-blue-800
                @elseif($pesanan->status_pesanan == 'selesai') bg-green-100 text-green-800
                @elseif($pesanan->status_pesanan == 'dibatalkan' || $pesanan->status_pesanan == 'gagal') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ str_replace('_', ' ', strtoupper($pesanan->status_pesanan)) }}
            </span>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Informasi:</strong>
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-gray-700">
            <div>
                <p class="font-semibold text-lg text-gray-800 mb-2">Informasi Pesanan</p>
                <p class="mb-2"><strong class="w-32 inline-block">ID Pesanan:</strong> {{ $pesanan->id }}</p>
                <p class="mb-2"><strong class="w-32 inline-block">Tanggal Pesan:</strong> {{ $pesanan->created_at->format('d F Y, H:i') }}</p>
                <p class="mb-2"><strong class="w-32 inline-block">Jumlah Tiket:</strong> {{ $pesanan->jumlah_tiket }}</p>
                <p class="mb-2"><strong class="w-32 inline-block">Total Harga:</strong> <span class="text-green-600 font-bold text-xl">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span></p>
                <p class="mb-2"><strong class="w-32 inline-block">Status Pembayaran:</strong> <span class="capitalize">{{ str_replace('_', ' ', $pesanan->status_pembayaran) }}</span></p>
                @if($pesanan->midtrans_transaction_status)
                    <p class="mb-2"><strong class="w-32 inline-block">Midtrans Status:</strong> <span class="capitalize">{{ str_replace('_', ' ', $pesanan->midtrans_transaction_status) }}</span></p>
                @endif
            </div>

            @if($pesanan->tiket)
            <div>
                <p class="font-semibold text-lg text-gray-800 mb-2">Detail Tiket</p>
                <p class="mb-2"><strong class="w-32 inline-block">Maskapai:</strong> {{ $pesanan->tiket->maskapai }}</p>
                <p class="mb-2"><strong class="w-32 inline-block">Rute:</strong> {{ $pesanan->tiket->bandara_asal }} &rarr; {{ $pesanan->tiket->bandara_tujuan }}</p>
                <p class="mb-2"><strong class="w-32 inline-block">Tanggal Berangkat:</strong> {{ \Carbon\Carbon::parse($pesanan->tiket->tanggal_keberangkatan)->format('d F Y') }}</p>
                <p class="mb-2"><strong class="w-32 inline-block">Jam Berangkat:</strong> {{ \Carbon\Carbon::parse($pesanan->tiket->jam_keberangkatan)->format('H:i') }} WIB</p>
                <p class="mb-2"><strong class="w-32 inline-block">Harga Tiket Satuan:</strong> Rp {{ number_format($pesanan->tiket->harga, 0, ',', '.') }}</p>
            </div>
            @else
            <div class="col-span-full">
                <p class="text-red-500">Informasi tiket tidak tersedia (kemungkinan tiket telah dihapus).</p>
            </div>
            @endif
        </div>

        <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            <a href="{{ route('pelanggan.pesanan.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat Pesanan
            </a>

            @if($pesanan->isPendingPayment() || $pesanan->status_pembayaran === 'failed_midtrans_init' || $pesanan->status_pembayaran === 'challenge')
                {{-- Tombol untuk menampilkan pop-up pembayaran. ID 'pay-button' penting untuk JS. --}}
                <button id="pay-button"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                </button>
                <form action="{{ route('pelanggan.pesanan.cancel', $pesanan->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan pesanan ini?');" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-md font-medium hover:bg-red-700 transition-colors duration-200">
                        <i class="fas fa-times-circle mr-2"></i> Batalkan Pesanan
                    </button>
                </form>
            @elseif($pesanan->status_pesanan === 'selesai')
                <span class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-md font-medium">
                    <i class="fas fa-check-circle mr-2"></i> Pesanan Selesai
                </span>
            @elseif($pesanan->status_pembayaran === 'failed' || $pesanan->status_pembayaran === 'expired' || $pesanan->status_pembayaran === 'cancelled' || $pesanan->status_pesanan === 'gagal')
                 {{-- Tombol "Coba Lagi Pembayaran" akan me-redirect ke show, yang akan memicu pop-up --}}
                 <form action="{{ route('pelanggan.pesanan.retry-payment', $pesanan->id) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-md font-medium hover:bg-yellow-700 transition-colors duration-200">
                        <i class="fas fa-redo-alt mr-2"></i> Coba Lagi Pembayaran
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- Pastikan ini berada di bagian akhir body atau di head, sebelum script utama Anda --}}
<script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script type="text/javascript">
    const payButton = document.getElementById('pay-button');

    @if ($snapToken)
        // Fungsi untuk memicu pop-up Midtrans
        function showMidtransPopup() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    alert("Pembayaran Berhasil!");
                    console.log(result);
                    window.location.href = "{{ route('pelanggan.pesanan.index') }}"; // Redirect ke halaman finish Anda
                },
                onPending: function(result) {
                    alert("Pembayaran Pending!");
                    console.log(result);
                    // Anda bisa memilih untuk tetap di halaman ini atau refresh
                    // window.location.reload();
                },
                onError: function(result) {
                    alert("Pembayaran Gagal!");
                    console.log(result);
                    window.location.href = "{{ route('midtrans.error') }}"; // Redirect ke halaman error Anda
                },
                onClose: function() {
                    // alert('Anda menutup pop-up tanpa menyelesaikan pembayaran.');
                    // Opsional: refresh halaman untuk memeriksa status pembayaran terbaru
                    // window.location.reload();
                }
            });
        }

        // HAPUS setTimeout() untuk mencegah auto-popup.
        // Sekarang pop-up hanya akan muncul saat tombol diklik.

        // Tambahkan event listener ke tombol "Bayar Sekarang" jika tombol itu ada
        if (payButton) {
            payButton.addEventListener('click', showMidtransPopup);
        }
    @else
        // Jika tidak ada snapToken (misal sudah dibayar atau ada error fatal),
        // bisa sembunyikan tombol atau beri alert info.
        if (payButton) {
            // Jika tombol 'pay-button' muncul tapi tidak ada snapToken,
            // ini berarti pesanan sudah tidak lagi pending atau ada masalah.
            // Anda bisa menyembunyikan tombol atau mengubah teksnya.
            // payButton.style.display = 'none'; // Sembunyikan tombol
            payButton.addEventListener('click', function() {
                alert('Pembayaran tidak diperlukan atau tidak dapat dilanjutkan. Silakan cek status pesanan Anda.');
            });
        }
    @endif
</script>
@endsection