{{-- resources/views/pelanggan/pesanan/pembayaran.blade.php --}}
@extends('layouts.appp') {{-- Sesuaikan layout Anda --}}

@section('content')
<div class="container">
    <h2>Detail Pesanan & Pembayaran</h2>
    <p>Kode Booking: <strong>{{ $pesanan->kode_booking }}</strong></p>
    <p>Total Harga: <strong>Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</strong></p>
    <p>Status Pembayaran: <span class="badge {{ $pesanan->status_pembayaran == 'pending' ? 'bg-warning' : 'bg-success' }}">{{ $pesanan->status_pembayaran }}</span></p>

    @if($pesanan->status_pembayaran == 'pending')
        <button id="pay-button" class="btn btn-primary">Lanjutkan Pembayaran</button>
    @else
        <p class="alert alert-success">Pembayaran sudah diterima. Terima kasih!</p>
    @endif

    <a href="{{ route('pelanggan.pesanan.index') }}" class="btn btn-secondary mt-3">Kembali ke Daftar Pesanan</a>
</div>

{{-- Midtrans Snap Embed --}}
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        // SnapToken dari controller
        Snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                /* You may add your own implementation here */
                alert("Pembayaran berhasil!");
                console.log(result);
                window.location.href = "{{ route('pelanggan.pesanan.show', $pesanan->id) }}"; // Redirect ke detail pesanan
            },
            onPending: function(result){
                /* You may add your own implementation here */
                alert("Pembayaran tertunda!");
                console.log(result);
                window.location.href = "{{ route('pelanggan.pesanan.show', $pesanan->id) }}";
            },
            onError: function(result){
                /* You may add your own implementation here */
                alert("Pembayaran gagal!");
                console.log(result);
                window.location.href = "{{ route('pelanggan.pesanan.show', $pesanan->id) }}";
            },
            onClose: function(){
                /* You may add your own implementation here */
                alert('Anda menutup popup tanpa menyelesaikan pembayaran');
                // Biasanya tidak langsung redirect, biarkan user mencoba lagi atau kembali ke detail pesanan
            }
        });
    };
</script>
@endsection