@extends('layouts.admin')

@section('content')
<div class="container-fluid ">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Data Absensi</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.absensi.index') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Transaksi</li>
                    <li class="breadcrumb-item active">Riwayat Absensi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="card">
            <div class="header">
                <h2>Riwayat Absensi Karyawan</h2>
            </div>

            <div class="body">

                {{-- FILTER --}}
                <form method="GET" action="{{ route('admin.absensi.index') }}" class="mb-4">
                    <div class="row">

                        <div class="col-md-3">
                            <label>Dari Tanggal</label>
                            <input type="date" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai', $tanggalMulai->format('Y-m-d')) }}"
                                class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label>Sampai Tanggal</label>
                            <input type="date" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai', $tanggalSelesai->format('Y-m-d')) }}"
                                class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label>Karyawan</label>
                            <select name="karyawan_id" class="form-control">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach($karyawan as $k)
                                <option value="{{ $k->id }}"
                                    {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->user->nama ?? '-' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary mr-2">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>


                @if(request()->filled('tanggal_mulai') && request()->filled('tanggal_selesai'))
                <form action="{{ route('admin.absensi.export') }}" method="GET" class="mb-4">

                    <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                    <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    <input type="hidden" name="karyawan_id" value="{{ request('karyawan_id') }}">

                    <button class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i> Export Rekap Excel
                    </button>

                </form>
                @endif

                {{-- HAPUS MASSAL SESUAI FILTER --}}
                <form action="{{ route('admin.absensi.destroyAll') }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus semua data sesuai filter?')">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                    <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                    <input type="hidden" name="karyawan_id" value="{{ request('karyawan_id') }}">

                    <button class="btn btn-danger mb-3">
                        <i class="fa fa-trash"></i> Hapus Semua Sesuai Filter
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Shift</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Status</th>
                                <th>Shift</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @forelse ($absensi as $item)

                            @php
                            $status = 'Alpha';

                            if($izin->where('karyawan_id', $item->karyawan_id)
                            ->where('tanggal_mulai', '<=', $item->tanggal)
                                ->where('tanggal_selesai', '>=', $item->tanggal)
                                ->count()) {

                                $status = 'Izin';

                                }
                                elseif($cuti->where('karyawan_id', $item->karyawan_id)
                                ->where('tanggal_mulai', '<=', $item->tanggal)
                                    ->where('tanggal_selesai', '>=', $item->tanggal)
                                    ->count()) {

                                    $status = 'Cuti';

                                    }
                                    elseif($item->status_masuk === 'terlambat') {

                                    $status = 'Terlambat';

                                    }
                                    elseif(!is_null($item->jam_masuk)) {

                                    $status = 'Hadir';

                                    }
                                    @endphp

                                    <tr>
                                        <td>
                                            {{ $loop->iteration + ($absensi->currentPage() - 1) * $absensi->perPage() }}
                                        </td>
                                        <td>{{ $item->karyawan->user->nama ?? '-' }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                        </td>
                                        <td>{{ $item->jam_masuk ?? '-' }}</td>
                                        <td>{{ $item->jam_keluar ?? '-' }}</td>
                                        <td>
                                            @if($status == 'Hadir')
                                            <span class="badge badge-success">Hadir</span>
                                            @elseif($status == 'Terlambat')
                                            <span class="badge badge-warning">Terlambat</span>
                                            @elseif($status == 'Alpha')
                                            <span class="badge badge-danger">Alpha</span>
                                            @elseif($status == 'Izin')
                                            <span class="badge badge-info">Izin</span>
                                            @elseif($status == 'Cuti')
                                            <span class="badge badge-secondary">Cuti</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->shift->nama_shift ?? '-' }}</td>
                                        <td>
                                            <form action="{{ route('admin.absensi.destroy', $item->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            No data available in table
                                        </td>
                                    </tr>
                                    @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-3 d-flex justify-content-center">
                    {{ $absensi->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection