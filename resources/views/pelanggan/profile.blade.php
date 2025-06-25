@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Profil Pelanggan</h1>
        <p>Nama: {{ $pelanggan->nama }}</p>
        <p>Email: {{ $pelanggan->email }}</p>
        <p>Alamat: {{ $pelanggan->alamat }}</p>
    </div>
@endsection
