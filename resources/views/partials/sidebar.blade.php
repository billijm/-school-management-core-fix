<div class="main-sidebar">
<aside id="sidebar-wrapper">
<div class="sidebar-brand mt-3">
<a href="{{ url('/') }}">
{{ $pengaturan->name ?? config('app.name', 'Aplikasi') }}
</a>
</div>
<div class="sidebar-brand sidebar-brand-sm">
<a href="#">

{{ strtoupper(substr($pengaturan->name ?? config('app.name', 'AP'), 0,
2)) }}
</a>
</div>

<ul class="sidebar-menu">
{{-- ========== ADMIN ========== --}}
@if (Auth::check() && Auth::user()->roles == 'admin')
<li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.dashboard') }}">
<i class="fas fa-columns"></i> <span>Dashboard</span>
</a>
</li>

<li class="menu-header">Master Data</li>

<li class="{{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.jurusan.index') }}">
<i class="fas fa-book"></i> <span>Jurusan</span>
</a>
</li>

<li class="{{ request()->routeIs('admin.mapel.*') ? 'active' : '' }}">

<a class="nav-link" href="{{ route('admin.mapel.index') }}">
<i class="fas fa-book"></i> <span>Mata Pelajaran</span>
</a>
</li>

<li class="{{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.guru.index') }}">
<i class="fas fa-user"></i> <span>Guru</span>
</a>
</li>

<li class="{{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.kelas.index') }}">
<i class="far fa-building"></i> <span>Kelas</span>
</a>
</li>

<li class="{{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.siswa.index') }}">
<i class="fas fa-users"></i> <span>Siswa</span>
</a>
</li>

<li class="{{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.jadwal.index') }}">
<i class="fas fa-calendar"></i> <span>Jadwal</span>
</a>
</li>

<li class="{{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.user.index') }}">
<i class="fas fa-user"></i> <span>User</span>
</a>
</li>

<li class="menu-header">Manajemen Sekolah</li>
<li class="nav-item dropdown {{ request()->routeIs('admin.jadwal-kerja.*',

'admin.penugasan-jadwal.*', 'admin.pengaturan-jam-khusus.*', 'admin.pengaturan-
lokasi.*') ? 'active' : '' }}">

<a href="#" class="nav-link has-dropdown"><i class="fas fa-
clock"></i><span>Manajemen Presensi</span></a>

<ul class="dropdown-menu">
<li class="{{ request()->routeIs('admin.jadwal-kerja.*') ? 'active' : ''
}}"><a class="nav-link" href="{{ route('admin.jadwal-kerja.index') }}">Jadwal
Kerja</a></li>
<li class="{{ request()->routeIs('admin.penugasan-jadwal.*') ? 'active' :
'' }}"><a class="nav-link" href="{{ route('admin.penugasan-jadwal.index')
}}">Penugasan Jadwal</a></li>

<li class="{{ request()->routeIs('admin.pengaturan-lokasi.*') ? 'active' :
'' }}"><a class="nav-link" href="{{ route('admin.pengaturan-lokasi.index')
}}">Lokasi Presensi</a></li>
</ul>
</li>

<li class="{{ request()->routeIs('admin.pengumuman-sekolah.*') ?
'active' : '' }}">

<a class="nav-link" href="{{ route('admin.pengumuman-sekolah.index') }}">

<i class="fas fa-bullhorn"></i> <span>Pengumuman</span>
</a>
</li>

<li class="{{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('admin.pengaturan.index') }}">
<i class="fas fa-cog"></i> <span>Pengaturan</span>
</a>
</li>

{{-- ========== GURU ========== --}}
@elseif (Auth::check() && Auth::user()->roles == 'guru')
<li class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('guru.dashboard') }}">

<i class="fas fa-columns"></i> <span>Dashboard</span>
</a>
</li>

<li class="menu-header">Master Data</li>

<li class="{{ request()->routeIs('materi.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('materi.index') }}">
<i class="fas fa-book"></i> <span>Materi</span>
</a>
</li>

<li class="{{ request()->routeIs('tugas.*') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('tugas.index') }}">
<i class="fas fa-list"></i> <span>Tugas</span>
</a>
</li>

{{-- ========== SISWA ========== --}}
@elseif (Auth::check() && Auth::user()->roles == 'siswa')
<li class="{{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('siswa.dashboard') }}">
<i class="fas fa-columns"></i> <span>Dashboard</span>
</a>
</li>

<li class="{{ request()->routeIs('siswa.materi') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('siswa.materi') }}">
<i class="fas fa-book"></i> <span>Materi</span>
</a>
</li>

<li class="{{ request()->routeIs('siswa.tugas') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('siswa.tugas') }}">
<i class="fas fa-list"></i> <span>Tugas</span>
</a>
</li>

{{-- ========== ORANG TUA ========== --}}
@elseif (Auth::check() && Auth::user()->roles == 'orangtua')
<li class="{{ request()->routeIs('orangtua.dashboard') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('orangtua.dashboard') }}">
<i class="fas fa-columns"></i> <span>Dashboard</span>
</a>
</li>

<li class="{{ request()->routeIs('orangtua.tugas.siswa') ? 'active' : '' }}">
<a class="nav-link" href="{{ route('orangtua.tugas.siswa') }}">
<i class="fas fa-list"></i> <span>Tugas Siswa</span>
</a>

</li>
@endif

{{-- ========== MENU UMUM ========== --}}
@if (Auth::check())
<li class="menu-header">Lainnya</li>
<li class="{{ request()->routeIs('nametag.cetak.pengguna') ? 'active' : ''
}}">
<a class="nav-link" href="{{ route('nametag.cetak.pengguna') }}">
<i class="fas fa-id-card"></i> <span>Cetak Nametag</span>
</a>
</li>
@endif
</ul>
</aside>
</div>