<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Wajib: Import Model Pengaturan Lokasi yang menjadi tujuan relasi
use App\Models\PengaturanLokasi; 

/**
* Model untuk mengatur master jadwal kerja (shift).
* Pusat pengaturan jam masuk, jam pulang, dan toleransi.
*/
class JadwalKerja extends Model
{
    use HasFactory;
    
    protected $table = 'jadwal_kerja'; 

    protected $fillable = [
        'kode_jadwal',
        'nama_jadwal',
        'tipe_pengguna',
        'jam_masuk',
        'jam_pulang',
        'toleransi_terlambat_menit',
        'batas_awal_absen_masuk',
        'batas_akhir_absen_pulang',
        'is_umum',
        'pengaturan_lokasi_id', // Pastikan kolom ini ada di tabel Anda
    ];

    protected $casts = [
        'is_umum' => 'boolean',
    ];

    /**
     * Relasi Many-to-One ke Model PengaturanLokasi.
     * Ini yang dibutuhkan oleh PresensiController::store ($jadwalDitemukan->lokasi).
     */
    public function lokasi()
    {
        // Asumsi: foreign key di tabel 'jadwal_kerja' adalah 'pengaturan_lokasi_id'
        return $this->belongsTo(PengaturanLokasi::class, 'pengaturan_lokasi_id');
    }

    /**
     * Satu Jadwal Kerja bisa dimiliki oleh banyak penugasan.
     */
    public function penugasanJadwal()
    {
        return $this->hasMany(PenugasanJadwal::class);
    }
}