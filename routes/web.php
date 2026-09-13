<?php

use App\Http\Controllers\GuruController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengumumanSekolahController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TugasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NametagController;
use App\Http\Controllers\JadwalKerjaController;
use App\Http\Controllers\PenugasanJadwalController;
use App\Http\Controllers\PengaturanLokasiController;
use App\Http\Controllers\LaporanPresensiController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('auth');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

/**
 * ✅ Rute Publik (Tanpa Login)
 */
Route::get('/halaman-presensi', [PresensiController::class, 'showPresensiPage'])->name('presensi.scan');
Route::post('/presensi/store', [PresensiController::class, 'store'])->name('presensi.store');
Route::post('/presensi/cek-lokasi', [PresensiController::class, 'cekLokasi'])->name('presensi.cek-lokasi');

// --- Rute untuk Halaman Scan QR ---

// Rute ini akan menampilkan halaman presensi/scanner QR
Route::get('/qr-scan', [App\Http\Controllers\PresensiController::class, 'showPresensiPage'])->name('qrscan.index');

// ... (Rute-rute lainnya) ...
// 
Route::post('/presensi/store', [App\Http\Controllers\PresensiController::class, 'store'])->name('presensi.store');
/**
 * ✅ Routes untuk semua pengguna login
 */
Route::group(['middleware' => 'auth'], function () {
    Route::get('/profile', [UserController::class, 'edit'])->name('profile');
    Route::put('/update-profile', [UserController::class, 'update'])->name('update.profile');
    Route::get('/edit-password', [UserController::class, 'editPassword'])->name('ubah-password');
    Route::patch('/update-password', [UserController::class, 'updatePassword'])->name('update-password');
    Route::get('/cetak-nametag', [NametagController::class, 'cetakNametagPengguna'])->name('nametag.cetak.pengguna');

    // ✅ Laporan presensi pribadi (semua role)
    Route::get('/laporan-presensi', [LaporanPresensiController::class, 'indexSelf'])->name('laporan-presensi.index');
    Route::get('/laporan-presensi/export', [LaporanPresensiController::class, 'exportSelf'])->name('laporan-presensi.export');
});

/**
 * ✅ Routes untuk Admin
 */
Route::group([
    'middleware' => ['auth', 'checkRole:admin'],
    'prefix' => 'admin',
    'as' => 'admin.',
], function () {
    Route::get('/dashboard', [HomeController::class, 'admin'])->name('dashboard');

    Route::resources([
        'jurusan' => JurusanController::class,
        'mapel' => MapelController::class,
        'guru' => GuruController::class,
        'kelas' => KelasController::class,
        'siswa' => SiswaController::class,
        'user' => UserController::class,
        'jadwal' => JadwalController::class,
        'pengumuman-sekolah' => PengumumanSekolahController::class,
        'pengaturan' => PengaturanController::class,
        'jadwal-kerja' => JadwalKerjaController::class,
        'penugasan-jadwal' => PenugasanJadwalController::class,
        'pengaturan-lokasi' => PengaturanLokasiController::class,
    ]);

    Route::post('/nametag/cetak-terpilih', [NametagController::class, 'cetakNametagTerpilih'])->name('nametag.cetak.terpilih');
    // ✅ Tambahkan di grup admin
Route::get('/halaman-presensi', [PresensiController::class, 'showPresensiPage'])->name('presensi.scan');
Route::resource('penugasan-jadwal', PenugasanJadwalController::class)->middleware(['auth', 'checkRole:admin']);



    // ✅ Laporan presensi admin
    Route::get('/laporan-presensi', [LaporanPresensiController::class, 'indexAdmin'])->name('laporan-presensi.index-admin');
    Route::get('/laporan-presensi/export', [LaporanPresensiController::class, 'exportAdmin'])->name('laporan-presensi.export');
});

/**
 * ✅ Routes untuk Guru
 */
Route::group([
    'middleware' => ['auth', 'checkRole:guru'],
    'prefix' => 'guru',
    'as' => 'guru.',
], function () {
    Route::get('/dashboard', [HomeController::class, 'guru'])->name('dashboard');

    Route::resources([
        'materi' => MateriController::class,
        'tugas' => TugasController::class,
    ]);

    Route::get('/jawaban-download/{id}', [TugasController::class, 'downloadJawaban'])->name('jawaban.download');

    // ✅ Laporan presensi guru
    Route::get('/laporan-presensi', [LaporanPresensiController::class, 'indexSelf'])->name('laporan-presensi');
});

/**
 * ✅ Routes untuk Siswa
 */
Route::group([
    'middleware' => ['auth', 'checkRole:siswa'],
    'prefix' => 'siswa',
    'as' => 'siswa.',
], function () {
    Route::get('/dashboard', [HomeController::class, 'siswa'])->name('dashboard');

    Route::get('/materi', [MateriController::class, 'index'])->name('materi');
    Route::get('/materi-download/{id}', [MateriController::class, 'download'])->name('materi.download');

    // ✅ Route tugas siswa
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas');
    Route::get('/tugas-download/{id}', [TugasController::class, 'download'])->name('tugas.download');
    Route::post('/kirim-jawaban', [TugasController::class, 'kirimJawaban'])->name('kirim-jawaban');

    // ✅ Laporan presensi siswa
    Route::get('/laporan-presensi', [LaporanPresensiController::class, 'indexSelf'])->name('laporan-presensi');
});

/**
 * ✅ Routes untuk Orangtua
 */
Route::group([
    'middleware' => ['auth', 'checkRole:orangtua'],
    'prefix' => 'orangtua',
    'as' => 'orangtua.',
], function () {
    Route::get('/dashboard', [HomeController::class, 'orangtua'])->name('dashboard');
    Route::get('/tugas/siswa', [TugasController::class, 'orangtua'])->name('tugas.siswa');
});