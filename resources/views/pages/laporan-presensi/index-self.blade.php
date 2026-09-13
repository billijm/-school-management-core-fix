@extends('layouts.main')
@section('title', 'Laporan Presensi Saya')

@push('style')
<style>
/* Sembunyikan elemen yang tidak perlu saat cetak */
@media print {
  .main-sidebar,
  .navbar,
  .section-header,
  .card-header .btn,
  .card-footer,
  #filter-form,
  .dt-buttons,
  .dataTables_filter,
  .dataTables_length,
  .dataTables_info,
  .dataTables_paginate {
    display: none !important;
  }

  .main-content {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  .card {
    box-shadow: none !important;
    border: none !important;
  }

  body {
    background-color: white !important;
  }
}
</style>
@endpush

@section('content')
<section class="section custom-section">
  <div class="section-header">
    <h1>Laporan Presensi Saya</h1>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex justify-content-between">
            <h4>Filter Laporan</h4>
          </div>

          <div class="card-body">
            {{-- Form Filter --}}
            <form id="filter-form" action="{{ route('laporan-presensi.index') }}" method="GET">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="tanggal_mulai">Tanggal Mulai</label>
                    <input
                      type="date"
                      id="tanggal_mulai"
                      name="tanggal_mulai"
                      class="form-control"
                      value="{{ $request->tanggal_mulai }}"
                    >
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label for="tanggal_akhir">Tanggal Akhir</label>
                    <input
                      type="date"
                      id="tanggal_akhir"
                      name="tanggal_akhir"
                      class="form-control"
                      value="{{ $request->tanggal_akhir }}"
                    >
                  </div>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                  <div class="form-group w-100">
                    <button type="submit" class="btn btn-primary w-100">
                      <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                  </div>
                </div>
              </div>
            </form>

            <hr>

            {{-- Tombol Aksi --}}
            <div class="mb-3">
              <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print"></i> Cetak
              </button>

              <a href="{{ route('laporan-presensi.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
              </a>
            </div>

            @include('partials.alert')

            <div class="table-responsive">
              <table class="table table-striped" id="table-laporan">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Status Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status Pulang</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>

                <tbody>
                  @forelse ($presensi as $data)
                    <tr>
                      <td>{{ $loop->iteration + $presensi->firstItem() - 1 }}</td>
                      <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y') }}</td>

                      <td>
                        {{ $data->waktu_masuk ? \Carbon\Carbon::parse($data->waktu_masuk)->format('H:i:s') : '-' }}
                      </td>

                      <td>
                        @if($data->status_masuk == 'Tepat Waktu')
                          <span class="badge badge-success">Tepat Waktu</span>
                        @elseif($data->status_masuk == 'Terlambat')
                          <span class="badge badge-danger">Terlambat</span>
                        @else
                          -
                        @endif
                      </td>

                      <td>
                        {{ $data->waktu_pulang ? \Carbon\Carbon::parse($data->waktu_pulang)->format('H:i:s') : '-' }}
                      </td>

                      <td>
                        @if($data->status_pulang == 'Sesuai Jadwal')
                          <span class="badge badge-success">Sesuai Jadwal</span>
                        @elseif($data->status_pulang == 'Pulang Cepat')
                          <span class="badge badge-warning">Pulang Cepat</span>
                        @else
                          -
                        @endif
                      </td>

                      <td>{{ $data->keterangan ?? '-' }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center">Tidak ada data presensi ditemukan.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <div class="card-footer text-right">
              {{ $presensi->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('script')
{{-- Tidak perlu script khusus untuk tabel ini --}}
@endpush
