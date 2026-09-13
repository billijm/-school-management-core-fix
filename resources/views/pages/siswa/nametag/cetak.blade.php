@extends('layouts.main')
@section('title', 'Cetak Nametag')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card text-center p-4">
                    <h4 class="mb-2">Nametag Siswa</h4>
                    <div class="mb-3">
                        {{-- QR Code --}}
                        {!! QrCode::size(150)->generate($user->id . '|' . $user->name) !!}
                    </div>
                    <div>
                        <p><strong>Nama:</strong> {{ $user->name }}</p>
                        <p><strong>NIS:</strong> {{ $user->nis ?? '-' }}</p>
                        <p><strong>Kelas:</strong> {{ $user->kelas->nama ?? '-' }}</p>
                        @if($pengaturan)
                        <p><small class="text-muted">Sekolah: {{ $pengaturan->nama_sekolah }}</small></p>
                        @endif
                    </div>
                    <button class="btn btn-primary mt-3" onclick="window.print()">Cetak Nametag</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        button {
            display: none;
        }
    }
</style>
@endpush
