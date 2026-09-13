<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiPresensi extends Model
{
    use HasFactory;

    protected $table = 'lokasi_presensi';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius',
    ];
}
