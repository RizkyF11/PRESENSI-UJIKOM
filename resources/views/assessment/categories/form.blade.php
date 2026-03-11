@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>{{ isset($category->id) ? 'Edit' : 'Tambah' }} Kategori Penilaian</h2>
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
                        {{ isset($category->id) ? 'Edit' : 'Tambah' }} Kategori
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>
                        {{ isset($category->id) ? 'Edit' : 'Tambah' }} Kategori Penilaian
                    </h2>
                </div>
                <div class="body">
                    <form action="{{ isset($category->id)
                            ? route('admin.assessment-categories.update', $category->id)
                            : route('admin.assessment-categories.store') }}"
                        method="POST">
                        @csrf
                        @if(isset($category->id))
                            @method('PUT')
                        @endif

                        <div class="row">
                            {{-- Nama Kategori --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        Nama Kategori
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="nama"
                                        class="form-control @error('nama') is-invalid @enderror"
                                        value="{{ old('nama', $category->nama ?? '') }}"
                                        placeholder="Cth: Disiplin, Kerja Sama, Tanggung Jawab"
                                        required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea
                                        name="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        rows="4"
                                        placeholder="Jelaskan indikator penilaian ini...">{{ old('description', $category->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            {{ isset($category->id) ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                        </button>
                        <a href="{{ route('admin.assessment-categories.index') }}"
                            class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
