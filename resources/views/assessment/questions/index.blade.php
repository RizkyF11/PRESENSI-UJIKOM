@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Pertanyaan Penilaian</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.assessment-categories.index') }}">
                            Kategori Penilaian
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        Pertanyaan — {{ $category->nama }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">

            {{-- Info Kategori --}}
            <div class="card">
                <div class="body py-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">
                                <i class="fa fa-folder-open text-primary"></i>
                                {{ $category->nama }}
                            </h5>
                            @if($category->description)
                                <small class="text-muted">{{ $category->description }}</small>
                            @endif
                        </div>
                        <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $category->is_active ? 'Aktif' : 'Non Aktif' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tabel Pertanyaan --}}
            <div class="card">
                <div class="header">
                    <h2>Daftar Pertanyaan</h2>
                </div>
                <div class="body">
                    <a href="{{ route('admin.assessment-questions.create', $category->id) }}"
                        class="btn btn-primary m-b-15">
                        <i class="fa fa-plus"></i> Tambah Pertanyaan
                    </a>
                    <a href="{{ route('admin.assessment-categories.index') }}"
                        class="btn btn-secondary m-b-15">
                        <i class="fa fa-arrow-left"></i> Kembali ke Kategori
                    </a>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pertanyaan</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Pertanyaan</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($questions as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->question }}</td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $item->urutan }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Non Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('admin.assessment-questions.edit', [$category->id, $item->id]) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>

                                        {{-- Tombol Toggle Aktif/Nonaktif --}}
                                        @if($item->is_active)
                                        <form action="{{ route('admin.assessment-questions.toggle', [$category->id, $item->id]) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-secondary btn-sm"
                                                onclick="return confirm('Nonaktifkan pertanyaan ini?')">
                                                <i class="fa fa-ban"></i> Nonaktifkan
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('admin.assessment-questions.toggle', [$category->id, $item->id]) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Aktifkan pertanyaan ini?')">
                                                <i class="fa fa-check"></i> Aktifkan
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.assessment-questions.destroy', [$category->id, $item->id]) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus pertanyaan ini?\nData penilaian terkait akan ikut terhapus!')">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fa fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">
                                            Belum ada pertanyaan untuk kategori ini.
                                            <a href="{{ route('admin.assessment-questions.create', $category->id) }}">
                                                Tambah sekarang
                                            </a>
                                        </p>
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