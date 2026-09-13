<?php

namespace App\Http\Controllers;

use App\Models\LokasiPresensi;
use Illuminate\Http\Request;

class LokasiPresensiController extends Controller
{
    // Tampilkan semua lokasi presensi
    public function index()
    {
        $lokasi = LokasiPresensi::orderBy('id', 'desc')->paginate(10);
        return view('admin.lokasi_presensi.index', compact('lokasi'));
    }

    // Form tambah lokasi
    public function create()
    {
        return view('admin.lokasi_presensi.create');
    }

    // Simpan data lokasi baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ]);

        LokasiPresensi::create([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
        ]);

        return redirect()->route('admin.lokasi-presensi.index')->with('success', 'Lokasi presensi berhasil ditambahkan!');
    }

    // Form edit lokasi
    public function edit($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        return view('admin.lokasi_presensi.edit', compact('lokasi'));
    }

    // Update data lokasi
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:10',
        ]);

        $lokasi = LokasiPresensi::findOrFail($id);
        $lokasi->update([
            'nama_lokasi' => $request->nama_lokasi,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
        ]);

        return redirect()->route('admin.lokasi-presensi.index')->with('success', 'Lokasi presensi berhasil diperbarui!');
    }

    // Hapus lokasi
    public function destroy($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('admin.lokasi-presensi.index')->with('success', 'Lokasi presensi berhasil dihapus!');
    }
}
