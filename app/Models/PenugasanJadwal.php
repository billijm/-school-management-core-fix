<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PenugasanJadwal extends Model
{
use HasFactory;
/**
* Nama tabel yang terhubung dengan model.
*
* @var string
*/
protected $table = 'penugasan_jadwal';
/**
* Atribut yang dapat diisi secara massal.
*
* @var array<int, string>
*/
protected $fillable = [
'user_id',
'kelas_id',
'roles',
'jadwal_kerja_id',
'hari',
'tanggal_mulai',
'tanggal_selesai',
];
/**
* Mendefinisikan relasi ke model User.
*/
public function user()
{
return $this->belongsTo(User::class, 'user_id');
}
/**
* Mendefinisikan relasi ke model JadwalKerja.
*/
public function jadwalKerja()
{

return $this->belongsTo(JadwalKerja::class, 'jadwal_kerja_id');
}
/**
* Mendefinisikan relasi ke model Kelas.
* INI ADALAH FUNGSI YANG MEMPERBAIKI ERROR.
*/
public function kelas()
{
return $this->belongsTo(Kelas::class, 'kelas_id');
}
}