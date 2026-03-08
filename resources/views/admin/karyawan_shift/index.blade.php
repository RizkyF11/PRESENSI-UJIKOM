@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Assign Shift Karyawan</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#"><i class="fa fa-dashboard"></i></a>
                    </li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active">Assign Shift</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">

        <!-- FORM ASSIGN -->
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Form Assign Shift</h2>
                </div>
                <div class="body">
                    <form action="{{ route('admin.karyawan_shift.store') }}" method="POST">
                        @csrf

                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Karyawan</label>
                                    <select name="karyawan_id" class="form-control" required>
                                        <option value="">-- Pilih Karyawan --</option>
                                        @foreach($karyawan as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->user->nama ?? '-' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Shift</label>
                                    <select name="shift_id" class="form-control" required>
                                        <option value="">-- Pilih Shift --</option>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}">
                                                {{ $shift->nama_shift }}
                                                ({{ $shift->jam_masuk }} - {{ $shift->jam_keluar }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="date" name="tanggal_selesai" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary m-b-15">
                                    <i class="fa fa-save"></i> Assign
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- TABEL DATA -->
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Daftar Assign Shift</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Karyawan</th>
                                    <th>Shift</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->karyawan->user->nama }}</td>
                                        <td>
                                            {{ $item->shift->nama_shift }}
                                            ({{ $item->shift->jam_masuk }} - {{ $item->shift->jam_keluar }})
                                        </td>
                                        <td>{{ $item->tanggal_mulai }}</td>
                                        <td>{{ $item->tanggal_selesai }}</td>
                                        <td>
                                            <form action="{{ route('admin.karyawan_shift.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus assign shift ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No data available in table
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
@endsection
