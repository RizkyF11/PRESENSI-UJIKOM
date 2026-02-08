@extends('layouts.admin')

@section('content')
<div class="container-fluid ">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Data Karyawan</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.karyawan.index') }}"><i class="fa fa-dashboard"></i></a></li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active">Karyawan</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        
        <div class="card ">
            <div class="header">
                <h2>Daftar Karyawan</h2>
            </div>
            <div class="body">
                <a href="{{ route('admin.karyawan.create') }}" class="btn btn-primary m-b-15">
                    <i class="fa fa-plus" aria-hidden="true"></i> Tambah Karyawan
                </a>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="addrowExample">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Nip</th>
                                <th>Jabatan</th>
                                <th>No.Handphone</th>
                                <th>Alamat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Nip</th>
                                <th>Jabatan</th>
                                <th>No.Handphone</th>
                                <th>Alamat</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->karyawan->nip }}</td>
                                <td>{{ $item->karyawan->jabatan }}</td>
                                <td>{{ $item->karyawan->no_hp }}</td>
                                <td>{{ $item->karyawan->alamat }}</td>
                                <td>
                                    <a href="{{ route('admin.karyawan.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('admin.karyawan.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
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
    @endsection