<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\LokasiPresensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengaturanController extends Controller
{
    // ======================
    // PENGATURAN SEKOLAH
    // ======================
    public function index()
    {
        $pengaturan = Pengaturan::first();
        return view('pages.admin.pengaturan.index', compact('pengaturan'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'nama_sekolah.required' => 'Nama sekolah harus diisi.',
            'nama_sekolah.string' => 'Nama sekolah harus berupa teks.',
            'nama_sekolah.max' => 'Nama sekolah tidak boleh lebih dari 255 karakter.',
            'logo.image' => 'Logo harus berupa gambar.',
            'logo.mimes' => 'Logo harus berformat jpeg, png, jpg, atau gif.',
            'logo.max' => 'Logo tidak boleh lebih dari 2MB.',
        ]);

        $pengaturan = Pengaturan::findOrFail($id);
        $pengaturan->name = $validatedData['nama_sekolah'];

        if ($request->hasFile('logo')) {
            if ($pengaturan->logo) {
                Storage::delete($pengaturan->logo);
            }
            $slug = Str::slug($pengaturan->name);
            $pengaturan->logo = 'storage/logos/' . $slug . '_logo.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->storeAs('logos', $slug . '_logo.' . $request->file('logo')->getClientOriginalExtension(), 'public');
        }

        $pengaturan->save();

        return redirect()->route('admin.pengaturan.index')
                 ->with('success', 'Pengaturan berhasil diperbarui.');

    }

    public function destroy(string $id)
    {
        abort(404);
    }

    // ======================
    // PENGATURAN LOKASI PRESENSI
    // ======================
    public function lokasiPresensi()
    {
        // Ambil data lokasi presensi dengan pagination agar bisa pakai ->links()
        $lokasi = LokasiPresensi::paginate(10);

        return view('pages.admin.pengaturan-lokasi.index', compact('lokasi'));
    }

    public function lokasiPresensiCreate()
    {
        // Tampilkan halaman form tambah lokasi
        return view('pages.admin.pengaturan-lokasi.create');
    }

    public function lokasiPresensiStore(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ]);

        LokasiPresensi::create($validated);

        return redirect()->route('admin.pengaturan-lokasi.index')
            ->with('success', 'Lokasi presensi berhasil ditambahkan.');
    }

    public function lokasiPresensiEdit($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        return view('pages.admin.pengaturan-lokasi.edit', compact('lokasi'));
    }

    public function lokasiPresensiUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ]);

        $lokasi = LokasiPresensi::findOrFail($id);
        $lokasi->update($validated);

        return redirect()->route('admin.pengaturan-lokasi.index')
            ->with('success', 'Lokasi presensi berhasil diperbarui.');
    }

    public function lokasiPresensiDestroy($id)
    {
        $lokasi = LokasiPresensi::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('admin.pengaturan-lokasi.index')
            ->with('success', 'Lokasi presensi berhasil dihapus.');
    }
}
