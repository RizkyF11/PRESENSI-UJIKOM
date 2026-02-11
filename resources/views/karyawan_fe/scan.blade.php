@extends('layouts.karyawan')

@section('content')
<div class="container-fluid">
    <div class="row clearfix justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card">
                <div class="header text-center">
                    <h2><strong>Scan QR Code</strong> Absensi</h2>
                    <ul class="header-dropdown">
                        <li class="dropdown">
                            <a href="{{ route('karyawan.dashboard') }}" class="btn btn-secondary text-white"><i class="fa fa-arrow-left"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <!-- CAMERA CONTAINER -->
                    <style>
                        #reader {
                            width: 100% !important;
                            border-radius: 25px;
                            overflow: hidden;
                            background: #000;
                            position: relative;
                            border: 4px solid #f4f7f6;
                            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                        }

                        /* Memastikan video memenuhi kotak di HP */
                        #reader video {
                            object-fit: cover !important;
                            width: 100% !important;
                            height: auto !important;
                            min-height: 300px;
                        }

                        /* Sembunyikan tombol stop/start bawaan library yang mengganggu UI */
                        #reader button {
                            padding: 10px 20px;
                            border-radius: 5px;
                            border: none;
                            background-color: #007bff;
                            color: white;
                            margin-top: 10px;
                            cursor: pointer;
                        }

                        @media (max-width: 576px) {
                            #reader {
                                border-radius: 0;
                                /* Full screen feel di HP */
                            }

                            .container-fluid {
                                padding-left: 5px;
                                padding-right: 5px;
                            }
                        }
                    </style>
                    <div class="text-center mb-3">
                        <div id="reader"></div>
                    </div>

                    <div class="alert alert-warning text-center" role="alert">
                        <i class="fa fa-info-circle"></i> Pastikan izin kamera dan lokasi diaktifkan.
                    </div>

                    <!-- Debug result -->
                    <div id="result-message" class="alert d-none mt-2 text-center font-weight-bold"></div>
                </div>
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
            resultMessage.className = 'alert alert-info mt-2 text-center font-weight-bold';
            resultMessage.classList.remove('d-none');
            resultMessage.innerText = 'Memproses data absensi...';

            // Get Location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        sendAbsensiData(decodedText, latitude, longitude);
                    },
                    (error) => {
                        handleError('Gagal mendapatkan lokasi: ' + error.message);
                        isProcessing = false;
                    }
                );
            } else {
                handleError('Browser tidak support Geolocation.');
                isProcessing = false;
            }
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning.
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

            // Add CSRF Token
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
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        resultMessage.className = 'alert alert-success mt-2 text-center font-weight-bold';
                        resultMessage.innerHTML = `<strong>Berhasil!</strong> ${data.message} <br> ${data.data.jam_masuk || data.data.jam_keluar || ''}`;

                        setTimeout(() => {
                            window.location.href = "{{ route('karyawan.dashboard') }}";
                        }, 2000);
                    } else {
                        handleError(data.message);
                        isProcessing = false;
                    }
                })
                .catch(error => {
                    handleError('Terjadi kesalahan server.');
                    console.error(error);
                    isProcessing = false;
                });
        }

        function handleError(msg) {
            resultMessage.className = 'alert alert-danger mt-2 text-center font-weight-bold';
            resultMessage.classList.remove('d-none');
            resultMessage.innerText = msg;
        }

        // Initialize Scanner
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", {
                fps: 15,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    // Responsif QR Box: 70% dari lebar layar HP
                let minEdgePercentage = 0.7; 
                let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                return {
                    width: qrboxSize,
                    height: qrboxSize
                };
              },
                aspectRatio: 1.0,
                videoConstraints: {
                    facingMode: { exact: "environment" }
                }
            },
            false
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    });
</script>
@endpush

@endsection