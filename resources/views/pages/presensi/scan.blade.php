@extends('layouts.main')
@section('title', 'Scan QR Code Presensi')
@section('content')
<section class="section custom-section">

<div class="section-body">
<div class="row">
<div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
<div class="card">
<div class="card-header">
<h4>Arahkan Kamera ke QR Code</h4>
</div>
<div class="card-body text-center">
@if (session('error'))
<div class="alert alert-danger">{{ session('error')
}}</div>
@endif
@if (session('success'))
<div class="alert alert-success">{{ session('success')
}}</div>
@endif
{{-- Wadah untuk menampilkan kamera scanner --}}
<div id="reader" style="width: 100%; max-width: 400px;
margin: auto;"></div>
{{-- Form ini disubmit oleh JavaScript setelah scan
berhasil --}}
<form id="form-presensi" action="{{route('presensi.store') }}" method="POST" class="d-none">
@csrf
<input type="hidden" name="token" id="token">
</form>
</div>
</div>
</div>
</div>
</div>
</section>
@endsection
@push('script')
{{-- Library JavaScript untuk QR Code Scanner --}}
<script src="https://unpkg.com/html5-qrcode"
type="text/javascript"></script>
<script>
function onScanSuccess(decodedText, decodedResult) {
// Fungsi ini akan berjalan ketika QR code berhasil terbaca
console.log(`Scan result: ${decodedText}`, decodedResult);
// Memasukkan hasil scan (token) ke dalam input form
document.getElementById('token').value = decodedText;
// Mengirim form secara otomatis
document.getElementById('form-presensi').submit();

// Menghentikan kamera setelah berhasil scan
html5QrcodeScanner.clear();
}
function onScanError(errorMessage) {
// Fungsi ini bisa diabaikan atau digunakan untuk logging
}
// Inisialisasi scanner
var html5QrcodeScanner = new Html5QrcodeScanner(
"reader", { fps: 10, qrbox: { width: 250, height: 250 } }); // Kotak
pemindaian 250x250px
// Mulai render kamera
html5QrcodeScanner.render(onScanSuccess, onScanError);
</script>
@endpush