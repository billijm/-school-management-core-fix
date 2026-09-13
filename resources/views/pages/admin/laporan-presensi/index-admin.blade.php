<?php

namespace App\Http\Controllers;

use App\Models\JadwalKerja;
use App\Models\PenugasanJadwal;
use App\Models\PengaturanJamKhusus;
use App\Models\PengaturanLokasi;
use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PresensiController extends Controller
{
    public function showPresensiPage()
    {
        return view('pages.presensi.halaman-presensi');
    }

    /**
     * Mengecek lokasi pengguna sebelum menampilkan tombol aksi presensi.
     */
    public function cekLokasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'in_range' => false,
                'message'  => 'Data lokasi tidak valid.'
            ], 422);
        }

        // Lokasi utama (ID = 1)
        $lokasiUtama = PengaturanLokasi::find(1);

        if (!$lokasiUtama) {
            // Jika belum diatur, izinkan presensi dari mana saja
            return response()->json(['in_range' => true]);
        }

        $jarak = $this->hitungJarak(
            $request->latitude,
            $request->longitude,
            $lokasiUtama->latitude,
            $lokasiUtama->longitude
        );

        if ($jarak <= $lokasiUtama->radius_meter) {
            return response()->json(['in_range' => true]);
        }

        return response()->json([
            'in_range' => false,
            'message'  => 'Anda berada di luar jangkauan lokasi presensi. Jarak Anda sekitar ' . round($jarak) . ' meter dari lokasi yang ditentukan.'
        ], 403);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|exists:users,id',
            'tipe'     => 'required|in:masuk,pulang',
            'latitude' => 'required|numeric',
            'longitude'=> 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $userId = $request->user_id;
        $tipe   = $request->tipe;
        $today  = Carbon::today();
        $now    = Carbon::now();
        $namaHariIni = Str::lower($today->translatedFormat('l'));

        $user = User::with('siswa')->findOrFail($userId);
        $jadwalDitemukan = null;

        // Filter waktu jadwal
        $timeClause = function ($query) use ($today, $namaHariIni) {
            $query->where(function ($q) use ($today, $namaHariIni) {
                $q->where('hari', $namaHariIni)
                    ->orWhere(function ($subQ) use ($today) {
                        $subQ->where('tanggal_mulai', '<=', $today)
                             ->where(function ($dateEndQ) use ($today) {
                                 $dateEndQ->where('tanggal_selesai', '>=', $today)
                                          ->orWhereNull('tanggal_selesai');
                             });
                    });
            });
        };

        // Cari jadwal berdasarkan user atau kelas
        $penugasan = PenugasanJadwal::where('user_id', $user->id)
            ->where($timeClause)
            ->first();

        if (!$penugasan && $user->roles == 'siswa' && $user->siswa) {
            $penugasan = PenugasanJadwal::where('kelas_id', $user->siswa->kelas_id)
                ->where($timeClause)
                ->first();
        }

        if (!$penugasan) {
            $penugasan = PenugasanJadwal::where('roles', $user->roles)
                ->where($timeClause)
                ->first();
        }

        if ($penugasan) {
            $jadwalDitemukan = $penugasan->jadwalKerja;
        }

        // Jika tidak ditemukan
        if (!$jadwalDitemukan) {
            $jadwalDitemukan = JadwalKerja::where('tipe_pengguna', $user->roles)
                ->where('is_umum', true)
                ->first();
        }

        if (!$jadwalDitemukan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki jadwal kerja yang aktif hari ini.'
            ], 400);
        }

        // Validasi lokasi presensi
        if ($jadwalDitemukan->pengaturan_lokasi_id) {
            $lokasiAbsen = $jadwalDitemukan->lokasi;

            if (!$lokasiAbsen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaturan lokasi untuk jadwal ini tidak ditemukan.'
                ], 400);
            }

            $jarak = $this->hitungJarak(
                $request->latitude,
                $request->longitude,
                $lokasiAbsen->latitude,
                $lokasiAbsen->longitude
            );

            if ($jarak > $lokasiAbsen->radius_meter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar jangkauan lokasi presensi. Jarak Anda: ' . round($jarak) . ' meter dari lokasi.'
                ], 400);
            }
        }

        $presensiHariIni = Presensi::where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        /** ========================
         *  PRESENSI MASUK
         *  ======================== */
        if ($tipe === 'masuk') {
            if ($presensiHariIni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi masuk hari ini.'
                ], 400);
            }

            $statusMasuk = $now->gt(
                Carbon::parse($jadwalDitemukan->jam_masuk)
                    ->addMinutes($jadwalDitemukan->toleransi_terlambat_menit)
            ) ? 'Terlambat' : 'Tepat Waktu';

            Presensi::create([
                'user_id'       => $userId,
                'tanggal'       => $today,
                'waktu_masuk'   => $now,
                'status_masuk'  => $statusMasuk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi Masuk berhasil dicatat. Selamat beraktivitas!'
            ]);
        }

        /** ========================
         *  PRESENSI PULANG
         *  ======================== */
        if ($tipe === 'pulang') {
            if (!$presensiHariIni || !$presensiHariIni->waktu_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus melakukan presensi masuk terlebih dahulu sebelum presensi pulang.'
                ], 400);
            }

            if ($presensiHariIni->waktu_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi pulang hari ini.'
                ], 400);
            }

            $jamPulangReferensi = $jadwalDitemukan->jam_pulang;
            $keteranganPresensi = "Jadwal " . $jadwalDitemukan->nama_jadwal;

            // Khusus siswa
            if ($user->roles === 'siswa' && $user->siswa) {
                $aturan = PenugasanJadwal::where('tanggal', $today)
                    ->where('kelas_id', $user->siswa->kelas_id)
                    ->first()
                    ?? PenugasanJadwal::where('tanggal', $today)
                        ->whereNull('kelas_id')->first()
                    ?? PenugasanJadwal::where('hari', $namaHariIni)
                        ->where('kelas_id', $user->siswa->kelas_id)->first()
                    ?? PenugasanJadwal::where('hari', $namaHariIni)
                        ->whereNull('kelas_id')->first();

                if ($aturan) {
                    $jamPulangReferensi = $aturan->jam_pulang;
                    $keteranganPresensi  = $aturan->keterangan;
                }
            }

            $statusPulang = $now->lt(Carbon::parse($jamPulangReferensi))
                ? 'Pulang Cepat'
                : 'Sesuai Jadwal';

            $presensiHariIni->update([
                'waktu_pulang'  => $now,
                'status_pulang' => $statusPulang,
                'keterangan'    => $keteranganPresensi
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi Pulang berhasil dicatat. Selamat beristirahat!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tipe presensi tidak valid.'
        ], 400);
    }

    /**
     * Menghitung jarak antara dua titik koordinat (meter)
     */
    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(
            sqrt(
                pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
            )
        );

        return $angle * $earthRadius;
    }
}
