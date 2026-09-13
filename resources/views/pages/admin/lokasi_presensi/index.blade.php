@extends('layouts.main')
@section('title', 'Manajemen Lokasi Presensi')

@section('content')
<section class="section custom-section">
  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">

          <div class="card-header d-flex justify-content-between">
            <h4>Manajemen Lokasi Presensi</h4>
            <button class="btn btn-primary" data-toggle="modal" data-target="#tambahLokasiModal">
              <i class="nav-icon fas fa-map-marker-alt"></i>&nbsp; Tambah Lokasi Presensi
            </button>
          </div>

          <div class="card-body">
            @include('partials.alert')
            <div class="table-responsive">
              <table class="table table-striped" id="table-2">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Lokasi</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Radius (meter)</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($lokasi as $data)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->nama_lokasi }}</td>
                    <td>{{ $data->latitude }}</td>
                    <td>{{ $data->longitude }}</td>
                    <td>{{ $data->radius }}</td>
                    <td>
                      <div class="d-flex">
                        <!-- Route edit sudah sesuai controller -->
                        <a href="{{ route('admin.pengaturan-lokasi.edit', $data->id) }}" 
                           class="btn btn-success btn-sm mr-2">
                          <i class="nav-icon fas fa-edit"></i> &nbsp; Edit
                        </a>
                        <form method="POST" action="{{ route('admin.pengaturan-lokasi.destroy', $data->id) }}">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm show_confirm" data-toggle="tooltip" title="Hapus">
                            <i class="nav-icon fas fa-trash-alt"></i> &nbsp; Hapus
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data lokasi presensi.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- Modal Tambah Lokasi -->
<div class="modal fade" tabindex="-1" role="dialog" id="tambahLokasiModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Lokasi Presensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.pengaturan-lokasi.store') }}" method="POST">
          @csrf
          <div class="form-group">
            <label for="nama_lokasi">Nama Lokasi</label>
            <input type="text" name="nama_lokasi" id="nama_lokasi"
              class="form-control @error('nama_lokasi') is-invalid @enderror"
              placeholder="Contoh: SMK Negeri 1 Bandung" required>
            @error('nama_lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="latitude">Latitude</label>
            <input type="text" name="latitude" id="latitude"
              class="form-control @error('latitude') is-invalid @enderror"
              placeholder="-6.912345" required>
            @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="longitude">Longitude</label>
            <input type="text" name="longitude" id="longitude"
              class="form-control @error('longitude') is-invalid @enderror"
              placeholder="107.654321" required>
            @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="radius">Radius (meter)</label>
            <input type="number" name="radius" id="radius"
              class="form-control @error('radius') is-invalid @enderror"
              placeholder="Misal: 50" required>
            @error('radius') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
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
