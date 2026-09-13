@extends('layouts.main')

@section('title', 'Laporan Presensi')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Laporan Presensi</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Data Presensi Saya</h4>
                <div class="card-header-action">
                    <a href="{{ route('laporan-presensi.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="card-body table-responsive">
                @if ($presensi->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Pulang</th>
                                <th>Status Masuk</th>
                                <th>Status Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presensi as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i:s') : '-' }}</td>
                                    <td>{{ $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i:s') : '-' }}</td>
                                    <td>{{ $item->status_masuk ?? '-' }}</td>
                                    <td>{{ $item->status_pulang ?? '-' }}</td>
                                    <td>
                                        @php
                                            // Status: "Aktif" jika ada waktu_masuk, "Tidak Aktif" jika ada waktu_pulang
                                            if ($item->waktu_masuk && !$item->waktu_pulang) {
                                                $status = 'Aktif';
                                                $badgeClass = 'success';
                                            } elseif ($item->waktu_pulang) {
                                                $status = 'Tidak Aktif';
                                                $badgeClass = 'secondary';
                                            } else {
                                                $status = '-';
                                                $badgeClass = 'secondary';
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $presensi->links() }}
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        Belum ada data presensi yang tercatat.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
@extends('layouts.main')

@section('title', 'Laporan Presensi')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Laporan Presensi</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header">
                <h4>Data Presensi Saya</h4>
                <div class="card-header-action">
                    <a href="{{ route('laporan-presensi.export') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="card-body table-responsive">
                @if ($presensi->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Pulang</th>
                                <th>Status Masuk</th>
                                <th>Status Pulang</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($presensi as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $item->waktu_masuk ? \Carbon\Carbon::parse($item->waktu_masuk)->format('H:i:s') : '-' }}</td>
                                    <td>{{ $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i:s') : '-' }}</td>
                                    <td>{{ $item->status_masuk ?? '-' }}</td>
                                    <td>{{ $item->status_pulang ?? '-' }}</td>
                                    <td>
                                        @php
                                            // Status: "Aktif" jika ada waktu_masuk, "Tidak Aktif" jika ada waktu_pulang
                                            if ($item->waktu_masuk && !$item->waktu_pulang) {
                                                $status = 'Aktif';
                                                $badgeClass = 'success';
                                            } elseif ($item->waktu_pulang) {
                                                $status = 'Tidak Aktif';
                                                $badgeClass = 'secondary';
                                            } else {
                                                $status = '-';
                                                $badgeClass = 'secondary';
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $presensi->links() }}
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        Belum ada data presensi yang tercatat.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection