@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2><i class="fa fa-ticket"></i> Antrean Helpdesk</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">Helpdesk Tickets</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <a href="{{ route('admin.helpdesk.dashboard') }}" class="btn btn-primary">
                    <i class="fa fa-bar-chart"></i> Analitik Helpdesk
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row clearfix row-deck">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card top_widget" style="border-top: 4px solid #17a2b8;">
                <div class="body">
                    <div class="icon" style="background: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                        <i class="fa fa-envelope-open-o"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Tiket Open</div>
                        <h3 class="number mb-0">{{ $counts['open'] ?? 0 }}</h3>
                        <small class="text-muted">Menunggu Respon</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card top_widget" style="border-top: 4px solid #ffc107;">
                <div class="body">
                    <div class="icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">In-Progress</div>
                        <h3 class="number mb-0">{{ $counts['in_progress'] ?? 0 }}</h3>
                        <small class="text-muted">Sedang Ditangani</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card top_widget" style="border-top: 4px solid #28a745;">
                <div class="body">
                    <div class="icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="content">
                        <div class="text mb-2 text-uppercase font-weight-bold">Closed</div>
                        <h3 class="number mb-0">{{ $counts['closed'] ?? 0 }}</h3>
                        <small class="text-muted">Selesai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table -->
    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header d-flex justify-content-between align-items-center">
                    <h2><i class="fa fa-list"></i> Daftar Tiket</h2>
                    
                    <form method="GET" action="{{ route('admin.helpdesk.tickets.index') }}" class="form-inline">
                        <div class="form-group mr-2">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">Semua Status</option>
                                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="In-Progress" {{ request('status') == 'In-Progress' ? 'selected' : '' }}>In-Progress</option>
                                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="form-group mr-2">
                            <select name="category" class="form-control form-control-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-info" title="Filter"><i class="fa fa-filter"></i></button>
                        <a href="{{ route('admin.helpdesk.tickets.index') }}" class="btn btn-sm btn-secondary ml-1" title="Reset"><i class="fa fa-refresh"></i></a>
                    </form>
                </div>
                <div class="body">
                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Subject</th>
                                        <th>Pelapor</th>
                                        <th>Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Prioritas</th>
                                        <th>Status</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $index => $ticket)
                                    <tr>
                                        <td>{{ $tickets->firstItem() + $index }}</td>
                                        <td>
                                            <strong title="{{ $ticket->subject }}">{{ Str::limit($ticket->subject, 30) }}</strong>
                                        </td>
                                        <td>
                                            {{ $ticket->reporter->nama ?? '-' }}
                                            <br>
                                            <small class="text-muted"><i class="fa fa-id-badge"></i> {{ $ticket->reporter->nip ?? '-' }}</small>
                                        </td>
                                        <td>{{ ucfirst($ticket->category) }}</td>
                                        <td>{{ Str::limit($ticket->description, 50) }}</td>
                                        <td>
                                            @if($ticket->priority == 'High')
                                                <span class="badge badge-danger">High</span>
                                            @elseif($ticket->priority == 'Mid')
                                                <span class="badge badge-warning">Mid</span>
                                            @else
                                                <span class="badge badge-secondary">Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->status == 'Open')
                                                <span class="badge badge-info">Open</span>
                                            @elseif($ticket->status == 'In-Progress')
                                                <span class="badge badge-warning">In-Progress</span>
                                            @elseif($ticket->status == 'Closed')
                                                <span class="badge badge-success">Closed</span>
                                            @else
                                                <span class="badge badge-dark">{{ $ticket->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.helpdesk.tickets.show', $ticket->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $tickets->withQueryString()->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="alert alert-info text-center mt-3">
                            <i class="fa fa-inbox fa-3x mb-3 d-block text-muted"></i>
                            <h5>Belum Ada Tiket</h5>
                            <p class="text-muted">Tidak ada data tiket aduan yang sesuai dengan kriteria yang ditentukan.</p>
                        </div>
                    @endif
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
