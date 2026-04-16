<div class="px-1">
    <div class="flex items-center justify-between mb-3">
        <h6 class="font-bold text-gray-800 mb-0" style="font-size: 14px;">My Inventory Kupon</h6>
    </div>

    @if($inventory->isEmpty())
    <div class="card border-0 shadow-sm rounded-xl py-5 text-center bg-white">
        <div class="mb-3">
            <span class="iconify text-gray-300 mx-auto" data-icon="heroicons:ticket" data-width="48"></span>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-0">Kamu belum memiliki kupon.</p>
        <p class="text-[11px] text-gray-400 mt-1">Tukarkan poinmu di Marketplace.</p>
    </div>
    @else
    
    <div class="flex flex-col gap-3">
        @foreach($inventory as $inv)
        @php
            $borderColor = $inv->status === 'AVAILABLE' ? '#4DB6AC' : ($inv->status === 'USED' ? '#9CA3AF' : '#F87171');
        @endphp
        <div class="card border-0 shadow-sm bg-white relative overflow-hidden" style="border-radius: 14px; border-left: 5px solid {{ $borderColor }};">
            
            <div class="card-body p-3">
                <div class="flex justify-between items-start">
                    
                    <div class="flex items-start gap-3">
                        <div class="bg-gray-50 flex-shrink-0 flex items-center justify-center rounded-lg" style="width: 40px; height: 40px;">
                            @if($inv->status === 'AVAILABLE')
                                <span class="iconify text-teal-500" data-icon="heroicons:ticket" data-width="24"></span>
                            @elseif($inv->status === 'USED')
                                <span class="iconify text-gray-400" data-icon="heroicons:check-circle" data-width="24"></span>
                            @else
                                <span class="iconify text-red-400" data-icon="heroicons:x-circle" data-width="24"></span>
                            @endif
                        </div>
                        
                        <div>
                            <h6 class="font-bold text-gray-800 text-[13px] mb-1">
                                {{ $inv->item->item_name ?? 'Kupon Digital' }}
                            </h6>
                            <p class="text-[10px] text-gray-500 mb-0 font-medium">
                                Diperoleh: {{ \Carbon\Carbon::parse($inv->created_at)->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex-shrink-0">
                        @if($inv->status === 'AVAILABLE')
                            <span class="badge bg-teal-50 text-teal-600 border border-teal-200 px-2 py-1 text-[10px] rounded-lg font-bold">Aktif</span>
                        @elseif($inv->status === 'USED')
                            <span class="badge bg-gray-100 text-gray-500 border border-gray-200 px-2 py-1 text-[10px] rounded-lg font-bold">Terpakai</span>
                        @else
                            <span class="badge bg-red-50 text-red-500 border border-red-200 px-2 py-1 text-[10px] rounded-lg font-bold">Expired</span>
                        @endif
                    </div>
                </div>

                @if($inv->status === 'AVAILABLE')
                    <div class="mt-3 pt-2 border-t border-gray-100">
                        <p class="text-[10px] text-blue-500 font-medium mb-0 flex items-center">
                            <span class="iconify mr-1" data-icon="heroicons:information-circle"></span>
                            Kupon ini akan teraplikasi otomatis saat darurat.
                        </p>
                    </div>
                @endif
                
                @if($inv->status === 'USED' && $inv->used_at_absensi_id)
                    <div class="mt-3 pt-2 border-t border-gray-100">
                        <p class="text-[10px] text-gray-500 font-medium mb-0 flex items-center">
                            <span class="iconify mr-1 text-gray-400" data-icon="heroicons:clock"></span>
                            Telah digunakan pada id absensi #{{ $inv->used_at_absensi_id }}
                        </p>
                    </div>
                @endif
                
            </div>
            
            <!-- Perforasi (Efek Robekan Kertas Kupon) -->
            <div class="absolute" style="top: 0; left: 0; bottom: 0; width: 8px; background-image: radial-gradient(#F5F7FA 3px, transparent 4px); background-size: 8px 12px; background-position: -4px 0; background-repeat: repeat-y; opacity: 0.5;"></div>
        </div>
        @endforeach
    </div>

    @endif
</div>
