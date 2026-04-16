@extends('layouts.karyawan')

@section('header-left')
<div class="flex items-center gap-3">
    <a href="{{ route('karyawan.dashboard') }}" class="text-gray-800 text-decoration-none">
        <span class="iconify" data-icon="heroicons:arrow-left" data-width="24"></span>
    </a>
    <h1 class="text-[17px] font-bold text-gray-800 leading-tight mb-0">
        Dompet Integritas
    </h1>
</div>
@endsection

@section('content')
<div class="container-fluid px-1">
    
    <!-- Peringatan Alert -->
    @if(session('error'))
    <div class="text-white bg-red-500 p-3 rounded-xl mb-3 flex items-center shadow-sm">
        <span class="iconify mr-2" data-icon="heroicons:exclamation-circle" data-width="24"></span>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif
    @if(session('success'))
    <div class="text-white bg-teal-500 p-3 rounded-xl mb-3 flex items-center shadow-sm">
        <span class="iconify mr-2" data-icon="heroicons:check-circle" data-width="24"></span>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- HERO SECTION E-WALLET -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: linear-gradient(135deg, #111827 0%, #374151 100%); overflow: hidden; position: relative;">
        <!-- Background Decoration -->
        <div style="position: absolute; right: -30px; top: -30px; opacity: 0.1;">
            <span class="iconify" data-icon="heroicons:wallet" data-width="180" style="color: white;"></span>
        </div>
        
        <div class="card-body p-4 position-relative z-10 text-white">
            <div class="flex justify-between items-center mb-1">
                <p class="mb-0 text-gray-300 font-medium text-xs uppercase tracking-wider">Saldo Poinku</p>
                <span class="badge bg-yellow-500 text-gray-900 border-0 font-bold px-3 py-1 shadow-sm" style="border-radius: 12px; font-size: 10px;">
                    @if($balance > 50)
                        Level: Disiplin Elite
                    @elseif($balance > 20)
                        Level: Rajin
                    @else
                        Level: Pemula
                    @endif
                </span>
            </div>
            
            <div class="flex items-center gap-2 mb-4">
                <span class="iconify text-yellow-400" data-icon="heroicons:star-solid" data-width="32"></span>
                <h2 class="font-bold mb-0" style="font-size: 38px; line-height: 1; letter-spacing: -1px;">{{ number_format($balance, 0, ',', '.') }}</h2>
            </div>
            
            <div class="flex justify-between gap-3 mt-4">
                <button onclick="switchTab('marketplace')" class="btn flex-1 flex flex-col items-center justify-center p-2 rounded-xl border-0 shadow-sm transition focus:outline-none focus:ring-0 outline-none" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
                    <span class="iconify text-yellow-400 mb-1" data-icon="heroicons:shopping-cart" data-width="22"></span>
                    <span class="text-[11px] text-white font-medium">Tukar</span>
                </button>
                <button onclick="switchTab('inventory')" class="btn flex-1 flex flex-col items-center justify-center p-2 rounded-xl border-0 shadow-sm transition focus:outline-none focus:ring-0 outline-none" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
                    <span class="iconify text-teal-300 mb-1" data-icon="heroicons:ticket" data-width="22"></span>
                    <span class="text-[11px] text-white font-medium">Kupon</span>
                </button>
                <button onclick="switchTab('riwayat')" class="btn flex-1 flex flex-col items-center justify-center p-2 rounded-xl border-0 shadow-sm transition focus:outline-none focus:ring-0 outline-none" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(5px);">
                    <span class="iconify text-blue-300 mb-1" data-icon="heroicons:clock-solid" data-width="22"></span>
                    <span class="text-[11px] text-white font-medium">Mutasi</span>
                </button>
            </div>
        </div>
    </div>

    <!-- TABS NAVIGATION STRIP -->
    <div class="flex border-b border-gray-200 mb-4 sticky rounded-xl bg-white shadow-sm overflow-hidden" style="top: 80px; z-index: 20;">
        <button id="tab-riwayat" onclick="switchTab('riwayat')" class="flex-1 py-3 text-[13px] font-bold border-b-[3px] text-teal-600 border-teal-500 bg-teal-50 transition-colors focus:outline-none focus:ring-0 outline-none">Mutasi</button>
        <button id="tab-marketplace" onclick="switchTab('marketplace')" class="flex-1 py-3 text-[13px] font-bold border-b-[3px] text-gray-400 border-transparent bg-white transition-colors focus:outline-none focus:ring-0 outline-none">Marketplace</button>
        <button id="tab-inventory" onclick="switchTab('inventory')" class="flex-1 py-3 text-[13px] font-bold border-b-[3px] text-gray-400 border-transparent bg-white transition-colors focus:outline-none focus:ring-0 outline-none">Inventory</button>
    </div>

    <!-- TAB CONTENTS -->
    <div id="content-riwayat" class="tab-content block pb-4">
        @include('karyawan_fe.riwayat-mutasi.content')
    </div>
    
    <div id="content-marketplace" class="tab-content hidden pb-4">
        @include('karyawan_fe.marketplace.content')
    </div>
    
    <div id="content-inventory" class="tab-content hidden pb-4">
        @include('karyawan_fe.inventory.content')
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.remove('block');
            el.classList.add('hidden');
        });
        
        // Reset all tabs
        document.querySelectorAll('[id^="tab-"]').forEach(el => {
            el.classList.remove('text-teal-600', 'border-teal-500', 'bg-teal-50');
            el.classList.add('text-gray-400', 'border-transparent', 'bg-white');
        });
        
        // Show selected content
        document.getElementById('content-' + tabName).classList.remove('hidden');
        document.getElementById('content-' + tabName).classList.add('block');
        
        // Highlight selected tab
        document.getElementById('tab-' + tabName).classList.remove('text-gray-400', 'border-transparent', 'bg-white');
        document.getElementById('tab-' + tabName).classList.add('text-teal-600', 'border-teal-500', 'bg-teal-50');
    }
</script>
@endsection
