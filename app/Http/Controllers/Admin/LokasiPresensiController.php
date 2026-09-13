<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LokasiPresensi;

class LokasiPresensiController extends Controller
{
    // Menampilkan daftar lokasi presensi
    public function index()
    {
        $lokasi = LokasiPresensi::orderBy('id', 'desc')->get();
        return view('pages.admin.lokasi_presensi.index', compact('lokasi'));
    }

    // Menyimpan lokasi presensi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:0',
        ]);

        LokasiPresensi::create($validated);

        return redirect()->route('admin.pengaturan-lokasi.index')
                         ->with('success', 'Lokasi presensi berhasil ditambahkan.');
    }

    // Menampilkan form edit lokasi presensi
    public function edit($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        return view('pages.admin.lokasi_presensi.edit', compact('lokasi'));
    }

    // Mengupdate lokasi presensi
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:0',
        ]);

        $lokasi = LokasiPresensi::findOrFail($id);
        $lokasi->update($validated);

        return redirect()->route('admin.pengaturan-lokasi.index')
                         ->with('success', 'Lokasi presensi berhasil diperbarui.');
    }

    // Menghapus lokasi presensi
    public function destroy($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('admin.pengaturan-lokasi.index')
                         ->with('success', 'Lokasi presensi berhasil dihapus.');
    }
}
