@extends('layouts.karyawan')

@section('header-left')
<div class="w-full flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('karyawan.dashboard') }}" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-bold" style="text-decoration: none;">
            <div class="bg-teal-50 w-8 h-8 rounded-full flex items-center justify-center">
                <span class="iconify" data-icon="heroicons:arrow-left" data-width="20"></span>
            </div>
        </a>
        <h1 class="text-[17px] font-bold text-gray-800 leading-tight mb-0">
            Riwayat Absensi
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid pt-4 pb-4 px-1">

    <!-- Filter Section (Optional UI) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: linear-gradient(135deg, #4DB6AC 0%, #2C7A7B 100%);">
        <div class="card-body p-4 text-white">
            <h5 class="font-medium mb-1" style="font-size: 13px; color: rgba(255,255,255,0.8);">Rekap Absensi</h5>
            <h4 class="font-weight-bold mb-3" style="font-size: 18px;">
                {{ $bulan == 'semua' ? 'Semua Bulan' : 'Bulan ' . \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} {{ $tahun == 'semua' ? '' : $tahun }}
            </h4>

            <form action="{{ route('karyawan.riwayat.index') }}" method="GET" class="d-flex align-items-center" style="gap: 8px;">
                <select name="bulan" class="form-control border-0 shadow-none text-gray-800 font-medium" style="border-radius: 8px; font-size: 12px; height: 36px;" onchange="this.form.submit()">
                    <option value="semua" {{ $bulan == 'semua' ? 'selected' : '' }}>Semua Bulan</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                        @endfor
                </select>

                <select name="tahun" class="form-control border-0 shadow-none text-gray-800 font-medium" style="border-radius: 8px; font-size: 12px; height: 36px;" onchange="this.form.submit()">
                    <option value="semua" {{ $tahun == 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                    @for($i = date('Y'); $i >= date('Y') - 6; $i--)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    @if($riwayatAbsensi->isEmpty())
    <div class="d-flex flex-column align-items-center justify-content-center text-center mt-5" style="min-height: 40vh;">
        <div class="icon-circle mb-3 flex items-center justify-center bg-gray-100 rounded-full" style="width: 80px; height: 80px;">
            <span class="iconify text-gray-400" data-icon="heroicons:document-text" data-width="40"></span>
        </div>
        <h4 class="text-gray-800 font-bold mb-1" style="font-size: 16px;">Belum ada data</h4>
        <p class="text-gray-500 font-medium" style="font-size: 13px;">Riwayat absensi pada periode ini kosong.</p>
    </div>
    @else
    <div class="d-flex flex-column" style="gap: 12px;">
        @foreach($riwayatAbsensi as $absen)
        @php
        $status = 'Alpha';

        // Cek apakah ada izin pada tanggal tersebut
        $isIzin = $izin->filter(function($i) use ($absen) {
        return $i->tanggal_mulai <= $absen->tanggal && $i->tanggal_selesai >= $absen->tanggal;
            })->count() > 0;

            // Cek apakah ada cuti pada tanggal tersebut
            $isCuti = $cuti->filter(function($c) use ($absen) {
            return $c->tanggal_mulai <= $absen->tanggal && $c->tanggal_selesai >= $absen->tanggal;
                })->count() > 0;

                if ($isIzin) {
                $status = 'Izin';
                } elseif ($isCuti) {
                $status = 'Cuti';
                } elseif ($absen->status_masuk === 'terlambat') {
                $status = 'Terlambat';
                } elseif (!is_null($absen->jam_masuk)) {
                $status = 'Hadir';
                } elseif ($absen->status_masuk === 'izin') { // Fallback kalo sudah terlanjur di db
                $status = 'Izin';
                } elseif ($absen->status_masuk === 'cuti') {
                $status = 'Cuti';
                }
                @endphp

                <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-b border-gray-100">
                            <div class="d-flex align-items-center">
                                <div class="bg-teal-50 mr-2 flex items-center justify-center rounded-lg" style="width: 32px; height: 32px;">
                                    <span class="iconify text-teal-600" data-icon="heroicons:calendar-days" data-width="18"></span>
                                </div>
                                <span class="font-weight-bold text-gray-800" style="font-size: 13px;">
                                    {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                            @php
                            $badgeClass = 'bg-red-50 text-red-600'; // Default: Alpha/Lainnya
                            if ($status == 'Hadir') {
                            $badgeClass = 'bg-teal-50 text-teal-600';
                            } elseif ($status == 'Terlambat') {
                            $badgeClass = 'bg-yellow-50 text-yellow-600';
                            } elseif ($status == 'Izin') {
                            $badgeClass = 'bg-blue-50 text-blue-600';
                            } elseif ($status == 'Cuti') {
                            $badgeClass = 'bg-purple-50 text-purple-600';
                            }
                            @endphp
                            <span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold" style="border-radius: 6px; font-size: 11px;">
                                {{ $status }}
                            </span>
                        </div>

                        <div class="d-flex">
                            <div class="flex-fill d-flex align-items-center px-1">
                                <div class="mr-2">
                                    <span class="iconify {{ $absen->jam_masuk ? 'text-teal-500' : 'text-gray-400' }}" data-icon="heroicons:arrow-right-end-on-rectangle" data-width="18"></span>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-0 font-medium" style="font-size: 10px;">Jam Masuk</p>
                                    <h6 class="font-weight-bold {{ $absen->jam_masuk ? 'text-teal-600' : 'text-gray-800' }} mb-0" style="font-size: 14px;">
                                        {{ $absen->jam_masuk ? substr($absen->jam_masuk, 0, 5) : '--:--' }}
                                    </h6>
                                </div>
                            </div>

                            <div style="width: 1px; background-color: #E5E7EB; margin: 0 5px;"></div>

                            <div class="flex-fill d-flex align-items-center justify-content-center px-1">
                                <div class="mr-2">
                                    <span class="iconify {{ $absen->jam_keluar ? 'text-red-500' : 'text-gray-400' }}" data-icon="heroicons:arrow-left-start-on-rectangle" data-width="18"></span>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-0 font-medium" style="font-size: 10px;">Jam Keluar</p>
                                    <h6 class="font-weight-bold {{ $absen->jam_keluar ? 'text-red-500' : 'text-gray-800' }} mb-0" style="font-size: 14px;">
                                        {{ $absen->jam_keluar ? substr($absen->jam_keluar, 0, 5) : '--:--' }}
                                    </h6>
                                </div>
                            </div>

                            <!-- Status Keluar Badge (Optional) -->
                            @if($absen->status_keluar && $absen->status_keluar != 'pulang')
                            <div class="d-flex align-items-center justify-content-end" style="width: 60px;">
                                <span class="badge {{ $absen->status_keluar == 'pulang_cepat' ? 'bg-orange-50 text-orange-600' : 'bg-gray-100 text-gray-600' }}" style="font-size: 9px; padding: 4px; border-radius: 4px;">
                                    {{ str_replace('_', ' ', ucfirst($absen->status_keluar)) }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
    </div>
    @endif
</div>
@endsection