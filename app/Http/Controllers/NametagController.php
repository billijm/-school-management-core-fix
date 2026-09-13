<?php 

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class NametagController extends Controller
{
    /**
     * Cetak semua pengguna (untuk tombol "Cetak Nametag")
     */
    public function cetakNametagPengguna()
    {
        $users = User::all();
        $pengaturan = Pengaturan::first();

        return view('pages.admin.nametag.cetak', compact('users', 'pengaturan'));
    }

    /**
     * Cetak hanya pengguna terpilih (fitur admin)
     */
    public function cetakNametagTerpilih(Request $request)
    {
        $ids = $request->input('user_ids', []);
        $users = User::whereIn('id', $ids)->get();
        $pengaturan = Pengaturan::first();

        return view('pages.admin.nametag.cetak', compact('users', 'pengaturan'));
    }
}
