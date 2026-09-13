@extends('layouts.main')
@section('title', 'Pengaturan Lokasi Presensi')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Pengaturan Lokasi Presensi</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.pengaturan-lokasi.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Lokasi
            </a>
        </div>
    </div>

    <div class="section-body">
        @include('partials.alert')
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lokasi</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Radius (m)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lokasi as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_lokasi }}</td>
                            <td>{{ $item->latitude }}</td>
                            <td>{{ $item->longitude }}</td>
                            <td>{{ $item->radius }}</td>
                            <td>
                                <a href="{{ route('admin.pengaturan-lokasi.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.pengaturan-lokasi.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus lokasi ini?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada lokasi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $lokasi->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
