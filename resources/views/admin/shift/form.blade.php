@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>{{ isset($shift->id) ? 'Edit' : 'Tambah' }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.shift.index') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Master</li>
                    <li class="breadcrumb-item active">
                        {{ isset($shift->id) ? 'Edit' : 'Tambah' }} Shift
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Informasi Shift</h2>
                </div>
                <div class="body">
                    <form
                        action="{{ isset($shift->id) 
                            ? route('admin.shift.update', $shift->id) 
                            : route('admin.shift.store') }}"
                        method="POST"
                        novalidate>

                        @csrf
                        @if(isset($shift->id))
                        @method('PUT')
                        @endif

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Shift</label>
                                    <input type="text"
                                        name="nama_shift"
                                        class="form-control @error('nama_shift') is-invalid @enderror"
                                        value="{{ old('nama_shift', $shift->nama_shift ?? '') }}"
                                        required>
                                    @error('nama_shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Toleransi (Menit)</label>
                                    <input type="number"
                                        name="toleransi_menit"
                                        class="form-control @error('toleransi_menit') is-invalid @enderror"
                                        value="{{ old('toleransi_menit', $shift->toleransi_menit ?? '') }}"
                                        required>
                                    @error('toleransi_menit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jam Masuk</label>
                                    <input type="time"
                                        name="jam_masuk"
                                        class="form-control @error('jam_masuk') is-invalid @enderror"
                                        value="{{ old('jam_masuk', $shift->jam_masuk ?? '') }}"
                                        required>
                                    @error('jam_masuk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jam Keluar</label>
                                    <input type="time"
                                        name="jam_keluar"
                                        class="form-control @error('jam_keluar') is-invalid @enderror"
                                        value="{{ old('jam_keluar', $shift->jam_keluar ?? '') }}"
                                        required>
                                    @error('jam_keluar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <br>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            {{ isset($shift->id) ? 'Simpan Perubahan' : 'Simpan Shift' }}
                        </button>

                        <a href="{{ route('admin.shift.index') }}"
                            class="btn btn-secondary">
                            Batal
                        </a>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection