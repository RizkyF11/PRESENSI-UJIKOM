@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Kategori Penilaian</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Assessment</li>
                    <li class="breadcrumb-item active">Kategori Penilaian</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Daftar Kategori Penilaian</h2>
                </div>
                <div class="body">
                    <a href="{{ route('admin.assessment-categories.create') }}"
                        class="btn btn-primary m-b-15">
                        <i class="fa fa-plus" aria-hidden="true"></i> Tambah Kategori
                    </a>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped"
                            cellspacing="0" id="addrowExample">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($categories as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->description ?? '-' }}</td>
                                    <td>
                                        @if ($item->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                        @else
                                        <span class="badge badge-danger">Non Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.assessment-categories.edit', $item->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>

                                        {{-- Tombol Toggle Aktif/Nonaktif (seperti shift) --}}
                                        @if ($item->is_active)
                                        <form action="{{ route('admin.assessment-categories.toggle', $item->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary btn-sm"
                                                onclick="return confirm('Nonaktifkan kategori ini?')">
                                                <i class="fa fa-ban"></i> Nonaktifkan
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.assessment-categories.toggle', $item->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Aktifkan kategori ini?')">
                                                <i class="fa fa-check"></i> Aktifkan
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Tombol Hapus Permanen --}}
                                        <form action="{{ route('admin.assessment-categories.destroy', $item->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus kategori ini secara permanen?\nData penilaian terkait akan ikut terhapus!')">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
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