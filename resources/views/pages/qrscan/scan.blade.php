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
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        
                        {{-- Wadah untuk menampilkan kamera scanner --}}
                        <div id="reader" style="width: 100%; max-width: 400px; margin: auto;"></div>
                        
                        {{-- Form ini disubmit oleh JavaScript setelah scan berhasil --}}
                        {{-- PENTING: Aksi form ini diarahkan ke PresensiController::store --}}
                        <form id="form-presensi" action="{{ route('presensi.store') }}" method="POST" class="d-none">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="hidden" name="tipe" id="tipe" value="masuk"> {{-- Asumsi default masuk --}}
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
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
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentLatitude, currentLongitude;
let html5QrcodeScanner;

// --- Fungsi untuk Mendapatkan Lokasi ---
function getLocation() {
    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentLatitude = position.coords.latitude;
                    currentLongitude = position.coords.longitude;
                    resolve(true);
                },
                (error) => {
                    let message = "Gagal mendapatkan lokasi.";
                    if (error.code === error.PERMISSION_DENIED) {
                        message = "Izin lokasi ditolak. Presensi memerlukan akses lokasi.";
                    }
                    console.error(message, error);
                    reject(message);
                },
                { enableHighAccuracy: true }
            );
        } else {
            reject('Geolocation tidak didukung oleh browser ini.');
        }
    });
}

// --- Fungsi Setelah Scan Sukses ---
async function onScanSuccess(decodedText) {
    // Hasil scan (decodedText) diasumsikan adalah user_id
    document.getElementById('user_id').value = decodedText;
    
    html5QrcodeScanner.pause();
    
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang memverifikasi lokasi dan mencatat presensi.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // 1. Dapatkan Lokasi sebelum submit
        await getLocation();
        
        // 2. Isi field lokasi
        document.getElementById('latitude').value = currentLatitude;
        document.getElementById('longitude').value = currentLongitude;

        // 3. Submit form ke PresensiController::store
        document.getElementById('form-presensi').submit();

    } catch (error) {
        Swal.fire({ 
            icon: 'error', 
            title: 'Gagal!', 
            text: error 
        });
        html5QrcodeScanner.resume();
    }
}

function onScanError(errorMessage) {
    // Bisa diabaikan atau digunakan untuk logging
}

document.addEventListener("DOMContentLoaded", function() {
    // Inisialisasi scanner
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { 
            fps: 10, 
            qrbox: { width: 250, height: 250 } 
        }, 
        false
    ); 
    
    // Mulai render kamera
    html5QrcodeScanner.render(onScanSuccess, onScanError);
});
</script>
@endpush