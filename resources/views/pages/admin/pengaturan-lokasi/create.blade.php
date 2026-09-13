@extends('layouts.main')

@section('title', 'Tambah Lokasi Presensi')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Tambah Lokasi Presensi</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.pengaturan-lokasi.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control" value="{{ old('nama_lokasi') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="text" name="latitude" class="form-control" value="{{ old('latitude') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="text" name="longitude" class="form-control" value="{{ old('longitude') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Radius (meter)</label>
                        <input type="number" name="radius" class="form-control" value="{{ old('radius') }}" min="1" required>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('admin.pengaturan-lokasi.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
