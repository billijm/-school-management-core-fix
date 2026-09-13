<?php

namespace App\Http\Controllers;

use App\Models\JadwalKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JadwalKerjaController extends Controller
{
    public function index()
    {
        $jadwalKerja = JadwalKerja::orderBy('is_umum', 'desc')
            ->orderBy('nama_jadwal', 'asc')
            ->get();

        return view('pages.admin.jadwal-kerja.index', compact('jadwalKerja'));
    }

    public function store(Request $request)
    {
        // Konversi checkbox ke boolean agar validasi tidak error
        $request->merge(['is_umum' => $request->has('is_umum')]);

        $validator = Validator::make($request->all(), [
            'kode_jadwal' => 'required|string|max:50|unique:jadwal_kerja,kode_jadwal',
            'nama_jadwal' => 'required|string|max:255',
            'tipe_pengguna' => 'required|in:siswa,guru,karyawan,orangtua',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_terlambat_menit' => 'required|integer|min:0',
            'is_umum' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $isUmum = $request->is_umum;

            if ($isUmum) {
                JadwalKerja::where('tipe_pengguna', $request->tipe_pengguna)
                    ->update(['is_umum' => false]);
            }

            JadwalKerja::create([
                'kode_jadwal' => $request->kode_jadwal,
                'nama_jadwal' => $request->nama_jadwal,
                'tipe_pengguna' => $request->tipe_pengguna,
                'is_umum' => $isUmum,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'toleransi_terlambat_menit' => $request->toleransi_terlambat_menit,
            ]);

            DB::commit();
            return redirect()->route('admin.jadwal-kerja.index')
                ->with('success', 'Jadwal kerja berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['db_error' => 'Gagal menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $jadwalKerja = JadwalKerja::findOrFail($id);
        return view('pages.admin.jadwal-kerja.edit', compact('jadwalKerja'));
    }

    public function update(Request $request, $id)
    {
        $jadwalKerja = JadwalKerja::findOrFail($id);

        // Konversi checkbox ke boolean
        $request->merge(['is_umum' => $request->has('is_umum')]);

        $validator = Validator::make($request->all(), [
            'kode_jadwal' => 'required|string|max:50|unique:jadwal_kerja,kode_jadwal,' . $id,
            'nama_jadwal' => 'required|string|max:255',
            'tipe_pengguna' => 'required|in:siswa,guru,karyawan,orangtua',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
            'toleransi_terlambat_menit' => 'required|integer|min:0',
            'is_umum' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $isUmum = $request->is_umum;

            if ($isUmum) {
                JadwalKerja::where('tipe_pengguna', $request->tipe_pengguna)
                    ->where('id', '!=', $id)
                    ->update(['is_umum' => false]);
            }

            $jadwalKerja->update([
                'kode_jadwal' => $request->kode_jadwal,
                'nama_jadwal' => $request->nama_jadwal,
                'tipe_pengguna' => $request->tipe_pengguna,
                'is_umum' => $isUmum,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'toleransi_terlambat_menit' => $request->toleransi_terlambat_menit,
            ]);

            DB::commit();
            return redirect()->route('admin.jadwal-kerja.index')
                ->with('success', 'Jadwal kerja berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['db_error' => 'Gagal memperbarui data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $jadwalKerja = JadwalKerja::findOrFail($id);
        $jadwalKerja->delete();

        return redirect()->route('admin.jadwal-kerja.index')
            ->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}
    