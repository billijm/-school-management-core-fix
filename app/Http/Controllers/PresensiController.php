<?php

namespace App\Http\Controllers;

use App\Models\JadwalKerja;
use App\Models\PenugasanJadwal;
use App\Models\PengaturanJamKhusus;
use App\Models\PengaturanLokasi;
use App\Models\Presensi;
use App\Models\User;
use App\Models\Siswa; // Pastikan ini di-import
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
     * Metode baru untuk mengecek lokasi pengguna sebelum menampilkan tombol aksi.
     */
    public function cekLokasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['in_range' => false, 'message' => 'Data lokasi tidak valid.'], 422);
        }

        // Asumsikan lokasi utama sekolah memiliki ID = 1.
        $lokasiUtama = PengaturanLokasi::find(1);

        if (!$lokasiUtama) {
            // Jika tidak ada lokasi yang diatur, izinkan presensi dari mana saja.
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
        } else {
            return response()->json([
                'in_range' => false,
                'message' => 'Anda berada di luar jangkauan lokasi presensi. Jarak Anda sekitar ' . round($jarak) . ' meter dari lokasi yang ditentukan.'
            ], 403); // 403 Forbidden
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'tipe' => 'required|in:masuk,pulang',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $userId = $request->user_id;
        $tipe = $request->tipe;
        $today = Carbon::today();
        $namaHariIni = Str::lower($today->translatedFormat('l'));
        $now = Carbon::now();
        // Memastikan relasi 'siswa' di Model User sudah benar
        $user = User::with('siswa')->findOrFail($userId);
        $jadwalDitemukan = null;

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

        // 1. Cari berdasarkan Penugasan spesifik User
        $penugasan = PenugasanJadwal::where('user_id', $user->id)->where($timeClause)->first();

        // 2. Cari berdasarkan Kelas jika Siswa
        if (!$penugasan && $user->roles == 'siswa' && $user->siswa) {
            $penugasan = PenugasanJadwal::where('kelas_id', $user->siswa->kelas_id)->where($timeClause)->first();
        }

        // 3. Cari berdasarkan Role umum
        if (!$penugasan) {
            $penugasan = PenugasanJadwal::where('roles', $user->roles)->where($timeClause)->first();
        }

        if ($penugasan) {
            // Pastikan Model PenugasanJadwal memiliki relasi 'jadwalKerja'
            $jadwalDitemukan = $penugasan->jadwalKerja; 
        }

        // 4. Cari Jadwal Umum (fallback)
        if (!$jadwalDitemukan) {
            $jadwalDitemukan = JadwalKerja::where('tipe_pengguna', $user->roles)->where('is_umum', true)->first();
        }

        if (!$jadwalDitemukan) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki jadwal kerja yang aktif hari ini.'], 400);
        }

        // --- Pengecekan Lokasi (Geofencing) ---
        if ($jadwalDitemukan->pengaturan_lokasi_id) {
            // Memastikan relasi 'lokasi' di Model JadwalKerja sudah ada (Sudah diperbaiki di JadwalKerja.php)
            $lokasiAbsen = $jadwalDitemukan->lokasi; 
            if (!$lokasiAbsen) {
                // Lokasi tidak ditemukan (Data error)
                return response()->json(['success' => false, 'message' => 'Pengaturan lokasi untuk jadwal ini tidak ditemukan atau ID lokasi tidak valid.'], 400);
            }

            $jarak = $this->hitungJarak($request->latitude, $request->longitude, $lokasiAbsen->latitude, $lokasiAbsen->longitude);

            if ($jarak > $lokasiAbsen->radius_meter) {
                return response()->json(['success' => false, 'message' => 'Anda berada di luar jangkauan lokasi presensi. Jarak Anda: ' . round($jarak) . ' meter dari lokasi.'], 400);
            }
        }
        // --- Akhir Pengecekan Lokasi ---

        // Query presensi hari ini
        $presensiHariIni = Presensi::where('user_id', $userId)
            ->whereDate('tanggal', $today->format('Y-m-d'))
            ->first();

        // --- Logika Presensi Masuk ---
        if ($tipe == 'masuk') {
            if ($presensiHariIni && $presensiHariIni->waktu_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi masuk hari ini.'], 400);
            }

            $statusMasuk = $now->gt(Carbon::parse($jadwalDitemukan->jam_masuk)->addMinutes($jadwalDitemukan->toleransi_terlambat_menit))
                ? 'Terlambat'
                : 'Tepat Waktu';
            
            if ($presensiHariIni) {
                $presensiHariIni->update([
                    'waktu_masuk' => $now,
                    'status_masuk' => $statusMasuk
                ]);
            } else {
                Presensi::create([
                    'user_id' => $userId,
                    'tanggal' => $today,
                    'waktu_masuk' => $now,
                    'status_masuk' => $statusMasuk
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Presensi Masuk berhasil dicatat. Selamat beraktivitas!']);
        }

        // --- Logika Presensi Pulang ---
        if ($tipe == 'pulang') {
            if (!$presensiHariIni || !$presensiHariIni->waktu_masuk) {
                return response()->json(['success' => false, 'message' => 'Anda harus melakukan presensi masuk terlebih dahulu sebelum presensi pulang.'], 400);
            }

            $presensiHariIni->refresh();
            if ($presensiHariIni->waktu_pulang) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan presensi pulang hari ini.'], 400);
            }

            $jamPulangReferensi = $jadwalDitemukan->jam_pulang;
            // Keterangan presensi dapat ditambahkan di sini jika perlu

            $statusPulang = $now->lt(Carbon::parse($jamPulangReferensi))
                ? 'Pulang Cepat'
                : 'Sesuai Jadwal';

            // Update presensi pulang
            $updateResult = $presensiHariIni->update([
                'waktu_pulang' => $now,
                'status_pulang' => $statusPulang
            ]);

            if ($updateResult) {
                return response()->json(['success' => true, 'message' => 'Presensi Pulang berhasil dicatat. Selamat beristirahat!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan presensi pulang. Silakan coba lagi.'], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Tipe presensi tidak valid.'], 400);
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}