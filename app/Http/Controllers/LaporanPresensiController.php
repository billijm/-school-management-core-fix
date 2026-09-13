<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;

class LaporanPresensiController extends Controller
{
    /**
     * 📊 Untuk ADMIN melihat semua laporan presensi
     */
    public function indexAdmin()
    {
        $presensi = Presensi::with('user')->latest()->paginate(20);
        return view('pages.admin.laporan-presensi.index', compact('presensi'));
    }

    /**
     * 📊 Untuk GURU atau SISWA melihat laporan presensi dirinya sendiri
     */
    public function indexSelf()
    {
        $user = Auth::user();
        $presensi = Presensi::where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('pages.laporan-presensi.index', compact('presensi'));
    }

    /**
     * 📤 Export laporan pribadi ke Excel (untuk Guru/Siswa)
     */
    public function exportSelf()
    {
        $user = Auth::user();
        return Excel::download(new PresensiExport($user->id), 'laporan_presensi_' . $user->name . '.xlsx');
    }

    /**
     * 📤 Export semua laporan (Admin)
     */
    public function exportAdmin()
    {
        return Excel::download(new PresensiExport(), 'laporan_presensi_semua.xlsx');
    }
}