<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;

class QrScanController extends Controller
{
    /**
     * Tampilkan halaman scanner QR Presensi
     */
    public function index()
    {
        return view('pages.qrscan.index');
    }

    /**
     * Proses hasil scan QR Code dari kamera
     */
    public function store(Request $request)
    {
        $kode = $request->input('kode');

        if (!$kode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode QR tidak ditemukan dalam permintaan.',
            ], 400);
        }

        // Cari user berdasarkan kode unik QR
        $user = User::where('qr_code', $kode)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid atau pengguna tidak ditemukan.',
            ], 404);
        }

        $tanggalHariIni = Carbon::now()->toDateString();
        $waktuSekarang = Carbon::now();

        // Cek apakah user sudah presensi hari ini
        $presensi = Presensi::where('user_id', $user->id)
            ->where('tanggal', $tanggalHariIni)
            ->first();

        if (!$presensi) {
            // Belum presensi → absen masuk
            Presensi::create([
                'user_id'       => $user->id,
                'tanggal'       => $tanggalHariIni,
                'waktu_masuk'   => $waktuSekarang,
                'status_masuk'  => $waktuSekarang->format('H:i:s') <= '07:00:00'
                    ? 'Tepat Waktu'
                    : 'Terlambat',
                'keterangan'    => 'Absen masuk melalui scan QR',
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => "{$user->name} berhasil absen masuk!",
            ]);
        }

        // Jika sudah absen masuk → cek apakah sudah absen pulang
        if ($presensi->waktu_pulang) {
            return response()->json([
                'status'  => 'error',
                'message' => "{$user->name} sudah melakukan absen pulang hari ini.",
            ], 400);
        }

        // Jika belum absen pulang → update data presensi
        $presensi->update([
            'waktu_pulang'  => $waktuSekarang,
            'status_pulang' => $waktuSekarang->format('H:i:s') >= '16:00:00'
                ? 'Sesuai Jadwal'
                : 'Pulang Cepat',
            'keterangan'    => 'Absen pulang melalui scan QR',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "{$user->name} berhasil absen pulang!",
        ]);
    }
}
