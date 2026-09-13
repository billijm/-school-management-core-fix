<?php
namespace App\Http\Controllers;
use App\Models\JadwalKerja;
use App\Models\Kelas;
use App\Models\PenugasanJadwal;
use App\Models\User;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class PenugasanJadwalController extends Controller
{
/**
* Menampilkan daftar penugasan jadwal khusus.
*/
public function index()
{
$penugasanJadwal = PenugasanJadwal::with('user', 'jadwalKerja', 'kelas')->orderBy('id', 'desc')->paginate(15);
$jadwalKerja = JadwalKerja::where('is_umum', false)->orderBy('nama_jadwal', 'asc')->get();
$users = User::orderBy('name', 'asc')->get();
$kelas = Kelas::orderBy('nama_kelas', 'asc')->get();
return view('pages.admin.penugasan-jadwal.index',
compact('penugasanJadwal', 'jadwalKerja', 'users', 'kelas'));
}
/**
* Menyimpan satu aturan penugasan jadwal khusus.
*/
public function store(Request $request)
{

// Konversi input kosong dari form yang disembunyikan menjadi NULLsebelum validasi.
if ($request->jenis_penugasan !== 'individu') $request->merge(['user_id' =>
null]);
if ($request->jenis_penugasan !== 'per_kelas') $request->merge(['kelas_id'
=> null]);
if ($request->jenis_penugasan !== 'per_peran') $request->merge(['roles' =>
null]);
if ($request->jenis_waktu !== 'tanggal') $request->merge(['tanggal_mulai'
=> null, 'tanggal_selesai' => null]);
if ($request->jenis_waktu !== 'hari') $request->merge(['hari' => null]);

// Validasi kondisional yang benar
$validator = Validator::make($request->all(), [
'jenis_penugasan' => 'required|in:individu,per_kelas,per_peran',
'jenis_waktu' => 'required|in:tanggal,hari',
'jadwal_kerja_id' => 'required|exists:jadwal_kerja,id',
'user_id' =>
'nullable|required_if:jenis_penugasan,individu|exists:users,id',
'kelas_id' =>
'nullable|required_if:jenis_penugasan,per_kelas|exists:kelas,id',
'roles' =>
'nullable|required_if:jenis_penugasan,per_peran|in:admin,guru,siswa,orangtua,ka
ryawan',
'tanggal_mulai' => 'nullable|required_if:jenis_waktu,tanggal|date',
'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
'hari' =>
'nullable|required_if:jenis_waktu,hari|in:senin,selasa,rabu,kamis,jumat,sabtu,min
ggu',
]);
if ($validator->fails()) {
return redirect()->back()->withErrors($validator)->withInput();
}
// Simpan satu baris aturan
PenugasanJadwal::create([
'jadwal_kerja_id' => $request->jadwal_kerja_id,
'user_id' => $request->user_id,
'kelas_id' => $request->kelas_id,
'roles' => $request->roles,
'hari' => $request->hari,
'tanggal_mulai' => $request->tanggal_mulai,
'tanggal_selesai' => $request->tanggal_selesai,
]);
return redirect()->route('admin.penugasan-jadwal.index')->with('success',
'Aturan penugasan jadwal khusus berhasil ditambahkan.');
}

/**
* Hapus penugasan jadwal khusus.
*/
public function destroy($id)
{
$penugasan = PenugasanJadwal::findOrFail($id);
$penugasan->delete();
return redirect()->route('admin.penugasan-jadwal.index')->with('success',
'Aturan penugasan jadwal berhasil dihapus.');
}
}