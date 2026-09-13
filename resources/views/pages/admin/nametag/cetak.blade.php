<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-
scale=1.0">

<title>Cetak Nametag</title>
<style>

@import
url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
body {
font-family: 'Poppins', sans-serif;
}
.nametag {
width: 321px; /* Ukuran standar kartu ID */
height: 204px;
border: 1px solid #ccc;
border-radius: 10px;
padding: 15px;
margin: 10px;
display: inline-block;
vertical-align: top;
box-sizing: border-box;
page-break-inside: avoid;
text-align: center;
}
.header {
display: flex;
align-items: center;
justify-content: center;
border-bottom: 2px solid #000;
padding-bottom: 5px;
}

.header img {
width: 40px;
height: 40px;
margin-right: 10px;
object-fit: contain;
}
.header h3 {
margin: 0;
font-size: 12px;
}
.content {
padding-top: 10px;
display: flex;
align-items: center;
}
.info {
flex-grow: 1;
}
.info h4 {
margin: 0;
font-size: 16px;
font-weight: 600;
}
.info p {
margin: 5px 0 0;

font-size: 12px;
color: #555;
}
.qrcode {
margin-left: 15px;
}
.no-print {
text-align: center;
margin: 20px;
}
@media print {
.no-print {
display: none;
}
.nametag {
border: 1px solid #000;
}
body {
margin: 0;
}
}
</style>
</head>
<body>

<div class="no-print">
<button onclick="window.print()">Cetak Halaman Ini</button>
</div>

@php
// fallback: ambil nilai dari $pengaturan jika ada, kalau tidak gunakan default
$peng = $pengaturan ?? null;
$logoPath = optional($peng)->logo ? asset(optional($peng)->logo) :
'https://via.placeholder.com/100';
$instName = optional($peng)->name ?? config('app.name', 'Nama
Institusi');
@endphp

@foreach($users as $user)
<div class="nametag">
<div class="header">
<img src="{{ $logoPath }}" alt="Logo">
<h3>{{ $instName }}</h3>
</div>

<div class="content">
<div class="info">
<h4>{{ $user->name }}</h4>
<p>{{ ucfirst($user->roles) }}</p>
</div>

<div class="qrcode">
{!! QrCode::size(80)->generate($user->id) !!}
</div>
</div>
</div>
@endforeach

</body>
</html>