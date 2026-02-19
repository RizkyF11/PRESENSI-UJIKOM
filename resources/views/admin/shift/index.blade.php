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
                                    <a href="{{ route('admin.shift.edit', $item->id) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.shift.destroy', $item->id) }}"
                                        method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                            Hapus
                                        </button>
                                    </form>
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