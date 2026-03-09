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
            Jadwal Shift
        </h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid pt-4 pb-4 px-1">

    <!-- Filter Section (Header modern konsisten dengan view sebelumnya) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: linear-gradient(135deg, #4DB6AC 0%, #2C7A7B 100%);">
        <div class="card-body p-4 text-white">
            <h5 class="font-medium mb-1" style="font-size: 13px; color: rgba(255,255,255,0.8);">Kalender Shift</h5>
            <h4 class="font-weight-bold mb-3" style="font-size: 18px;">
                {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} {{ $tahun }}
            </h4>

            <form action="{{ route('karyawan.jadwal.index') }}" method="GET" class="d-flex align-items-center" style="gap: 8px;">
                <select name="bulan" class="form-control border-0 shadow-none text-gray-800 font-medium" style="border-radius: 8px; font-size: 12px; height: 36px;" onchange="this.form.submit()">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                        @endfor
                </select>

                <select name="tahun" class="form-control border-0 shadow-none text-gray-800 font-medium" style="border-radius: 8px; font-size: 12px; height: 36px;" onchange="this.form.submit()">
                    @for($i = date('Y') + 1; $i >= date('Y') - 3; $i--)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <!-- Calendar Layout -->
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px; overflow: hidden;">
        <div class="card-body p-0">
            <!-- Days of Week Header -->
            <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50 text-center">
                @php $days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; @endphp
                @foreach($days as $day)
                <div class="py-2 text-[10px] font-bold {{ $day == 'Min' ? 'text-red-400' : 'text-gray-500' }} uppercase">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Grid Dates Cells -->
            <div class="grid grid-cols-7" style="grid-auto-rows: minmax(70px, auto);">
                @php
                $padBefore = $startDayOfWeek; // 0 to 6
                $cells = [];
                // Pad empty cells before the 1st day of month
                for($i=0; $i<$padBefore; $i++) $cells[]=null;

                    // Insert actual days
                    foreach($calendar as $day) $cells[]=$day;

                    // Pad empty cells at the end to complete the grid (if not divisible by 7)
                    while(count($cells) % 7 !==0) $cells[]=null;
                    @endphp

                    @foreach($cells as $cell)
                    <div class="border-b border-r border-gray-100 p-1 relative flex flex-col justify-start {{ !$cell ? 'bg-gray-50/50' : '' }}" style="{{ $loop->iteration % 7 == 0 ? 'border-right: none;' : '' }}">
                    @if($cell)
                    @php
                    $isToday = clone $cell['date'];
                    $isToday = $isToday->isToday();
                    $hasShift = $cell['shift'] != null;
                    $isSunday = $cell['date']->dayOfWeek == 0;
                    @endphp

                    <!-- Date Number Text -->
                    <div class="d-flex justify-content-center mb-1 mt-1">
                        <span class="d-flex align-items-center justify-content-center rounded-full text-[12px] font-bold {{ $isToday ? 'bg-teal-500 text-white shadow-sm' : ($isSunday ? 'text-red-500' : 'text-gray-700') }}" style="width: 24px; height: 24px;">
                            {{ $cell['date']->format('d') }}
                        </span>
                    </div>

                    <!-- Shift Indicator Box -->
                    @if($hasShift)
                    <div class="bg-blue-50 border border-blue-100 rounded-md p-1 d-flex flex-column align-items-center text-center w-full shadow-sm mt-auto" style="min-height:35px;">
                        <span class="text-[9px] font-bold text-blue-700 leading-tight mb-[2px]" style="display: -webkit-box; -webkit-line-clamp: 1; line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; word-break: break-all;">
                            {{ $cell['shift']->nama_shift }}
                        </span>
                        <span class="text-[8.5px] font-bold text-gray-500 bg-white px-1 rounded-[4px] mt-0.5" style="border: 1px solid #DBEAFE">
                            {{ substr($cell['shift']->jam_masuk, 0, 5) }}
                        </span>
                    </div>
                    @endif
                    @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
</div>
@endsection