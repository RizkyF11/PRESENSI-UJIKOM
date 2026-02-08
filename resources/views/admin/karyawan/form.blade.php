@extends('layouts.admin')
@section('content')


<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>{{isset($karyawan->id) ? 'Edit' : 'Tambah'}}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.karyawan.index') }}"><i class="fa fa-dashboard"></i></a></li>
                    <li class="breadcrumb-item">Karyawan</li>
                    <li class="breadcrumb-item active">{{isset($karyawan->id) ? 'Edit' : 'Tambah'}} Karyawan</li>
                </ul>
            </div>
        </div>
    </div>


    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>Informasi Akun & Data Diri</h2>
                </div>
                <div class="body">
                    <form id="basic-form" action="{{isset($karyawan->id) ? route('admin.karyawan.update', $karyawan->id) : route('admin.karyawan.store')}}" method="post" novalidate>
                        @csrf
                        @if(isset($karyawan->id))
                        @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $karyawan->nama ?? '') }}" required>
                                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $karyawan->email ?? '') }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Password {{ isset($karyawan->id) ? '(Kosongkan jika tidak ingin diganti)' : '' }}</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($karyawan->id) ? '' : 'required' }}>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NIP</label>
                                    <input type="number" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $karyawan->karyawan->nip ?? '') }}" required>
                                    @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label>Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $karyawan->karyawan->jabatan ?? '') }}" required>
                                    @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label>No. Handphone</label>
                                    <input type="number" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $karyawan->karyawan->no_hp ?? '') }}" required>
                                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat', $karyawan->karyawan->alamat ?? '') }}</textarea>
                                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            @if(isset($karyawan->id))
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <br />
                                    <label class="fancy-radio">
                                        <input type="radio" name="status" value="aktif" {{ old('status', $karyawan->karyawan->status ?? '') == 'aktif' ? 'checked' : '' }} required>
                                        <span><i></i>Aktif</span>
                                    </label>
                                    <label class="fancy-radio">
                                        <input type="radio" name="status" value="non-aktif" {{ old('status', $karyawan->karyawan->status ?? '') == 'non-aktif' ? 'checked' : '' }}>
                                        <span><i></i>Non-Aktif</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> {{ isset($karyawan->id) ? 'Simpan Perubahan' : 'Daftarkan Karyawan' }}
                        </button>
                        <a href="{{ route('admin.karyawan.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection