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
                <div class="header">
                    <h2>Scan QR Code ini untuk melakukan absensi</h2>
                </div>
                <div class="body text-center">
                    <div id="qr-container" style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Kode Unik: <span id="kode-unik" class="text-primary">-</span></h5>
                        <p>Expired pada: <span id="expired-at" class="text-danger">-</span></p>
                    </div>

                    <div class="progress mt-3" style="height: 5px;">
                        <div id="progress-bar" class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timerInterval;

        function fetchQrCode() {
            fetch("{{ route('admin.qrcode.generate') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update QR Code
                        document.getElementById('qr-container').innerHTML = data.html;
                        document.getElementById('kode-unik').textContent = data.kode;
                        document.getElementById('expired-at').textContent = data.expired_at;

                        // Reset progress bar
                        resetProgressBar();
                    } else {
                        console.error('Error generating QR:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function resetProgressBar() {
            const progressBar = document.getElementById('progress-bar');
            let width = 100;
            const duration = 25000; // 25 seconds
            const interval = 100; // Update every 100ms
            const step = 100 / (duration / interval);

            // Clear existing interval if any
            if (timerInterval) clearInterval(timerInterval);

            progressBar.style.width = '100%';

            timerInterval = setInterval(() => {
                width -= step;
                progressBar.style.width = width + '%';

                if (width <= 0) {
                    clearInterval(timerInterval);
                    fetchQrCode(); // Refresh QR when expired
                }
            }, interval);
        }

        // Initial fetch
        fetchQrCode();
    });
</script>
@endpush
@endsection