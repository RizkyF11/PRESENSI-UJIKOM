@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6">
                <h2>{{ isset($lokasi) && $lokasi->id ? 'Edit' : 'Tambah' }} Lokasi Kantor</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.lokasi-kantor.index') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active">Lokasi Kantor</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="card">
            <div class="header">
                <h2>Form Lokasi Kantor</h2>
            </div>

            <div class="body">
                <form action="{{ $route }}" method="POST">
                    @csrf
                    @if(isset($lokasi) && $lokasi->id)
                    @method('PUT')
                    @endif

                    <div class="form-group">
                        <label>Nama Lokasi</label>
                        <input type="text"
                            name="nama_lokasi"
                            class="form-control"
                            value="{{ old('nama_lokasi', $lokasi->nama_lokasi ?? '') }}"
                            required>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Latitude</label>
                            <input type="text"
                                id="latitude"
                                name="latitude"
                                class="form-control"
                                value="{{ old('latitude', $lokasi->latitude ?? '') }}"
                                readonly required>
                        </div>

                        <div class="col-md-4">
                            <label>Longitude</label>
                            <input type="text"
                                id="longitude"
                                name="longitude"
                                class="form-control"
                                value="{{ old('longitude', $lokasi->longitude ?? '') }}"
                                readonly required>
                        </div>

                        <div class="col-md-4">
                            <label>Radius (meter)</label>
                            <input type="number"
                                id="radius"
                                name="radius"
                                class="form-control"
                                value="{{ old('radius', $lokasi->radius ?? 100) }}"
                                required>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-success m-t-15"
                        onclick="getCurrentLocation()">
                        <i class="fa fa-map-marker"></i> Gunakan Lokasi Saya Saat Ini
                    </button>

                    <div id="map" style="height:450px;" class="m-t-15"></div>

                    <button type="submit" class="btn btn-primary m-t-20">
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() { // Gunakan DOMContentLoaded
        const mapContainer = document.getElementById('map');

        // Data dari PHP
        let defaultLat = Number("{{ $lokasi->latitude ?? -6.817253 }}");
        let defaultLng = Number("{{ $lokasi->longitude ?? 107.142730 }}");
        let defaultRadius = Number("{{ $lokasi->radius ?? 100 }}");

        // Inisialisasi Map
        const map = L.map('map').setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const marker = L.marker([defaultLat, defaultLng], {
            draggable: true
        }).addTo(map);
        const circle = L.circle([defaultLat, defaultLng], {
            radius: defaultRadius
        }).addTo(map);

        // Fungsi Update
        function updateMarker(newLat, newLng) {
            marker.setLatLng([newLat, newLng]);
            circle.setLatLng([newLat, newLng]);
            document.getElementById('latitude').value = newLat;
            document.getElementById('longitude').value = newLng;
        }

        map.on('click', function(e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function() {
            const pos = marker.getLatLng();
            updateMarker(pos.lat, pos.lng);
        });

        document.getElementById('radius').addEventListener('input', function() {
            circle.setRadius(this.value);
        });

        // Definisikan getCurrentLocation agar bisa dipanggil tombol
        window.getCurrentLocation = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const myLat = position.coords.latitude;
                    const myLng = position.coords.longitude;
                    map.setView([myLat, myLng], 17);
                    updateMarker(myLat, myLng);
                });
            } else {
                alert("Geolocation tidak didukung browser ini.");
            }
        };

        // --- KUNCI PERBAIKAN DI SINI ---
        // Panggil invalidateSize beberapa kali untuk memastikan map render sempurna
        setTimeout(function() {
            map.invalidateSize();
        }, 200);

        setTimeout(function() {
            map.invalidateSize();
        }, 600);
    });
</script>


@endsection