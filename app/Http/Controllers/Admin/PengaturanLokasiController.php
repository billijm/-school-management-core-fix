<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LokasiPresensi;

class PengaturanLokasiController extends Controller
{
   public function lokasiPresensi()
{
    $lokasi = LokasiPresensi::all();
    return view('pages.admin.pengaturan-lokasi.index', compact('lokasi'));
}


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

    public function edit($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        return view('pages.admin.lokasi_presensi.edit', compact('lokasi'));
    }

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

    public function destroy($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('admin.pengaturan-lokasi.index')
                         ->with('success', 'Lokasi presensi berhasil dihapus.');
    }
}
