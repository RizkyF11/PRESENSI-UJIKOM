@extends('layouts.karyawan')

@section('header-left')
<div class="flex items-center w-full">
    <!-- Tombol kembali yang langsung kembali ke dashboard karyawan -->
    <a href="{{ route('karyawan.dashboard') }}" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-bold" style="text-decoration: none;">
        <div class="bg-teal-50 w-8 h-8 rounded-full flex items-center justify-center">
            <span class="iconify" data-icon="heroicons:arrow-left" data-width="20"></span>
        </div>
    </a>
    <h1 class="text-[16px] font-bold text-gray-800 ml-auto mr-auto pl-4 mb-0">Scan Absensi</h1>
    <div class="w-20"></div> <!-- Placeholder space -->
</div>
@endsection

@section('content')
<div class="container-fluid mb-4 px-2">
    <div class="row justify-content-center">
        <div class="col-12 mt-3">
            <div class="card p-4 shadow-sm" style="border-radius: 20px;">
                <div class="text-center mb-4">
                    <h5 class="font-weight-bold text-gray-800 mb-1">Arahkan QR Code</h5>
                    <p class="text-muted small">Pastikan QR Code berada pas di dalam kotak scan.</p>
                </div>

                <!-- CAMERA CONTAINER -->
                <style>
                    #reader {
                        width: 100% !important;
                        border-radius: 16px;
                        overflow: hidden;
                        background: #000;
                        position: relative;
                        border: 3px solid #E5E7EB;
                    }

                    #reader video {
                        object-fit: cover !important;
                        width: 100% !important;
                        min-height: 350px !important;
                    }

                    #reader img {
                        display: none !important;
                    }

                    /* Memperbaiki tombol Start Scan bawaan library agar lebih bagus */
                    #reader__dashboard_section_csr button {
                        background-color: #4DB6AC;
                        color: white !important;
                        border: none;
                        border-radius: 8px;
                        padding: 10px 16px;
                        font-weight: 600;
                        margin-bottom: 10px;
                        cursor: pointer;
                        box-shadow: 0 2px 4px rgba(77, 182, 172, 0.4);
                    }

                    #reader__dashboard_section_csr span {
                        color: #fff !important;
                    }

                    #reader__dashboard_section_swaplink {
                        color: #4DB6AC;
                        text-decoration: none;
                        font-weight: bold;
                        margin-top: 10px;
                        display: inline-block;
                    }
                </style>
                <div id="reader"></div>

                <div class="alert alert-info d-flex align-items-center mt-4 mb-0 border-0 shadow-sm" style="border-radius: 12px; font-size: 13px; background-color: #E0F2F1; color: #00796B;">
                    <span class="iconify mr-2 h4 mb-0" style="color: #00796B;" data-icon="heroicons:information-circle-solid"></span>
                    <span class="text-left font-weight-bold">Pastikan Akses Kamera & GPS/Lokasi perangkat telah DIIZINKAN (ALLOW).</span>
                </div>

                <!-- Debug result -->
                <div id="result-message" class="alert d-none mt-3 text-center font-weight-bold shadow-sm" style="border-radius: 12px; font-size: 14px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resultMessage = document.getElementById('result-message');
        let isProcessing = false;

        function onScanSuccess(decodedText, decodedResult) {
            // Prevent multiple scans
            if (isProcessing) return;
            isProcessing = true;

            // Show processing
            resultMessage.className = 'alert alert-warning mt-3 text-center font-weight-bold shadow-sm';
            resultMessage.classList.remove('d-none');
            resultMessage.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Memproses data absensi...';

            // Get Location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        sendAbsensiData(decodedText, latitude, longitude);
                    },
                    (error) => {
                        handleError('Gagal mendapatkan lokasi. Aktifkan GPS dan izinkan Akses!');
                        isProcessing = false;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                handleError('Browser Anda tidak support Geolocation.');
                isProcessing = false;
            }
        }

        function onScanFailure(error) {
            // Ignored, Keep scanning
        }

        function sendAbsensiData(qrData, lat, long) {
            let qrCode = qrData;
            let tipeAbsen = 'masuk';

            try {
                const parsed = JSON.parse(qrData);
                qrCode = parsed.kode;
                if (parsed.qr_type) tipeAbsen = parsed.qr_type;
            } catch (e) {
                console.log('QR Raw Data used');
            }

            let url = "{{ route('karyawan.scan.store') }}";

            const csrfToken = document.querySelector('meta[name="csrf-token"]') ?
                document.querySelector('meta[name="csrf-token"]').getAttribute('content') :
                "{{ csrf_token() }}";

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        kode: qrCode,
                        latitude: lat,
                        longitude: long,
                        tipe_scan: tipeAbsen
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || "Terjadi kesalahan");
                    }
                    return data;
                })
                .then(data => {
                    if (data.status === 'success') {
                        resultMessage.className = 'alert alert-success mt-3 text-center font-weight-bold shadow-sm';
                        resultMessage.innerHTML = `<span class="iconify mr-1" data-icon="heroicons:check-circle" data-width="20"></span> Berhasil! ${data.message}`;

                        setTimeout(() => {
                            window.location.href = "{{ route('karyawan.dashboard') }}";
                        }, 2000);
                    } else {
                        handleError(data.message);
                        isProcessing = false;
                    }
                })
                .catch(error => {
                    handleError(error.message);
                    console.error(error);
                    setTimeout(() => {
                        isProcessing = false;
                    }, 2000);
                });
        }

        function handleError(msg) {
            resultMessage.className = 'alert alert-danger mt-3 text-center font-weight-bold shadow-sm';
            resultMessage.classList.remove('d-none');
            resultMessage.innerText = msg;
        }

        // Initialize Scanner
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 15,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    let minEdgePercentage = 0.7;
                    let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                    return {
                        width: qrboxSize,
                        height: qrboxSize
                    };
                },
                aspectRatio: 1.0,
            },
            false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>
@endpush
@endsection