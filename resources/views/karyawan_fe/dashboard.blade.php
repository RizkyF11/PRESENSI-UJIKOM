@extends('layouts.karyawan')

@section('content')

<div class="container-fluid pt-2">
    <!-- WELCOME & SHIFT CARD -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #4DB6AC 0%, #2C7A7B 100%); overflow: hidden; position: relative;">
        <!-- Background Decoration -->
        <div style="position: absolute; right: -20px; top: -20px; opacity: 0.1;">
            <span class="iconify" data-icon="heroicons:calendar-days" data-width="150" style="color: white;"></span>
        </div>

        <div class="card-body p-4 position-relative z-10 text-white">
            <h5 class="font-medium mb-1" style="font-size: 14px; color: rgba(255,255,255,0.8);">Selamat datang,</h5>
            <h4 class="font-weight-bold mb-4">{{ Auth::user()->name ?? Auth::user()->nama }}</h4>

            <div class="p-3" style="background-color: rgba(255,255,255,0.15); border-radius: 12px; backdrop-filter: blur(5px);">
                <p class="mb-1" style="font-size: 12px; color: rgba(255,255,255,0.9);">Shift hari ini:</p>
                @if($shiftHariIni)
                <h5 class="font-weight-bold mb-1 text-white" style="font-size: 16px;">{{ $shiftHariIni->nama_shift ?? 'Jadwal Reguler' }}</h5>
                <div class="d-flex align-items-center text-white">
                    <span class="iconify mr-1" data-icon="heroicons:clock"></span>
                    <span style="font-size: 13px;">{{ substr($shiftHariIni->jam_masuk, 0, 5) }} - {{ substr($shiftHariIni->jam_keluar, 0, 5) }} WIB</span>
                </div>
                @else
                <h5 class="font-weight-bold mb-0 text-white" style="font-size: 14px;">
                    <span class="iconify mr-1 align-middle" data-icon="heroicons:moon"></span> Libur / Tidak ada jadwal
                </h5>
                <p class="mb-0 mt-1" style="font-size: 11px; opacity: 0.8;">Selamat beristirahat!</p>
                @endif
            </div>
        </div>
    </div>

    <!-- STATUS ABSENSI HARI INI -->
    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 14px;">Status Absensi</h6>
        <span class="badge text-teal-600 bg-teal-50 px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 11px;">
            {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
        </span>
    </div>

    <div class="d-flex mb-4">
        <!-- Card Masuk -->
        <div class="card border-0 shadow-sm flex-fill mr-2" style="border-radius: 14px;">
            <div class="card-body p-3 text-center">
                <div class="icon-circle mb-2 mx-auto d-flex align-items-center justify-content-center bg-teal-50" style="width: 36px; height: 36px; border-radius: 50%;">
                    <span class="iconify text-teal-500" data-icon="heroicons:arrow-right-end-on-rectangle" data-width="20"></span>
                </div>
                <p class="text-gray-500 mb-1 font-medium" style="font-size: 11px;">Jam Masuk</p>
                <h5 class="font-weight-bold {{ $absensiHariIni && $absensiHariIni->jam_masuk ? 'text-teal-600' : 'text-gray-800' }} mb-0" style="font-size: 16px;">
                    {{ $absensiHariIni && $absensiHariIni->jam_masuk ? substr($absensiHariIni->jam_masuk, 0, 5) : '--:--' }}
                </h5>
            </div>
        </div>

        <!-- Card Keluar -->
        <div class="card border-0 shadow-sm flex-fill ml-2" style="border-radius: 14px;">
            <div class="card-body p-3 text-center">
                <div class="icon-circle mb-2 mx-auto d-flex align-items-center justify-content-center bg-red-50" style="width: 36px; height: 36px; border-radius: 50%;">
                    <span class="iconify text-red-500" data-icon="heroicons:arrow-left-start-on-rectangle" data-width="20"></span>
                </div>
                <p class="text-gray-500 mb-1 font-medium" style="font-size: 11px;">Jam Keluar</p>
                <h5 class="font-weight-bold {{ $absensiHariIni && $absensiHariIni->jam_keluar ? 'text-red-500' : 'text-gray-800' }} mb-0" style="font-size: 16px;">
                    {{ $absensiHariIni && $absensiHariIni->jam_keluar ? substr($absensiHariIni->jam_keluar, 0, 5) : '--:--' }}
                </h5>
            </div>
        </div>
    </div>

    <!-- MAIN MENU SEC -->
    <h6 class="font-weight-bold text-gray-800 mb-2 px-1" style="font-size: 14px;">Menu Cepat</h6>
    <div class="d-flex overflow-auto no-scrollbar mb-4 px-1" style="gap: 12px; padding-bottom: 5px;">

         <!-- Dompet Integritas -->
        <a href="{{ route('karyawan.dompet.index') }}" class="text-decoration-none flex-shrink-0" style="width: 100px;">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(145deg, #fff, #f8f9fa);">
                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="text-yellow-600 mb-2 flex items-center justify-center rounded-full" style="width:40px;height:40px; background: rgba(234, 179, 8, 0.15);">
                        <span class="iconify" data-icon="heroicons:wallet" data-width="22"></span>
                    </div>
                    <span class="text-gray-700 font-medium" style="font-size: 10px; line-height: 1.2;">Dompet Integritas</span>
                </div>
            </div>
        </a>

        <!-- Leaderboard -->
        <a href="{{ route('karyawan.leaderboard.index') }}" class="text-decoration-none flex-shrink-0" style="width: 100px;">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: linear-gradient(145deg, #fff, #f8f9fa);">
                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="text-orange-500 mb-2 flex items-center justify-center bg-orange-50 rounded-full" style="width:40px;height:40px;">
                        <span class="iconify animate-bounce" data-icon="heroicons:trophy" data-width="20"></span>
                    </div>
                    <span class="text-gray-700 font-medium" style="font-size: 10px; line-height: 1.2;">Leaderboard</span>
                </div>
            </div>
        </a>

        <!-- Jadwal Shift -->
        <a href="{{ route('karyawan.jadwal.index') }}" class="text-decoration-none flex-shrink-0" style="width: 100px;">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="text-blue-500 mb-2 flex items-center justify-center bg-blue-50 rounded-full" style="width:40px;height:40px;">
                        <span class="iconify" data-icon="heroicons:calendar" data-width="20"></span>
                    </div>
                    <span class="text-gray-700 font-medium" style="font-size: 10px; line-height: 1.2;">Jadwal Shift</span>
                </div>
            </div>
        </a>

        <!-- Riwayat Absensi -->
        <a href="{{ route('karyawan.riwayat.index') }}" class="text-decoration-none flex-shrink-0" style="width: 100px;">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="text-yellow-500 mb-2 flex items-center justify-center bg-yellow-50 rounded-full" style="width:40px;height:40px;">
                        <span class="iconify" data-icon="heroicons:document-text" data-width="20"></span>
                    </div>
                    <span class="text-gray-700 font-medium" style="font-size: 10px; line-height: 1.2;">Riwayat Absensi</span>
                </div>
            </div>
        </a>

        <!-- Pengajuan Izin -->
        <a href="{{ route('karyawan.izin.index') }}" class="text-decoration-none flex-shrink-0" style="width: 100px;">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="text-green-500 mb-2 flex items-center justify-center bg-green-50 rounded-full" style="width:40px;height:40px;">
                        <span class="iconify" data-icon="heroicons:envelope-open" data-width="20"></span>
                    </div>
                    <span class="text-gray-700 font-medium" style="font-size: 10px; line-height: 1.2;">Pengajuan Izin</span>
                </div>
            </div>
        </a>

        <!-- Pengajuan Cuti (Baru) -->
        <a href="{{ route('karyawan.cuti.index') }}" class="text-decoration-none flex-shrink-0" style="width: 100px;">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                    <div class="text-purple-500 mb-2 flex items-center justify-center bg-purple-50 rounded-full" style="width:40px;height:40px;">
                        <span class="iconify" data-icon="heroicons:paper-airplane" data-width="20"></span>
                    </div>
                    <span class="text-gray-700 font-medium" style="font-size: 10px; line-height: 1.2;">Pengajuan Cuti</span>
                </div>
            </div>
        </a>

       
    </div>

    <!-- STATISTIK BULANAN -->
    <h6 class="font-weight-bold text-gray-800 mb-2 px-1" style="font-size: 14px;">Statistik Bulan Ini</h6>
    <div class="row px-1">
        <div class="col-6 px-1 mb-2">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 bg-teal-50 flex items-center justify-center rounded-lg" style="width: 38px; height: 38px;">
                        <span class="iconify text-teal-500" data-icon="heroicons:check-badge" data-width="22"></span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0 font-medium" style="font-size: 10px;">Hadir</p>
                        <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $stats['hadir'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 px-1 mb-2">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 bg-yellow-50 flex items-center justify-center rounded-lg" style="width: 38px; height: 38px;">
                        <span class="iconify text-yellow-500" data-icon="heroicons:exclamation-triangle" data-width="22"></span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0 font-medium" style="font-size: 10px;">Terlambat</p>
                        <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $stats['terlambat'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 px-1 mb-2">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 bg-blue-50 flex items-center justify-center rounded-lg" style="width: 38px; height: 38px;">
                        <span class="iconify text-blue-500" data-icon="heroicons:information-circle" data-width="22"></span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0 font-medium" style="font-size: 10px;">Izin</p>
                        <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $stats['izin'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 px-1 mb-2">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 bg-blue-50 flex items-center justify-center rounded-lg" style="width: 38px; height: 38px;">
                        <span class="iconify text-blue-500" data-icon="heroicons:information-circle" data-width="22"></span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0 font-medium" style="font-size: 10px;">Cuti</p>
                        <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $stats['cuti'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 px-1 mb-2">
            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="mr-3 bg-red-50 flex items-center justify-center rounded-lg" style="width: 38px; height: 38px;">
                        <span class="iconify text-red-500" data-icon="heroicons:x-circle" data-width="22"></span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs mb-0 font-medium" style="font-size: 10px;">Alpha</p>
                        <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 15px;">{{ $stats['alpha'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes bounce {
  0%, 100% { transform: translateY(-15%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
  50% { transform: none; animation-timing-function: cubic-bezier(0,0,0.2,1); }
}
.animate-bounce {
  animation: bounce 2s infinite;
}
</style>
@endsection