@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- Header Section -->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2><i class="fa fa-bar-chart"></i> Dashboard</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">Dashboard Admin</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <div class="d-flex flex-row-reverse">
                    <div class="page_action">
                        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-warning">
                            <i class="fa fa-users"></i> Kelola Karyawan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 1: Main Stats Cards -->
    <div class="row clearfix row-deck">

        <!-- Total Karyawan -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #4099FF;">
                <div class="body">
                    <div class="icon" style="background: rgba(64, 153, 255, 0.1); color: #4099FF;">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Total Karyawan</div>
                        <h3 class="number mb-0">{{ $totalKaryawan }}</h3>
                        <small class="text-muted">Karyawan Aktif</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Shift -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #00BCD4;">
                <div class="body">
                    <div class="icon" style="background: rgba(0, 188, 212, 0.1); color: #00BCD4;">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Total Shift</div>
                        <h3 class="number mb-0">{{ $totalShift }}</h3>
                        <small class="text-muted">Shift Aktif</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Pengajuan -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #FF9800;">
                <div class="body">
                    <div class="icon" style="background: rgba(255, 152, 0, 0.1); color: #FF9800;">
                        <i class="fa fa-hourglass-half"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Pending Pengajuan</div>
                        <h3 class="number mb-0">{{ $pendingIzin + $pendingCuti }}</h3>
                        <small class="text-muted">Menunggu Approval</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kehadiran Hari Ini -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #4CAF50;">
                <div class="body">
                    <div class="icon" style="background: rgba(76, 175, 80, 0.1); color: #4CAF50;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Kehadiran Hari Ini</div>
                        <h3 class="number mb-0">{{ $absensiHariIni }}/{{ $totalSeharusnyaMasuk }}</h3>
                        <small class="text-muted">{{ $persentaseKehadiran }}% Hadir</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 2: Statistik Kehadiran Hari Ini -->
    <div class="row clearfix row-deck">

        <!-- Hadir -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #4CAF50;">
                <div class="body">
                    <div class="icon" style="background: rgba(76, 175, 80, 0.1); color: #4CAF50;">
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Hadir</div>
                        <h3 class="number mb-0">{{ $absensiHariIni }}</h3>
                        <small class="text-muted">Tepat Waktu Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #FF9800;">
                <div class="body">
                    <div class="icon" style="background: rgba(255, 152, 0, 0.1); color: #FF9800;">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Terlambat</div>
                        <h3 class="number mb-0">{{ $terlambatHariIni }}</h3>
                        <small class="text-muted">Melewati Toleransi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alpha -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #F44336;">
                <div class="body">
                    <div class="icon" style="background: rgba(244, 67, 54, 0.1); color: #F44336;">
                        <i class="fa fa-times-circle"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Alpha</div>
                        <h3 class="number mb-0">{{ $alphaHariIni }}</h3>
                        <small class="text-muted">Tanpa Keterangan</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Izin / Cuti -->
        <div class="col-lg-3 col-md-6 col-sm-6">
            <div class="card top_widget" style="border-top: 4px solid #9C27B0;">
                <div class="body">
                    <div class="icon" style="background: rgba(156, 39, 176, 0.1); color: #9C27B0;">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Izin / Cuti</div>
                        <h3 class="number mb-0">{{ $izinCutiHariIni->count() }}</h3>
                        <small class="text-muted">Disetujui Hari Ini</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 3: Pending Requests & Attendance Stats -->
    <div class="row clearfix">

        <!-- Pending Izin & Cuti -->
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-clock-o"></i> Pengajuan Izin & Cuti Pending</h2>
                    <ul class="header-dropdown dropdown-lag">
                        <li><a href="{{ route('admin.izin.index') }}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-right"></i> Lihat Semua</a></li>
                    </ul>
                </div>
                <div class="body">
                    @if($pendingPengajuan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Tipe</th>
                                    <th>Tanggal</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingPengajuan as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar mr-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                                {{ substr($item->karyawan->user->nama ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $item->karyawan->user->nama ?? '-' }}</strong>
                                                <br>
                                                <small class="text-muted">NIP: {{ $item->karyawan->nip ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->type == 'Izin' ? 'badge-info' : 'badge-warning' }}">
                                            {{ $item->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small title="{{ $item->alasan }}">
                                            {{ Str::limit($item->alasan, 20, '...') }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($item->type == 'Izin')
                                        <a href="{{ route('admin.izin.index', ['status' => 'pending']) }}" class="btn btn-sm btn-success" title="Approval">
                                            <i class="fa fa-check"></i>
                                        </a>
                                        @else
                                        <a href="{{ route('admin.cuti.index', ['status' => 'pending']) }}" class="btn btn-sm btn-success" title="Approval">
                                            <i class="fa fa-check"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-success" role="alert">
                        <i class="fa fa-check-circle"></i> Tidak ada pengajuan izin/cuti yang pending
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistik Kehadiran 7 Hari -->
        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-line-chart"></i> Statistik Kehadiran 7 Hari</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>Hari</th>
                                    <th class="text-center">Hadir</th>
                                    <th class="text-center">Terlambat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendanceStats as $stat)
                                <tr>
                                    <td><strong>{{ $stat['date'] }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $stat['hadir'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">{{ $stat['terlambat'] }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ROW 4: Izin/Cuti Hari Ini -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-calendar-check-o"></i> Karyawan Izin / Cuti Hari Ini</h2>
                    <ul class="header-dropdown dropdown-lag">
                        <li><a href="{{ route('admin.izin.index') }}" class="btn btn-sm btn-primary"><i class="fa fa-arrow-right"></i> Lihat Semua</a></li>
                    </ul>
                </div>
                <div class="body">
                    @if($izinCutiHariIni->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Tipe</th>
                                    <th>Periode</th>
                                    <th>Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($izinCutiHariIni as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar mr-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                                                {{ substr($item->karyawan->user->nama ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $item->karyawan->user->nama ?? '-' }}</strong>
                                                <br>
                                                <small class="text-muted">NIP: {{ $item->karyawan->nip ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->type == 'Izin' ? 'badge-info' : 'badge-warning' }}">
                                            {{ $item->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small title="{{ $item->alasan }}">
                                            {{ Str::limit($item->alasan, 30, '...') }}
                                        </small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info" role="alert">
                        <i class="fa fa-info-circle"></i> Tidak ada karyawan yang izin/cuti hari ini
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 5: Grafik Kehadiran -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-bar-chart"></i> Grafik Kehadiran 7 Hari Terakhir</h2>
                </div>
                <div class="body">
                    <div style="position: relative; height: 320px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Custom Styles -->
<style>
    .top_widget {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .top_widget:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .top_widget .body {
        display: flex;
        align-items: center;
        padding: 15px;
    }

    .top_widget .icon {
        font-size: 32px;
        padding: 15px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .top_widget .content {
        margin-left: 10px;
    }

    .top_widget .number {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .avatar {
        flex-shrink: 0;
    }
</style>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const ctx = document.getElementById('attendanceChart');
        if (!ctx) return;

        const labels = @json(array_column($attendanceStats, 'date'));
        const hadir = @json(array_column($attendanceStats, 'hadir'));
        const terlambat = @json(array_column($attendanceStats, 'terlambat'));
        const sakit = @json(array_column($attendanceStats, 'sakit'));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: hadir,
                        backgroundColor: 'rgba(76, 175, 80, 0.8)',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    },
                    {
                        label: 'Terlambat',
                        data: terlambat,
                        backgroundColor: 'rgba(255, 152, 0, 0.85)',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    },
                    {
                        label: 'Sakit/Izin',
                        data: sakit,
                        backgroundColor: 'rgba(64, 153, 255, 0.85)',
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 16,
                        }
                    },
                    tooltip: {
                        cornerRadius: 8,
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 11 },
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.3)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush