<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanLokasi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     * Secara default, Laravel mencari 'pengaturan_lokasis'.
     *
     * @var string
     */
    protected $table = 'pengaturan_lokasis'; 

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius_meter',
        'keterangan',
        'is_aktif',
    ];

    /**
     * Jika Anda tidak menggunakan kolom timestamps (created_at dan updated_at)
     * atur ini menjadi false.
     *
     * @var bool
     */
    // public $timestamps = false;
}