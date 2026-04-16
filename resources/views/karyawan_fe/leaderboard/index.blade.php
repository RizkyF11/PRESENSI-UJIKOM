@extends('layouts.karyawan')

@section('content')
<div class="container-fluid pt-2 pb-5">
    
    <!-- HEADER -->
    <div class="d-flex align-items-center mb-4 px-1 mt-2">
        <a href="{{ route('karyawan.dashboard') }}" class="btn btn-sm btn-light bg-white border-0 shadow-sm mr-3 flex-shrink-0" style="border-radius: 12px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
            <span class="iconify text-gray-600" data-icon="heroicons:arrow-left" data-width="20"></span>
        </a>
        <div class="flex-grow-1">
            <h5 class="font-bold text-gray-800 mb-0 leading-tight">Leaderboard</h5>
            <p class="text-[11px] text-gray-500 mb-0">Peringkat Poin Integritas Karyawan</p>
        </div>
    </div>

    <!-- TOP 3 PODIUM -->
    <div class="d-flex justify-content-center align-items-end mb-4 px-2" style="gap: 15px;">
        @if(isset($leaderboard[1]))
            <!-- Rank 2 -->
            <div class="text-center" style="width: 30%;">
                <div class="mx-auto position-relative mb-2">
                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center font-bold text-gray-600 mx-auto" style="width: 65px; height: 65px; border: 3px solid #E2E8F0; background: #F8FAFC; font-size: 24px;">
                        {{ strtoupper(substr($leaderboard[1]->nama, 0, 1)) }}
                    </div>
                    <div class="position-absolute bg-gray-300 text-gray-700 font-bold rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; bottom: -5px; left: 50%; transform: translateX(-50%); font-size: 11px; border: 2px solid white;">2</div>
                </div>
                <h6 class="font-bold text-gray-800 text-[12px] mb-0 text-truncate">{{ explode(' ', $leaderboard[1]->nama)[0] }}</h6>
                <p class="text-gray-500 font-bold text-[10px]">{{ $leaderboard[1]->current_balance }} pt</p>
            </div>
        @endif

        @if(isset($leaderboard[0]))
            <!-- Rank 1 -->
            <div class="text-center position-relative" style="width: 35%; margin-bottom: 25px;">
                <span class="iconify position-absolute text-yellow-500 animate-bounce" data-icon="heroicons:sparkles-solid" style="top: -20px; right: 0px; z-index: 10;" data-width="24"></span>
                <div class="mx-auto position-relative mb-2">
                    <div class="rounded-circle shadow-md d-flex align-items-center justify-content-center font-bold text-yellow-700 mx-auto" style="width: 85px; height: 85px; border: 4px solid #FDE68A; background: #FFFBEB; font-size: 32px;">
                        {{ strtoupper(substr($leaderboard[0]->nama, 0, 1)) }}
                    </div>
                    <div class="position-absolute bg-yellow-400 text-yellow-900 font-bold rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; bottom: -8px; left: 50%; transform: translateX(-50%); font-size: 13px; border: 2px solid white;">1</div>
                </div>
                <h6 class="font-bold text-gray-800 text-[14px] mb-0 text-truncate">{{ explode(' ', $leaderboard[0]->nama)[0] }}</h6>
                <p class="text-yellow-600 font-bold text-[11px]">{{ $leaderboard[0]->current_balance }} pt</p>
            </div>
        @endif

        @if(isset($leaderboard[2]))
            <!-- Rank 3 -->
            <div class="text-center" style="width: 30%;">
                <div class="mx-auto position-relative mb-2">
                    <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center font-bold text-orange-700 mx-auto" style="width: 65px; height: 65px; border: 3px solid #FED7AA; background: #FFF7ED; font-size: 24px;">
                        {{ strtoupper(substr($leaderboard[2]->nama, 0, 1)) }}
                    </div>
                    <div class="position-absolute bg-orange-300 text-white font-bold rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 22px; height: 22px; bottom: -5px; left: 50%; transform: translateX(-50%); font-size: 11px; border: 2px solid white;">3</div>
                </div>
                <h6 class="font-bold text-gray-800 text-[12px] mb-0 text-truncate">{{ explode(' ', $leaderboard[2]->nama)[0] }}</h6>
                <p class="text-gray-500 font-bold text-[10px]">{{ $leaderboard[2]->current_balance }} pt</p>
            </div>
        @endif
    </div>

    <!-- LIST RANK 4 DAN SETERUSNYA -->
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-0">
            @php $rank = 4; @endphp
            @foreach($leaderboard->skip(3) as $user)
            <div class="d-flex align-items-center p-3 border-bottom border-gray-100 {{ $user->id === Auth::id() ? 'bg-teal-50' : '' }}">
                <div class="font-bold mr-3 {{ $user->id === Auth::id() ? 'text-teal-600' : 'text-gray-400' }}" style="width: 20px; text-align: center; font-size: 14px;">
                    {{ $rank++ }}
                </div>
                
                <div class="rounded-circle d-flex align-items-center justify-content-center font-bold text-gray-500 mr-3 shadow-sm border border-gray-200 bg-gray-50 flex-shrink-0" style="width: 42px; height: 42px; font-size: 16px;">
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                </div>
                
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="font-bold text-gray-800 mb-0 text-truncate" style="font-size: 13px;">
                        {{ $user->nama }}
                        @if($user->id === Auth::id())
                            <span class="badge bg-teal-100 text-teal-600 ml-1" style="font-size: 9px; padding: 2px 6px; border-radius: 8px;">Anda</span>
                        @endif
                    </h6>
                    <p class="text-gray-500 mb-0 text-truncate" style="font-size: 10px;">{{ $user->karyawan->jabatan ?? 'Karyawan' }}</p>
                </div>
                
                <div class="font-bold text-teal-600 text-right ml-2 flex-shrink-0" style="font-size: 13px;">
                    {{ $user->current_balance }} <span class="text-gray-400 font-medium" style="font-size: 10px;">pt</span>
                </div>
            </div>
            @endforeach
            
            @if($leaderboard->count() <= 3)
            <div class="p-4 text-center">
                <p class="text-gray-400 font-medium text-xs mb-0">Hanya ada {{ $leaderboard->count() }} karyawan di dalam peringkat.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@keyframes bounce {
  0%, 100% { transform: translateY(-25%); animation-timing-function: cubic-bezier(0.8,0,1,1); }
  50% { transform: none; animation-timing-function: cubic-bezier(0,0,0.2,1); }
}
.animate-bounce {
  animation: bounce 1.5s infinite;
}
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
@endsection
