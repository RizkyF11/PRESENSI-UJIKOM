@extends('layouts.karyawan')

@section('content')

<div class="container-fluid pt-2 pb-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('karyawan.profile.show') }}" class="btn btn-outline-secondary btn-sm px-3 py-2" style="border-radius: 10px; font-weight: 600; font-size: 13px; color: #6B7280; border-color: #D1D5DB;">
            <span class="iconify mr-1" data-icon="heroicons:arrow-left" style="vertical-align: -2px;"></span> Kembali
        </a>
    </div>

    <!-- Edit Profile Header -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #4DB6AC 0%, #2C7A7B 100%); overflow: hidden; position: relative;">
        <!-- Background Decoration -->
        <div style="position: absolute; right: -50px; top: -50px; opacity: 0.08;">
            <span class="iconify" data-icon="heroicons:pencil-square" data-width="250" style="color: white;"></span>
        </div>

        <div class="card-body p-4 position-relative z-10 text-white">
            <h5 class="font-weight-bold mb-2" style="font-size: 18px;">Edit Profil Anda</h5>
            <p class="mb-0" style="font-size: 13px; opacity: 0.9;">Perbarui email dan password dengan aman</p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #FEE2E2; color: #DC2626;" role="alert">
        <div class="d-flex align-items-start">
            <span class="iconify mr-2 mt-1" data-icon="heroicons:exclamation-circle" style="font-size: 20px;"></span>
            <div class="flex-grow-1">
                <strong class="d-block mb-2">Terjadi kesalahan!</strong>
                <ul class="mb-0 pl-3" style="list-style: none;">
                    @foreach($errors->all() as $error)
                    <li style="font-size: 13px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: #DC2626;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <!-- Edit Form Card -->
            <form action="{{ route('karyawan.profile.update') }}" method="POST" class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                @csrf
                @method('PUT')

                <div class="card-body p-4">
                    <!-- Avatar Display -->
                    <div class="text-center mb-4 pb-4 border-bottom" style="border-bottom: 1px solid #E5E7EB !important;">
                        <div class="d-inline-flex align-items-center justify-content-center" style="width: 90px; height: 90px; border-radius: 50%; background-color: #4DB6AC; font-size: 40px; font-weight: bold; color: white; box-shadow: 0 4px 12px rgba(77, 182, 172, 0.3);">
                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                        </div>
                        <p class="mt-3 text-gray-600 mb-0" style="font-size: 13px;">Avatar berdasarkan inisial nama Anda</p>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="form-label font-weight-bold text-gray-800 mb-2" style="font-size: 13px;">
                            <span class="iconify mr-1" data-icon="heroicons:envelope" style="vertical-align: -2px;"></span> Email
                        </label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="nama@example.com"
                            style="border-radius: 10px; border: 1px solid #E5E7EB; padding: 10px 14px; font-size: 14px;">
                        @error('email')
                        <div class="invalid-feedback" style="display: block; font-size: 12px;">
                            {{ $message }}
                        </div>
                        @enderror
                        <small class="form-text text-muted mt-1" style="font-size: 12px;">
                            Email ini akan tersimpan di database dan visible di admin
                        </small>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="password" class="form-label font-weight-bold text-gray-800 mb-2" style="font-size: 13px;">
                            <span class="iconify mr-1" data-icon="heroicons:lock-closed" style="vertical-align: -2px;"></span> Password Baru
                        </label>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Biarkan kosong jika tidak ingin mengubah"
                            style="border-radius: 10px; border: 1px solid #E5E7EB; padding: 10px 14px; font-size: 14px;">
                        @error('password')
                        <div class="invalid-feedback" style="display: block; font-size: 12px;">
                            {{ $message }}
                        </div>
                        @enderror
                        <small class="form-text text-muted mt-1" style="font-size: 12px;">
                            Minimal 6 karakter. Kosongkan jika tidak ingin ganti password.
                        </small>
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label font-weight-bold text-gray-800 mb-2" style="font-size: 13px;">
                            <span class="iconify mr-1" data-icon="heroicons:lock-closed" style="vertical-align: -2px;"></span> Konfirmasi Password
                        </label>
                        <input
                            type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Masukkan ulang password"
                            style="border-radius: 10px; border: 1px solid #E5E7EB; padding: 10px 14px; font-size: 14px;">
                        @error('password_confirmation')
                        <div class="invalid-feedback" style="display: block; font-size: 12px;">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Info Box -->
                    <div class="p-3 mb-4" style="border-radius: 10px; background-color: #DCFCE7; border-left: 4px solid #15803D;">
                        <div class="d-flex">
                            <span class="iconify mr-2" data-icon="heroicons:information-circle" style="color: #15803D; font-size: 20px;"></span>
                            <div style="font-size: 13px; color: #15803D; line-height: 1.5;">
                                <strong>Informasi:</strong> Perubahan akan tersimpan langsung ke database dan otomatis terlihat di profil admin.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="card-footer bg-white border-top p-4" style="border-top: 1px solid #E5E7EB !important;">
                    <div class="d-flex gap-2">
                        <a href="{{ route('karyawan.profile.show') }}" class="btn btn-sm px-4 py-2 font-weight-bold" style="background-color: #E5E7EB; color: #4B5563; border-radius: 10px; font-size: 13px; border: none; transition: all 0.2s;">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-sm px-4 py-2 font-weight-bold" style="background-color: #4DB6AC; color: white; border-radius: 10px; font-size: 13px; border: none; transition: all 0.2s;">
                            <span class="iconify mr-1" data-icon="heroicons:check-circle" style="vertical-align: -2px;"></span> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Column - Security Tips -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white border-bottom p-4" style="border-bottom: 1px solid #E5E7EB !important;">
                    <h6 class="font-weight-bold text-gray-800 mb-0 d-flex align-items-center" style="font-size: 15px;">
                        <span class="iconify mr-2" data-icon="heroicons:shield-exclamation" style="color: #4DB6AC; font-size: 20px;"></span>
                        Tips Keamanan
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <p class="text-gray-700 font-weight-bold mb-2" style="font-size: 13px;">Password yang Kuat:</p>
                        <ul class="pl-3 mb-0" style="font-size: 12px; list-style: none; line-height: 1.8; color: #6B7280;">
                            <li class="mb-1"><span class="iconify mr-2" data-icon="heroicons:check-circle" style="color: #15803D; vertical-align: -2px;"></span> Minimal 6 karakter</li>
                            <li class="mb-1"><span class="iconify mr-2" data-icon="heroicons:check-circle" style="color: #15803D; vertical-align: -2px;"></span> Kombinasi huruf besar & kecil</li>
                            <li class="mb-1"><span class="iconify mr-2" data-icon="heroicons:check-circle" style="color: #15803D; vertical-align: -2px;"></span> Sertakan angka & simbol</li>
                            <li><span class="iconify mr-2" data-icon="heroicons:check-circle" style="color: #15803D; vertical-align: -2px;"></span> Jangan bagikan password</li>
                        </ul>
                    </div>

                    <hr style="border-color: #E5E7EB; margin: 16px 0;">

                    <p class="text-gray-700 font-weight-bold mb-2" style="font-size: 13px;">Perlindungan Akun:</p>
                    <p class="mb-0" style="font-size: 12px; color: #6B7280; line-height: 1.6;">
                        Pastikan email Anda masih aktif dan password tidak mudah ditebak. Jangan gunakan password yang sama di aplikasi lain.
                    </p>
                </div>
            </div>

            <!-- Additional Info Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden; background-color: #F3F4F6;">
                <div class="card-body p-4">
                    <h6 class="font-weight-bold text-gray-800 mb-3 d-flex align-items-center" style="font-size: 14px;">
                        <span class="iconify mr-2" data-icon="heroicons:question-mark-circle" style="color: #6B7280; font-size: 18px;"></span>
                        Butuh Bantuan?
                    </h6>
                    <p class="mb-0" style="font-size: 12px; color: #6B7280; line-height: 1.6;">
                        Jika mengalami kesulitan, hubungi admin atau lihat dokumentasi sistem.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control {
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: #4DB6AC !important;
        box-shadow: 0 0 0 0.2rem rgba(77, 182, 172, 0.25) !important;
    }

    .form-control.is-invalid:focus {
        border-color: #DC2626 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 38, 38, 0.25) !important;
    }

    .btn {
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>

@endsection