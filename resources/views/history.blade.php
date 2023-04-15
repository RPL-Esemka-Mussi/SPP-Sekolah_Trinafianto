@extends('main.navbar')

@section('content')
<table class="table table-bordered table-striped">
    <div class="text-center py-3">
        <h4><b>History Anda</b></h4>
    </div>
    <div class="d-flex justify-content-center align-items-center">
        <div class="me-2"><b>NIS :{{ $siswa->nis }}</b></div>
        <div class="me-2"><b>Nama :{{ $siswa->user->name }}</b></div>
        <div><b>Kelas:{{ $siswa->kelas->kelas }}</b></div>
    </div>
    <thead>
        <tr>
            <td><b>#</b></td>
            <td><b>Petugas</b></td>
            <td><b>Tanggal</b></td>
            <td><b>Tahun</b></td>
            <td><b>Jumlah Bayar</b></td>
        </tr>
    </thead>
    <tbody>
        @if ($pembayaran->count() == 0)
            <tr class="text-center">
                <td colspan="7"><b>KOSONG</b></td>
            </tr>
        @else
            @foreach ($pembayaran as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->user->name }}</td>
                    <td>{{ $data->tanggal_bayar }}</td>
                    <td>{{ $data->spp->tahun }}</td>
                    <td>{{ 'Rp.' . $data->jumlah_bayar }}</td>
                </tr>
            @endforeach
        @endif

    </tbody>
</table>
@endsection