@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2><i class="fa fa-bar-chart"></i> Dashboard Analitik Helpdesk</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.helpdesk.tickets.index') }}">Helpdesk</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row clearfix row-deck">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card top_widget" style="border-top: 4px solid #4099FF;">
                <div class="body">
                    <div class="icon" style="background: rgba(64, 153, 255, 0.1); color: #4099FF;">
                        <i class="fa fa-ticket"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Total Tiket</div>
                        <h3 class="number mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        <small class="text-muted">Semua Aduan</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card top_widget" style="border-top: 4px solid #17a2b8;">
                <div class="body">
                    <div class="icon" style="background: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                        <i class="fa fa-envelope-open-o"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Open</div>
                        <h3 class="number mb-0">{{ $stats['open'] ?? 0 }}</h3>
                        <small class="text-muted">Belum Ditangani</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card top_widget" style="border-top: 4px solid #ffc107;">
                <div class="body">
                    <div class="icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">In-Progress</div>
                        <h3 class="number mb-0">{{ $stats['in_progress'] ?? 0 }}</h3>
                        <small class="text-muted">Sedang Berjalan</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="card top_widget" style="border-top: 4px solid #28a745;">
                <div class="body">
                    <div class="icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Closed</div>
                        <h3 class="number mb-0">{{ $stats['closed'] ?? 0 }}</h3>
                        <small class="text-muted">Terselesaikan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: Chart & Rating Average -->
    <div class="row clearfix row-deck">
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-line-chart"></i> Grafik Tiket Masuk (7 Hari Terakhir)</h2>
                </div>
                <div class="body">
                    <div style="position: relative; height: 320px;">
                        <canvas id="ticketsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-star text-warning"></i> Rata-Rata Kepuasan</h2>
                </div>
                <div class="body text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 320px;">
                    <div class="mb-3">
                        <h1 class="font-weight-bold mb-0" style="font-size: 64px; color: #FFC107;">
                            {{ number_format($avgRatingOverall, 1) }}
                        </h1>
                        <h5 class="text-muted">dari 5.0</h5>
                    </div>
                    
                    <div class="text-warning mb-4" style="font-size: 36px;">
                        @for($i=1; $i<=5; $i++)
                            @if($i <= floor($avgRatingOverall))
                                <i class="fa fa-star"></i>
                            @elseif($i - 0.5 <= $avgRatingOverall)
                                <i class="fa fa-star-half-o"></i>
                            @else
                                <i class="fa fa-star-o text-muted"></i>
                            @endif
                        @endfor
                    </div>
                    
                    <p class="text-muted px-4"><small>Penilaian keseluruhan rata-rata berdasarkan dari feedback rating Karyawan.</small></p>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 3: Operator & Category -->
    <div class="row clearfix row-deck">
        <div class="col-lg-8 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-users"></i> Performa Operator / Admin</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-left">Nama Operator</th>
                                    <th>Total Handle</th>
                                    <th>Rata-rata Response Time</th>
                                    <th>Rata-rata Resolution Time</th>
                                    <th>Rating Operator</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($operatorPerformance as $op)
                                    <tr>
                                        <td class="text-left font-weight-bold">{{ $op['nama'] }}</td>
                                        <td><span class="badge badge-info px-3">{{ $op['total_handled'] }} Tiket</span></td>
                                        <td>
                                            @if($op['avg_response'])
                                                {{ $op['avg_response'] }} <small class="text-muted">Mnt</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($op['avg_resolution'])
                                                {{ $op['avg_resolution'] }} <small class="text-muted">Mnt</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-warning font-weight-bold" style="font-size: 16px;">
                                            <i class="fa fa-star"></i> {{ number_format($op['avg_rating'], 1) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-4 text-muted">Belum ada data pengerjaan oleh admin.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3">
                        <small class="text-muted d-block mt-3">* Waktu SLA Response & Resolution Time dihitung dalam satuan menit sejak laporan dikirim (Created At).</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-pie-chart"></i> Sebaran Kategori Tiket</h2>
                </div>
                <div class="body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ticketsByCategory as $cat)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <strong>{{ ucfirst($cat->category) }}</strong>
                                <span class="badge badge-primary badge-pill">{{ $cat->total }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">Belum ada aduan</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 4: Ulasan Terkini -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-comments-o"></i> 5 Ulasan & Feedback Terbaru</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="150">Tanggal</th>
                                    <th width="120">Tiket ID</th>
                                    <th>Karyawan</th>
                                    <th width="120">Rating</th>
                                    <th>Komentar / Feedback</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFeedbacks as $feedback)
                                    <tr>
                                        <td><small class="text-muted">{{ $feedback->created_at->format('d M Y H:i') }}</small></td>
                                        <td><a href="{{ route('admin.helpdesk.tickets.show', $feedback->ticket_id) }}">#{{ $feedback->ticket_id }}</a></td>
                                        <td><strong>{{ optional($feedback->ticket->reporter)->nama ?? 'Anonim' }}</strong></td>
                                        <td class="text-warning font-weight-bold">
                                            @for($i=1; $i<=5; $i++)
                                                @if($i <= $feedback->score)
                                                    <i class="fa fa-star"></i>
                                                @else
                                                    <i class="fa fa-star-o text-muted"></i>
                                                @endif
                                            @endfor
                                        </td>
                                        <td><em class="text-muted">"{{ Str::limit($feedback->feedback, 60) }}"</em></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted border-0">
                                            Belum ada rating atau ulasan pesan kepuasan dari karyawan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const ctx = document.getElementById('ticketsChart');
        if (!ctx) return;

        // Ambil data dari backend PHP Blade Array
        const labels = @json(array_column($ticketsPerDay->toArray(), 'date'));
        const totals = @json(array_column($ticketsPerDay->toArray(), 'total'));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Tiket Masuk',
                    data: totals,
                    borderColor: '#4099FF',
                    backgroundColor: 'rgba(64, 153, 255, 0.2)',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4099FF',
                    pointRadius: 5,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    });
</script>
@endpush
