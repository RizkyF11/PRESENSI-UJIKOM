@extends('layouts.karyawan')

@section('content')

<div class="container-fluid pt-2">
    <!-- Alert Success -->
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 12px;" role="alert">
        <div class="d-flex align-items-center">
            <span class="iconify mr-2" data-icon="heroicons:check-circle"></span>
            <span>{{ $message }}</span>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Profile Header Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #4DB6AC 0%, #2C7A7B 100%); overflow: hidden; position: relative;">
        <!-- Background Decoration -->
        <div style="position: absolute; right: -50px; top: -50px; opacity: 0.08;">
            <span class="iconify" data-icon="heroicons:user-circle" data-width="250" style="color: white;"></span>
        </div>

        <div class="card-body p-4 position-relative z-10">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <!-- Avatar (Consistent with Header) -->
                    <div class="d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; border-radius: 50%; background-color: rgba(255,255,255, 0.2); font-size: 28px; font-weight: bold; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15); backdrop-filter: blur(10px);">
                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                    </div>

                    <!-- User Info -->
                    <div class="text-white">
                        <h5 class="font-weight-bold mb-2" style="font-size: 18px;">{{ $user->nama }}</h5>
                        <div style="font-size: 12px; opacity: 0.9; line-height: 1.6;">
                            <p class="mb-1 d-flex align-items-center">
                                <span class="iconify mr-2" data-icon="heroicons:envelope" style="font-size: 16px;"></span>
                                {{ $user->email }}
                            </p>
                            @if($karyawan && $karyawan->jabatan)
                            <p class="mb-0 d-flex align-items-center">
                                <span class="iconify mr-2" data-icon="heroicons:briefcase" style="font-size: 16px;"></span>
                                {{ $karyawan->jabatan }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Edit Button -->
                <a href="{{ route('karyawan.profile.edit') }}" class="btn btn-sm px-4 py-2 font-weight-bold" style="background-color: white; color: #2C7A7B; border-radius: 10px; font-size: 13px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.2s;">
                    <span class="iconify mr-1" data-icon="heroicons:pencil" style="vertical-align: -2px;"></span> Edit
                </a>
            </div>
        </div>
    </div>

    <!-- Information Grid -->
    <div class="row g-3">
        <!-- Left Column: Main Info -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white border-bottom p-4" style="border-bottom: 1px solid #E5E7EB !important;">
                    <h6 class="font-weight-bold text-gray-800 mb-0 d-flex align-items-center" style="font-size: 15px;">
                        <span class="iconify mr-2" data-icon="heroicons:user" style="color: #4DB6AC; font-size: 20px;"></span>
                        Informasi Pribadi
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <p class="text-gray-500 mb-2 font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Nama Lengkap</p>
                            <p class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $user->nama }}</p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-gray-500 mb-2 font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Email</p>
                            <p class="font-weight-bold text-gray-800 mb-0 text-break" style="font-size: 14px;">{{ $user->email }}</p>
                        </div>

                        @if($karyawan)
                        <div class="col-md-6">
                            <p class="text-gray-500 mb-2 font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">NIP</p>
                            <p class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $karyawan->nip ?? '-' }}</p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-gray-500 mb-2 font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Jabatan</p>
                            <p class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $karyawan->jabatan ?? '-' }}</p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-gray-500 mb-2 font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">No. Telepon</p>
                            <p class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $karyawan->no_hp ?? '-' }}</p>
                        </div>

                        <div class="col-md-6">
                            <p class="text-gray-500 mb-2 font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Status</p>
                            @if($karyawan->status === 'aktif')
                            <span class="badge" style="background-color: #DCFCE7; color: #15803D; border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 600;">
                                <span class="iconify mr-1" data-icon="heroicons:check-circle" style="vertical-align: -2px;"></span>Aktif
                            </span>
                            @else
                            <span class="badge" style="background-color: #FEE2E2; color: #DC2626; border-radius: 6px; padding: 6px 12px; font-size: 12px; font-weight: 600;">
                                <span class="iconify mr-1" data-icon="heroicons:x-circle" style="vertical-align: -2px;"></span>Nonaktif
                            </span>
                            @endif
                        </div>

                        @if($karyawan->alamat)
                        <div class="col-12">
                            <p class="text-gray-500 mb-2 font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Alamat</p>
                            <p class="font-weight-bold text-gray-800 mb-0" style="font-size: 14px; line-height: 1.5;">{{ $karyawan->alamat }}</p>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Security Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white border-bottom p-4" style="border-bottom: 1px solid #E5E7EB !important;">
                    <h6 class="font-weight-bold text-gray-800 mb-0 d-flex align-items-center" style="font-size: 15px;">
                        <span class="iconify mr-2" data-icon="heroicons:lock-closed" style="color: #4DB6AC; font-size: 20px;"></span>
                        Keamanan Akun
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-gray-600 mb-3" style="font-size: 14px; line-height: 1.6;">Kelola password dan keamanan akun Anda untuk melindungi data pribadi.</p>
                    <a href="{{ route('karyawan.profile.edit') }}" class="btn btn-sm px-4 py-2 font-weight-bold" style="background-color: #4DB6AC; color: white; border-radius: 10px; font-size: 13px; border: none; transition: all 0.2s;">
                        <span class="iconify mr-1" data-icon="heroicons:key" style="vertical-align: -2px;"></span> Ubah Password
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column: Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white border-bottom p-4" style="border-bottom: 1px solid #E5E7EB !important;">
                    <h6 class="font-weight-bold text-gray-800 mb-0 d-flex align-items-center" style="font-size: 15px;">
                        <span class="iconify mr-2" data-icon="heroicons:lightning-bolt" style="color: #4DB6AC; font-size: 20px;"></span>
                        Aksi Cepat
                    </h6>
                </div>
                <div class="card-body p-3" style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('karyawan.profile.edit') }}" class="btn btn-sm w-100 py-2 font-weight-bold" style="background-color: #4DB6AC; color: white; border-radius: 10px; font-size: 13px; border: none;">
                        <span class="iconify mr-1" data-icon="heroicons:pencil-square" style="vertical-align: -2px;"></span> Edit Profil
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="w-100" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-sm w-100 py-2 font-weight-bold" style="border: 2px solid #DC2626; color: #DC2626; background-color: white; border-radius: 10px; font-size: 13px; transition: all 0.2s;">
                            <span class="iconify mr-1" data-icon="heroicons:arrow-left-on-rectangle" style="vertical-align: -2px;"></span> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection