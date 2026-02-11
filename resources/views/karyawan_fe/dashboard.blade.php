@extends('layouts.karyawan')

@section('content')
<div class="block-header">
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg-gradient-primary text-white shadow-sm mb-3">
                <div class="body d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="profile-image mr-3">
                            <img src="{{ asset('assets/images/user.png') }}" draggable="false" class="rounded-circle border border-white" alt="User" width="65">
                        </div>
                        <div>
                            <h4 class="mb-0 font-weight-bold">{{ Auth::user()->nama }}</h4>
                            <span class="text-light small">{{ Auth::user()->role ?? 'Karyawan' }}</span>
                            <br>
                            <span class="badge badge-light mt-1">{{ $tanggal }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('logout') }}" class="icon-menu"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off"></i>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- INFO SHIFT & ACTION -->
    <div class="row clearfix">
        <div class="col-lg-8 col-md-12">
            <div class="card bg-gradient-info text-white">
                <div class="body">
                    <div class="row align-items-center">
                        <div class="col-md-7 col-sm-12">
                            <h4 class="mb-0">Selamat Datang, {{ Auth::user()->name }}</h4>
                            <p class="mb-2">Shift Anda hari ini:</p>
                            @if($shiftHariIni)
                            <h3 class="font-weight-bold mb-0">{{ $shiftHariIni->nama_shift }}</h3>
                            <p class="mb-0"><i class="fa fa-clock-o"></i> {{ $shiftHariIni->jam_masuk }} - {{ $shiftHariIni->jam_keluar }} WIB</p>
                            @else
                            <h3 class="font-weight-bold mb-0">Libur / Tidak Ada Jadwal</h3>
                            @endif
                        </div>
                        <div class="col-md-5 col-sm-12 text-md-right mt-3 mt-md-0">
                            <a href="{{ route('karyawan.scan') }}" class="btn btn-light btn-lg btn-block text-primary font-weight-bold shadow">
                                <i class="fa fa-qrcode mr-2"></i> SCAN ABSENSI
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header pb-0">
                    <h2><strong>Status</strong> Hari Ini</h2>
                </div>
                <div class="body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <small>Masuk:</small>
                            <h5 class="mb-0 font-weight-bold text-success">
                                {{ $absensiHariIni && $absensiHariIni->jam_masuk ? $absensiHariIni->jam_masuk : '--:--:--' }}
                            </h5>
                        </li>
                        <li>
                            <small>Keluar:</small>
                            <h5 class="mb-0 font-weight-bold text-danger">
                                {{ $absensiHariIni && $absensiHariIni->jam_keluar ? $absensiHariIni->jam_keluar : '--:--:--' }}
                            </h5>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW SECTIONS -->
    <div class="row clearfix">
        <!-- View Shift Schedule -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="body text-center">
                    <a href="#" class="btn btn-info btn-lg btn-block">
                        <i class="fa fa-calendar"></i> Lihat Jadwal Shift
                    </a>
                </div>
            </div>
        </div>

        <!-- View Absence History -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="body text-center">
                    <a href="#" class="btn btn-warning btn-lg btn-block">
                        <i class="fa fa-history"></i> Riwayat Absensi
                    </a>
                </div>
            </div>
        </div>

        <!-- Leave Request -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="body text-center">
                    <a href="#" class="btn btn-success btn-lg btn-block">
                        <i class="fa fa-envelope"></i> Pengajuan Izin / Cuti
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIK -->
    <div class="row clearfix">
        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card" style="border-radius: 15px;">
                <div class="body text-center">
                    <div class="icon-in-bg bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 45px; height: 45px;">
                        <i class="fa fa-check"></i>
                    </div>
                    <span>Hadir</span>
                    <h4 class="mb-0 font-weight-bold">{{ $stats['hadir'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card" style="border-radius: 15px;">
                <div class="body text-center">
                    <div class="icon-in-bg bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 45px; height: 45px;">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <span>Terlambat</span>
                    <h4 class="mb-0 font-weight-bold">{{ $stats['terlambat'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card" style="border-radius: 15px;">
                <div class="body text-center">
                    <div class="icon-in-bg bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 45px; height: 45px;">
                        <i class="fa fa-envelope-o"></i>
                    </div>
                    <span>Izin/Sakit</span>
                    <h4 class="mb-0 font-weight-bold">{{ $stats['izin_sakit'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-6">
            <div class="card" style="border-radius: 15px;">
                <div class="body text-center">
                    <div class="icon-in-bg bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 45px; height: 45px;">
                        <i class="fa fa-times"></i>
                    </div>
                    <span>Alpha</span>
                    <h4 class="mb-0 font-weight-bold">{{ $stats['alpha'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection