@extends('layouts.admin')

@section('content')
<div class="container-fluid ">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Data Shift</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.shift.index') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active">Shift</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">

        <div class="card ">
            <div class="header">
                <h2>Daftar Shift</h2>
            </div>
            <div class="body">
                <a href="{{ route('admin.shift.create') }}" class="btn btn-primary m-b-15">
                    <i class="fa fa-plus" aria-hidden="true"></i> Tambah Shift
                </a>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="addrowExample">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Shift</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Toleransi (Menit)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Nama Shift</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Toleransi (Menit)</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>

                        <tbody>
                            @foreach ($shift as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_shift }}</td>
                                <td>{{ $item->jam_masuk }}</td>
                                <td>{{ $item->jam_keluar }}</td>
                                <td>{{ $item->toleransi_menit }} menit</td>
                                <td>
                                    @if ($item->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                    @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.shift.edit', $item->id) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    @if ($item->is_active)
                                    <form action="{{ route('admin.shift.deactivate', $item->id) }}"
                                        method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Nonaktifkan shift ini?')">
                                            Nonaktifkan
                                        </button>
                                    </form>

                                    @else

                                    <form action="{{ route('admin.shift.activate', $item->id) }}"
                                        method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Aktifkan shift ini?')">
                                            Aktifkan
                                        </button>
                                    </form>

                                    @endif
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
@endsection