@extends('layouts.main')
@section('title', 'Penugasan Jadwal Khusus')

@section('content')
<section class="section custom-section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Penugasan Jadwal Khusus</h4>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                            <i class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Penugasan
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            Gunakan halaman ini untuk memberikan jadwal khusus kepada
                            <strong>individu, kelas, atau seluruh peran</strong>. Jadwal yang diatur di sini akan menimpa "Jadwal Umum".
                        </div>

                        @include('partials.alert')

                        <div class="table-responsive">
                            <table class="table table-striped" id="table-2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pengguna</th>
                                        <th>Jadwal Kerja Khusus</th>
                                        <th>Berlaku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penugasanJadwal as $data)
                                    <tr>
                                        <td>{{ $loop->iteration + $penugasanJadwal->firstItem() - 1 }}</td>
                                        <td>{{ $data->user->name ?? 'N/A' }}</td>
                                        <td><span class="badge badge-warning">{{ $data->jadwalKerja->nama_jadwal ?? 'N/A' }}</span></td>
                                        <td>
                                            @if($data->hari)
                                                Setiap Hari {{ Str::ucfirst($data->hari) }}
                                            @else
                                                {{ \Carbon\Carbon::parse($data->tanggal_mulai)->translatedFormat('d M Y') }}
                                                -
                                                {{ $data->tanggal_selesai ? \Carbon\Carbon::parse($data->tanggal_selesai)->translatedFormat('d M Y') : 'Selamanya' }}
                                            @endif
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.penugasan-jadwal.destroy', $data->id) }}">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title="Delete">
                                                    <i class="nav-icon fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="float-right">
                            {{ $penugasanJadwal->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Tambah -->
<div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penugasan Jadwal Khusus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.penugasan-jadwal.store') }}" method="POST">
                    @csrf

                    {{-- Langkah 1 --}}
                    <div class="form-group">
                        <label><strong>Langkah 1: Tentukan Target Penugasan</strong></label>
                        <select name="jenis_penugasan" id="jenis_penugasan" class="form-control">
                            <option value="individu">Per Individu</option>
                            <option value="per_kelas">Per Kelas (untuk Siswa)</option>
                            <option value="per_peran">Per Peran (Role)</option>
                        </select>
                    </div>

                    {{-- Target Penugasan (Dinamis) --}}
                    <div id="form_individu" class="target-form">
                        <div class="form-group">
                            <label for="user_id">Pilih Pengguna</label>
                            <select id="user_id" name="user_id" class="form-control select2">
                                <option value="">-- Pilih Pengguna --</option>
                                @foreach ($users as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }} ({{ $data->roles }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="form_per_kelas" class="target-form" style="display: none;">
                        <div class="form-group">
                            <label for="kelas_id">Pilih Kelas</label>
                            <select id="kelas_id" name="kelas_id" class="form-control select2">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $data)
                                    <option value="{{ $data->id }}">{{ $data->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="form_per_peran" class="target-form" style="display: none;">
                        <div class="form-group">
                            <label for="roles">Pilih Peran (Role)</label>
                            <select id="roles" name="roles" class="form-control select2">
                                <option value="">-- Pilih Peran --</option>
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                                <option value="karyawan">Karyawan</option>
                                <option value="orangtua">Orang Tua</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    {{-- Langkah 2 --}}
                    <div class="form-group">
                        <label><strong>Langkah 2: Tentukan Waktu Berlakunya</strong></label>
                        <select name="jenis_waktu" id="jenis_waktu" class="form-control">
                            <option value="tanggal">Berdasarkan Rentang Tanggal</option>
                            <option value="hari">Berdasarkan Hari Tertentu (Berulang)</option>
                        </select>
                    </div>

                    {{-- Waktu Dinamis --}}
                    <div id="form_tanggal" class="waktu-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai</label>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai">Tanggal Selesai (Opsional)</label>
                                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control">
                                    <small class="form-text text-muted">Kosongkan jika berlaku selamanya.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="form_hari" class="waktu-form" style="display: none;">
                        <div class="form-group">
                            <label for="hari">Pilih Hari</label>
                            <select id="hari" name="hari" class="form-control select2">
                                <option value="">-- Pilih Hari --</option>
                                <option value="senin">Senin</option>
                                <option value="selasa">Selasa</option>
                                <option value="rabu">Rabu</option>
                                <option value="kamis">Kamis</option>
                                <option value="jumat">Jumat</option>
                                <option value="sabtu">Sabtu</option>
                                <option value="minggu">Minggu</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    {{-- Langkah 3 --}}
                    <div class="form-group">
                        <label><strong>Langkah 3: Pilih Jadwal yang Akan Diterapkan</strong></label>
                        <select id="jadwal_kerja_id" name="jadwal_kerja_id" class="form-control select2" required>
                            <option value="">-- Pilih Jadwal Kerja --</option>
                            @foreach ($jadwalKerja as $data)
                                <option value="{{ $data->id }}">{{ $data->nama_jadwal }} ({{ $data->tipe_pengguna }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer br">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Penugasan</button>
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
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });

    $(document).ready(function() {
        // Form target penugasan dinamis
        $('#jenis_penugasan').on('change', function() {
            var selected = $(this).val();
            $('.target-form').hide();
            $('#form_' + selected).show();
        }).trigger('change');

        // Form waktu dinamis
        $('#jenis_waktu').on('change', function() {
            var selected = $(this).val();
            $('.waktu-form').hide();
            $('#form_' + selected).show();
        }).trigger('change');
    });
</script>
@endpush
