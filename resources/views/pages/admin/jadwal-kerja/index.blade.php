@extends('layouts.main')
@section('title', 'Manajemen Jadwal Kerja')

@section('content')
<section class="section custom-section">
<div class="section-body">
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header d-flex justify-content-between">
<h4>Manajemen Jadwal Kerja</h4>
<button class="btn btn-primary" data-toggle="modal"

data-target="#exampleModal"><i class="nav-icon fas fa-folder-
plus"></i>&nbsp; Tambah Jadwal Kerja</button>

</div>
<div class="card-body">
@include('partials.alert')
<div class="table-responsive">
<table class="table table-striped" id="table-2">
<thead>
<tr>
<th>No</th>
<th>Nama Jadwal</th>

<th>Tipe Pengguna</th>
<th>Jenis Jadwal</th> {{-- Kolom baru --}}
<th>Jam Masuk</th>
<th>Jam Pulang</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
@foreach ($jadwalKerja as $data)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $data->nama_jadwal }} <br><small
class="text-muted">Kode: {{ $data->kode_jadwal }}</small></td>
<td><span class="badge badge-secondary">{{
Str::ucfirst($data->tipe_pengguna) }}</span></td>
{{-- Tampilan status baru --}}
<td>
@if($data->is_umum)

<span class="badge badge-
primary">Jadwal Umum</span>

@else
<span class="badge badge-light">Jadwal
Khusus</span>
@endif
</td>
<td>{{ \Carbon\Carbon::parse($data->jam_masuk)->format('H:i') }}</td>

<td>{{ \Carbon\Carbon::parse($data->jam_pulang)->format('H:i') }}</td>
<td>
<div class="d-flex">
{{-- Tombol Edit Baru --}}

<a href="{{ route('admin.jadwal-kerja.edit', $data->id) }}" class="btn btn-success btn-sm"

style="margin-right: 8px"><i class="nav-icon fas fa-edit"></i> &nbsp;
Edit</a>
<form method="POST" action="{{
route('admin.jadwal-kerja.destroy', $data->id) }}">
@csrf
@method('delete')
<button class="btn btn-danger btn-sm
show_confirm" data-toggle="tooltip" title='Delete'><i class="nav-icon
fas fa-trash-alt"></i> &nbsp; Hapus</button>
</form>
</div>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>

</div>
</section>

<!-- Modal Tambah (Diperbarui dengan checkbox) -->
<div class="modal fade" tabindex="-1" role="dialog"
id="exampleModal">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Tambah Jadwal Kerja</h5>
<button type="button" class="close" data-dismiss="modal"
aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<form action="{{ route('admin.jadwal-kerja.store') }}"
method="POST">
@csrf
{{-- ... form fields ... --}}
<div class="form-group">
<label for="nama_jadwal">Nama Jadwal</label>
<input type="text" id="nama_jadwal"

name="nama_jadwal" class="form-control @error('nama_jadwal') is-
invalid @enderror" placeholder="Contoh: Jadwal Siswa Normal"

value="{{ old('nama_jadwal') }}" required>
@error('nama_jadwal') <div class="invalid-feedback">{{
$message }}</div> @enderror
</div>
<div class="form-group">

<label for="kode_jadwal">Kode Jadwal</label>
<input type="text" id="kode_jadwal"

name="kode_jadwal" class="form-control @error('kode_jadwal') is-
invalid @enderror" placeholder="Contoh: SISWA_NORMAL (Unik)"

value="{{ old('kode_jadwal') }}" required>
@error('kode_jadwal') <div class="invalid-feedback">{{
$message }}</div> @enderror
</div>
<div class="form-group">
<label for="tipe_pengguna">Tipe Pengguna</label>
<select id="tipe_pengguna" name="tipe_pengguna"
class="form-control @error('tipe_pengguna') is-invalid @enderror"
required>
<option value="">-- Pilih Tipe --</option>
<option value="siswa" @if(old('tipe_pengguna') ==
'siswa') selected @endif>Siswa</option>
<option value="guru" @if(old('tipe_pengguna') ==
'guru') selected @endif>Guru</option>
<option value="karyawan" @if(old('tipe_pengguna')
== 'karyawan') selected @endif>Karyawan</option>
<option value="orangtua" @if(old('tipe_pengguna') ==
'orangtua') selected @endif>Orang Tua</option>
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
invalid @enderror" value="{{ old('jam_masuk') }}" required>

@error('jam_masuk') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>
</div>
<div class="col-md-6">
<div class="form-group">
<label for="jam_pulang">Jam Pulang</label>
<input type="time" id="jam_pulang"

name="jam_pulang" class="form-control @error('jam_pulang') is-
invalid @enderror" value="{{ old('jam_pulang') }}" required>

@error('jam_pulang') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>
</div>
</div>
<div class="form-group">
<label for="toleransi_terlambat_menit">Toleransi
Keterlambatan (menit)</label>
<input type="number" id="toleransi_terlambat_menit"
name="toleransi_terlambat_menit" class="form-control
@error('toleransi_terlambat_menit') is-invalid @enderror" value="{{
old('toleransi_terlambat_menit', 0) }}" required>

@error('toleransi_terlambat_menit') <div class="invalid-
feedback">{{ $message }}</div> @enderror

</div>
{{-- Checkbox Baru --}}
<div class="form-group">

<div class="form-check">
<input class="form-check-input" type="checkbox"
name="is_umum" id="is_umum_modal">
<label class="form-check-label"
for="is_umum_modal">
Jadikan Jadwal Umum
</label>
</div>
<small class="form-text text-muted">Jika dicentang,
jadwal ini akan menjadi jadwal default untuk tipe pengguna yang
dipilih.</small>
</div>
<div class="modal-footer br">

<button type="button" class="btn btn-danger" data-
dismiss="modal">Tutup</button>

<button type="submit" class="btn btn-
primary">Simpan</button>

</div>
</form>
</div>
</div>
</div>
</div>
@endsection

@push('script')
<script type="text/javascript">
$('.show_confirm').click(function(event) {

var form = $(this).closest("form");
event.preventDefault();
swal({
title: `Yakin ingin menghapus data ini?`,
text: "Data akan terhapus secara permanen!",
icon: "warning",
buttons: true,
dangerMode: true,
})
.then((willDelete) => {
if (willDelete) {
form.submit();
}
});
});
</script>
@endpush