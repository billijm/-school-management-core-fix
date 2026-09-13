@extends('layouts.main')
@section('title', 'Edit Jadwal Kerja')

@section('content')
<section class="section custom-section">
<div class="section-body">

<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<h4>Edit Jadwal Kerja: {{ $jadwalKerja->nama_jadwal
}}</h4>
</div>
<div class="card-body">
<form action="{{ route('admin.jadwal-kerja.update',
$jadwalKerja->id) }}" method="POST">
@csrf
@method('PUT')
<div class="form-group">
<label for="nama_jadwal">Nama Jadwal</label>
<input type="text" id="nama_jadwal"

name="nama_jadwal" class="form-control @error('nama_jadwal') is-
invalid @enderror" value="{{ old('nama_jadwal', $jadwalKerja->nama_jadwal) }}" required>

@error('nama_jadwal') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>
<div class="form-group">
<label for="kode_jadwal">Kode Jadwal</label>
<input type="text" id="kode_jadwal"

name="kode_jadwal" class="form-control @error('kode_jadwal') is-
invalid @enderror" value="{{ old('kode_jadwal', $jadwalKerja->kode_jadwal) }}" required>

@error('kode_jadwal') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>

<div class="form-group">
<label for="tipe_pengguna">Tipe Pengguna</label>
<select id="tipe_pengguna" name="tipe_pengguna"
class="form-control @error('tipe_pengguna') is-invalid @enderror"
required>
<option value="siswa" @if(old('tipe_pengguna',
$jadwalKerja->tipe_pengguna) == 'siswa') selected
@endif>Siswa</option>
<option value="guru" @if(old('tipe_pengguna',
$jadwalKerja->tipe_pengguna) == 'guru') selected
@endif>Guru</option>
<option value="karyawan"
@if(old('tipe_pengguna', $jadwalKerja->tipe_pengguna) == 'karyawan')
selected @endif>Karyawan</option>
<option value="orangtua"
@if(old('tipe_pengguna', $jadwalKerja->tipe_pengguna) == 'orangtua')
selected @endif>Orang Tua</option>
</select>

@error('tipe_pengguna') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label for="jam_masuk">Jam Masuk</label>
<input type="time" id="jam_masuk"

name="jam_masuk" class="form-control @error('jam_masuk') is-
invalid @enderror" value="{{ old('jam_masuk', $jadwalKerja->jam_masuk) }}" required>

@error('jam_masuk') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>

</div>
<div class="col-md-6">
<div class="form-group">
<label for="jam_pulang">Jam Pulang</label>
<input type="time" id="jam_pulang"

name="jam_pulang" class="form-control @error('jam_pulang') is-
invalid @enderror" value="{{ old('jam_pulang', $jadwalKerja->jam_pulang) }}" required>

@error('jam_pulang') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>
</div>
</div>
<div class="form-group">
<label for="toleransi_terlambat_menit">Toleransi
Keterlambatan (menit)</label>
<input type="number"
id="toleransi_terlambat_menit" name="toleransi_terlambat_menit"
class="form-control @error('toleransi_terlambat_menit') is-invalid
@enderror" value="{{ old('toleransi_terlambat_menit', $jadwalKerja->toleransi_terlambat_menit) }}" required>
@error('toleransi_terlambat_menit') <div
class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="form-group">
<div class="form-check">
<input class="form-check-input" type="checkbox"
name="is_umum" id="is_umum_edit" @if(old('is_umum',
$jadwalKerja->is_umum)) checked @endif>
<label class="form-check-label"
for="is_umum_edit">

Jadikan Jadwal Umum
</label>
</div>
<small class="form-text text-muted">Jika
dicentang, jadwal ini akan menjadi jadwal default untuk tipe pengguna
yang dipilih.</small>
</div>

<div class="card-footer text-right">
<a href="{{ route('admin.jadwal-kerja.index') }}"
class="btn btn-secondary">Batal</a>

<button type="submit" class="btn btn-
primary">Simpan Perubahan</button>

</div>
</form>
</div>
</div>
</div>
</div>
</div>
</section>
@endsection