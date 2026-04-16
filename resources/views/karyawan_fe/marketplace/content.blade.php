<div class="px-1">
    <div class="flex items-center justify-between mb-3">
        <h6 class="font-bold text-gray-800 mb-0" style="font-size: 14px;">Marketplace Privilege</h6>
    </div>

    @if($items->isEmpty())
    <div class="card border-0 shadow-sm rounded-xl py-5 text-center bg-white">
        <div class="mb-3">
            <span class="iconify text-gray-300 mx-auto" data-icon="heroicons:shopping-bag" data-width="48"></span>
        </div>
        <p class="text-sm font-medium text-gray-500 mb-0">Marketplace sedang kosong.</p>
    </div>
    @else
    
    <div class="grid grid-cols-2 gap-3">
        @foreach($items as $item)
        <div class="card border-0 shadow-sm bg-white h-100 flex flex-col relative" style="border-radius: 16px; overflow: hidden;">
            
            <!-- Harga Badge -->
            <div class="absolute top-2 right-2 bg-yellow-100 text-yellow-700 font-bold px-2 py-1 rounded-lg text-[10px] flex items-center shadow-sm z-10 border border-yellow-200">
                <span class="iconify mr-1" data-icon="heroicons:star-solid"></span>
                {{ $item->point_cost }}
            </div>

            <!-- Ilustrasi Bagian Atas -->
            <div class="bg-gray-50 flex items-center justify-center p-4 border-b border-gray-100 relative overflow-hidden" style="height: 100px;">
                <!-- Decorative Circle -->
                <div class="absolute bg-white rounded-full opacity-50" style="width: 120px; height: 120px; top: -20px; right: -40px;"></div>
                
                @if(stripos($item->item_name, 'telat') !== false || stripos($item->item_name, 'terlambat') !== false)
                    <span class="iconify text-teal-400 relative z-10" data-icon="heroicons:clock" data-width="48"></span>
                @elseif(stripos($item->item_name, 'izin') !== false || stripos($item->item_name, 'cuti') !== false)
                    <span class="iconify text-blue-400 relative z-10" data-icon="heroicons:paper-airplane" data-width="48"></span>
                @elseif(stripos($item->item_name, 'wfh') !== false)
                    <span class="iconify text-purple-400 relative z-10" data-icon="heroicons:home-modern" data-width="48"></span>
                @else
                    <span class="iconify text-orange-400 relative z-10" data-icon="heroicons:gift" data-width="48"></span>
                @endif
            </div>

            <div class="card-body p-3 flex flex-col justify-between flex-grow-1">
                <div>
                    <h6 class="font-bold text-gray-800 text-[13px] mb-2 leading-tight">
                        {{ $item->item_name }}
                    </h6>
                    <p class="text-[10px] text-gray-500 mb-2" style="line-height: 1.4;">
                        {{ \Illuminate\Support\Str::limit($item->description ?? 'Gunakan poinmu untuk mendapatkan token ini.', 50) }}
                    </p>
                    
                    <!-- Indikator Stok -->
                    <div class="flex items-center gap-1 text-[10px] font-bold mt-1 {{ $item->stock_limit !== null && $item->stock_limit <= 0 ? 'text-red-500' : 'text-teal-600' }}">
                        <span class="iconify" data-icon="heroicons:archive-box" data-width="12"></span>
                        <span>Stok: {{ $item->stock_limit === null ? 'Unlimited' : $item->stock_limit }}</span>
                    </div>
                </div>

                <form action="{{ route('karyawan.dompet.redeem', $item->id) }}" method="POST" class="mt-3">
                    @csrf
                    @php
                        $isOwned = $inventory->where('item_id', $item->id)->where('status', 'AVAILABLE')->isNotEmpty();
                        $isOutOfStock = $item->stock_limit !== null && $item->stock_limit <= 0;
                        $canBuy = !$isOwned && !$isOutOfStock && $balance >= $item->point_cost;
                    @endphp

                    <button type="submit" class="btn w-full font-bold text-[11px] py-2 rounded-xl transition shadow-sm {{ $canBuy ? 'bg-teal-500 text-white hover:bg-teal-600' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}" {{ !$canBuy ? 'disabled' : '' }}>
                        @if($isOwned)
                            Sudah Dimiliki
                        @elseif($isOutOfStock)
                            Stok Habis
                        @elseif($balance < $item->point_cost)
                            Poin Kurang
                        @else
                            Tukar Poin
                        @endif
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    @endif
</div>
