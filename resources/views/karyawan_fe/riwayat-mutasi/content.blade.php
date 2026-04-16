<div class="px-1">
    <div class="flex items-center justify-between mb-3">
        <h6 class="font-bold text-gray-800 mb-0" style="font-size: 14px;">Riwayat Mutasi</h6>
    </div>

    @if($ledgers->isEmpty())
    <div class="card border-0 shadow-sm rounded-xl py-5 text-center bg-white">
        <div class="mb-3">
            <span class="iconify text-gray-300 mx-auto" data-icon="heroicons:document-text" data-width="48"></span>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-0">Belum ada riwayat mutasi poin.</p>
    </div>
    @else
    
    <div class="flex flex-col gap-3">
        @foreach($ledgers as $ledger)
        <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    
                    <!-- Icon Type -->
                    @if($ledger->transaction_type === 'EARN')
                    <div class="flex-shrink-0 bg-green-50 flex items-center justify-center rounded-xl mr-3" style="width: 46px; height: 46px;">
                        <span class="iconify text-green-500" data-icon="heroicons:arrow-down-left" data-width="24"></span>
                    </div>
                    @elseif($ledger->transaction_type === 'PENALTY')
                    <div class="flex-shrink-0 bg-red-50 flex items-center justify-center rounded-xl mr-3" style="width: 46px; height: 46px;">
                        <span class="iconify text-red-500" data-icon="heroicons:arrow-up-right" data-width="24"></span>
                    </div>
                    @else
                    <div class="flex-shrink-0 bg-blue-50 flex items-center justify-center rounded-xl mr-3" style="width: 46px; height: 46px;">
                        <span class="iconify text-blue-500" data-icon="heroicons:shopping-bag" data-width="24"></span>
                    </div>
                    @endif

                    <!-- Detail Section -->
                    <div class="flex-grow-1 overflow-hidden">
                        <h6 class="font-bold text-gray-800 text-[13px] mb-1 truncate">
                            {{ $ledger->description }}
                        </h6>
                        <p class="text-xs text-gray-500 mb-0 font-medium flex items-center">
                            <span class="iconify mr-1" data-icon="heroicons:calendar" data-width="12"></span>
                            {{ \Carbon\Carbon::parse($ledger->created_at)->translatedFormat('d M Y, H:i') }}
                        </p>
                    </div>

                    <!-- Amount Section -->
                    <div class="flex-shrink-0 text-right ml-2 text-sm">
                        @if($ledger->transaction_type === 'EARN')
                            <h6 class="font-bold text-green-500 mb-0">+{{ $ledger->amount }}</h6>
                        @else
                            <h6 class="font-bold text-red-500 mb-0">-{{ $ledger->amount }}</h6>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @endif
</div>
