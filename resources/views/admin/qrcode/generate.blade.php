@extends('layouts.admin')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h2>QR Code Absensi</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                <li class="breadcrumb-item active">QR Code</li>
            </ul>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="masuk-tab" data-toggle="tab" href="#masuk" role="tab" aria-controls="masuk" aria-selected="true">QR Absen Masuk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="keluar-tab" data-toggle="tab" href="#keluar" role="tab" aria-controls="keluar" aria-selected="false">QR Absen Keluar</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="myTabContent">

                        <!-- TAB MASUK -->
                        <div class="tab-pane fade show active" id="masuk" role="tabpanel" aria-labelledby="masuk-tab">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="header text-center">
                                        <h2>QR Code <strong class="text-success">MASUK</strong></h2>
                                        <small>Scan untuk melakukan absensi MASUK</small>
                                    </div>
                                    <div class="body text-center">
                                        <div id="qr-masuk-container" style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>

                                        <div class="mt-4">

                                            <p>Expired pada: <span id="expired-at-masuk" class="text-danger">-</span></p>
                                            <p class="text-muted"><small>Refresh otomatis dalam: <b><span id="countdown-masuk">60</span></b> detik</small></p>
                                        </div>

                                        <div class="progress mt-3" style="height: 5px;">
                                            <div id="progress-bar-masuk" class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB KELUAR -->
                        <div class="tab-pane fade" id="keluar" role="tabpanel" aria-labelledby="keluar-tab">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="header text-center">
                                        <h2>QR Code <strong class="text-danger">KELUAR</strong></h2>
                                        <small>Scan untuk melakukan absensi KELUAR</small>
                                    </div>
                                    <div class="body text-center">
                                        <div id="qr-keluar-container" style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                                            <div class="spinner-border text-danger" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </div>

                                        <div class="mt-4">

                                            <p>Expired pada: <span id="expired-at-keluar" class="text-danger">-</span></p>
                                            <p class="text-muted"><small>Refresh otomatis dalam: <b><span id="countdown-keluar">60</span></b> detik</small></p>
                                        </div>

                                        <div class="progress mt-3" style="height: 5px;">
                                            <div id="progress-bar-keluar" class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- LOGIC QR MASUK ---
        let timerIntervalMasuk;
        let countdownMasuk = 60; // Default seconds

        function fetchQrMasuk() {
            fetch("{{ route('admin.qrcode.generate', ['tipe' => 'masuk']) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('qr-masuk-container').innerHTML = data.html;

                        document.getElementById('expired-at-masuk').textContent = data.expired_at;

                        // Restart countdown
                        startCountdownMasuk(60);
                    } else {
                        console.error('Error generating QR Masuk:', data.message);
                        // Retry after 5 seconds on logic error
                        startCountdownMasuk(5);
                    }
                })
                .catch(error => {
                    console.error('Error Masuk:', error);
                    // Retry after 5 seconds on network error
                    startCountdownMasuk(5);
                });
        }

        function startCountdownMasuk(seconds) {
            let timeLeft = seconds;
            const progressBar = document.getElementById('progress-bar-masuk');
            const countdownText = document.getElementById('countdown-masuk'); // Ensure this element exists

            if (timerIntervalMasuk) clearInterval(timerIntervalMasuk);

            // Set initial state
            progressBar.style.width = '100%';
            if (countdownText) countdownText.textContent = timeLeft;

            const intervalTime = 1000; // 1 second

            timerIntervalMasuk = setInterval(() => {
                timeLeft--;

                // Update UI
                const percentage = (timeLeft / seconds) * 100;
                progressBar.style.width = percentage + '%';
                if (countdownText) countdownText.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timerIntervalMasuk);
                    fetchQrMasuk();
                }
            }, intervalTime);
        }

        // --- LOGIC QR KELUAR ---
        let timerIntervalKeluar;
        let countdownKeluar = 60;

        function fetchQrKeluar() {
            fetch("{{ route('admin.qrcode.generate', ['tipe' => 'keluar']) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('qr-keluar-container').innerHTML = data.html;

                        document.getElementById('expired-at-keluar').textContent = data.expired_at;

                        startCountdownKeluar(60);
                    } else {
                        console.error('Error generating QR Keluar:', data.message);
                        startCountdownKeluar(5);
                    }
                })
                .catch(error => {
                    console.error('Error Keluar:', error);
                    startCountdownKeluar(5);
                });
        }

        function startCountdownKeluar(seconds) {
            let timeLeft = seconds;
            const progressBar = document.getElementById('progress-bar-keluar');
            const countdownText = document.getElementById('countdown-keluar');

            if (timerIntervalKeluar) clearInterval(timerIntervalKeluar);

            progressBar.style.width = '100%';
            if (countdownText) countdownText.textContent = timeLeft;

            const intervalTime = 1000;

            timerIntervalKeluar = setInterval(() => {
                timeLeft--;

                const percentage = (timeLeft / seconds) * 100;
                progressBar.style.width = percentage + '%';
                if (countdownText) countdownText.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timerIntervalKeluar);
                    fetchQrKeluar();
                }
            }, intervalTime);
        }

        // Helper function to stop timers
        function stopAllTimers() {
            if (timerIntervalMasuk) clearInterval(timerIntervalMasuk);
            if (timerIntervalKeluar) clearInterval(timerIntervalKeluar);
        }

        // Event for clicking "QR Absen Masuk" tab
        document.getElementById('masuk-tab').addEventListener('click', function() {
            // Stop logic keluar
            if (timerIntervalKeluar) clearInterval(timerIntervalKeluar);

            // Start logic masuk immediately
            // But check if it's already running or just let fetchQrMasuk handle restart
            fetchQrMasuk();
        });

        // Event for clicking "QR Absen Keluar" tab
        document.getElementById('keluar-tab').addEventListener('click', function() {
            // Stop logic masuk
            if (timerIntervalMasuk) clearInterval(timerIntervalMasuk);

            // Start logic keluar immediately
            fetchQrKeluar();
        });

        // Initial state: Start Masuk, ensure Keluar is stopped
        fetchQrMasuk();
    });
</script>
@endpush
@endsection