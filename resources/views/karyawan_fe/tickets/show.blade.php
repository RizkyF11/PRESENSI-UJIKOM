@extends('layouts.karyawan')

@section('header-left')
<div class="flex items-center gap-3">
    <a href="{{ route('karyawan.tickets.index') }}" class="text-gray-800 hover:text-teal-600 transition-colors">
        <span class="iconify" data-icon="heroicons:arrow-left-solid" data-width="24"></span>
    </a>
    <h1 class="text-[16px] font-bold text-gray-800 leading-tight mb-0">
        Detail Tiket #{{ $ticket->id }}
    </h1>
</div>
@endsection

@section('content')
<!-- Header Detail Tiket -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
    <div class="flex justify-between items-start mb-3 gap-3">
        <h2 class="font-bold text-gray-800 text-sm leading-snug">{{ $ticket->subject }}</h2>
        <div class="flex flex-col gap-1.5 items-end shrink-0">
            @if($ticket->status === 'Open')
                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Open</span>
            @elseif($ticket->status === 'In-Progress')
                <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full">In-Progress</span>
            @elseif($ticket->status === 'Closed')
                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full">Closed</span>
            @endif
        </div>
    </div>
    
    <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-xs">
        <div>
            <p class="text-gray-400 font-medium mb-0.5">Kategori</p>
            <p class="text-gray-700 font-bold bg-gray-50 inline-block px-2 py-0.5 rounded">{{ $ticket->category }}</p>
        </div>
        <div>
            <p class="text-gray-400 font-medium mb-0.5">Prioritas</p>
            <p>
                @if($ticket->priority === 'High')
                    <span class="text-red-700 bg-red-50 font-bold inline-block px-2 py-0.5 rounded">High</span>
                @elseif($ticket->priority === 'Mid')
                    <span class="text-yellow-700 bg-yellow-50 font-bold inline-block px-2 py-0.5 rounded">Mid</span>
                @else
                    <span class="text-gray-700 bg-gray-100 font-bold inline-block px-2 py-0.5 rounded">Low</span>
                @endif
            </p>
        </div>
        <div>
            <p class="text-gray-400 font-medium mb-0.5">Tanggal Buat</p>
            <p class="text-gray-700 font-bold">{{ $ticket->created_at->format('d M Y H:i') }}</p>
        </div>
        <div>
            <p class="text-gray-400 font-medium mb-0.5">Operator</p>
            <p class="text-gray-700 font-bold">{{ $ticket->operator ? $ticket->operator->name : 'Belum Ada' }}</p>
        </div>
    </div>
    
    <hr class="border-gray-100 my-3">
    
    <div>
        <p class="text-gray-400 font-medium text-xs mb-1">Deskripsi Kendala:</p>
        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 text-xs text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->description }}</div>
    </div>
</div>

<!-- Thread Percakapan -->
<div class="mb-4">
    <div class="flex items-center gap-1.5 mb-3 ml-1">
        <span class="iconify text-gray-400" data-icon="heroicons:chat-bubble-left-right" data-width="16"></span>
        <h3 class="text-xs font-bold text-gray-700">Thread Balasan</h3>
    </div>
    
    <div class="flex flex-col gap-3">
        @forelse($responses as $resp)
            @php 
                // Cek role menggunakan helper di Models\TicketsResponse
                $isKaryawan = !$resp->isFromOperator();
            @endphp
            
            <div class="flex w-full {{ $isKaryawan ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[85%] rounded-2xl px-3 py-2 shadow-sm text-sm {{ $isKaryawan ? 'bg-teal-50 border border-teal-100 text-teal-900 rounded-tr-none' : 'bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-tl-none' }}">
                    <div class="flex justify-between items-end gap-3 mb-1.5">
                        <span class="font-bold text-[10px] {{ $isKaryawan ? 'text-teal-700' : 'text-indigo-600' }}">{{ $resp->responder->nama ?? ($isKaryawan ? 'Anda' : 'Admin/Operator') }}</span>
                        <span class="text-[9px] {{ $isKaryawan ? 'text-teal-500' : 'text-indigo-400' }}">{{ $resp->created_at->format('d/m H:i') }}</span>
                    </div>
                    <p class="text-xs leading-relaxed break-words whitespace-pre-wrap">{{ $resp->message }}</p>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-xl border border-dashed border-gray-200">
                <span class="iconify text-gray-300 mx-auto mb-1" data-icon="heroicons:chat-bubble-oval-left" data-width="24"></span>
                <p class="text-[11px] text-gray-400 font-medium">Belum ada balasan percakapan.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Form Reply (Hanya jika belum closed) -->
@if($ticket->status !== 'Closed')
    <div class="bg-white p-3.5 rounded-xl shadow-sm border border-gray-100 mb-4">
        <form action="{{ route('karyawan.tickets.reply', $ticket->id) }}" method="POST">
            @csrf
            <label class="block text-[11px] font-bold text-gray-700 mb-1.5">Balas Tiket</label>
            <textarea name="message" rows="2" class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all resize-none @error('message') border-red-500 @enderror" placeholder="Ketik balasan Anda..." required></textarea>
            @error('message')
                <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
            @enderror
            <div class="mt-2.5 flex justify-end">
                <button type="submit" class="bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs py-1.5 px-4 rounded-lg shadow-sm flex items-center gap-1.5 transition-colors">
                    <span class="iconify" data-icon="heroicons:paper-airplane"></span> Kirim
                </button>
            </div>
        </form>
    </div>
@endif

<!-- Rating Section (Hanya jika status Closed) -->
@if($ticket->status === 'Closed')
    @if(!$ticket->rating)
        <!-- Form Rating Jika Belum Ada -->
        <div class="bg-gradient-to-br from-teal-50 to-white p-4 rounded-xl shadow-sm border border-teal-100 mb-6 text-center relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-10">
                <span class="iconify w-24 h-24 text-teal-500" data-icon="heroicons:star-solid"></span>
            </div>
            
            <h3 class="text-sm font-bold text-teal-800 mb-1 relative z-10">Yay! Tiket Selesai</h3>
            <p class="text-[11px] text-teal-700 mb-3 relative z-10">Bagaimana pengalaman Anda terkait pelayanan bantuan aduan ini?</p>
            
            <form action="{{ route('karyawan.tickets.rate', $ticket->id) }}" method="POST" id="ratingForm" class="relative z-10">
                @csrf
                <div class="flex justify-center gap-1.5 mb-3" id="starContainer">
                    @for($i=1; $i<=5; $i++)
                        <div class="text-gray-300 star-btn cursor-pointer transition-transform duration-200 hover:scale-110" style="outline: none;" data-value="{{ $i }}">
                            <span class="iconify w-8 h-8 pointer-events-none" data-icon="heroicons:star-solid"></span>
                        </div>
                    @endfor
                </div>
                <input type="hidden" name="score" id="scoreInput" value="0">
                @error('score')
                    <p class="text-red-500 text-[10px] mt-1 mb-2">{{ $message }}</p>
                @enderror
                
                <textarea name="feedback" rows="2" class="w-full text-xs border border-teal-200 rounded-lg px-3 py-2 outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all resize-none mb-3 bg-white" placeholder="Masukan atau pesan tambahan (opsional)"></textarea>
                
                <button type="button" id="submitRating" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs py-2.5 rounded-lg shadow-sm transition-colors">
                    Kirim Penilaian Pemuasan
                </button>
            </form>
        </div>
    @else
        <!-- Display Existing Rating Jika Sudah Ada -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-yellow-100 mb-6 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 opacity-5">
                <span class="iconify w-28 h-28 text-yellow-500" data-icon="heroicons:star-solid"></span>
            </div>
            
            <div class="flex items-center justify-between mb-2.5 relative z-10">
                <h3 class="text-xs font-bold text-gray-700">Penilaian Anda</h3>
                <div class="flex text-yellow-400 gap-0.5">
                    @for($i=1; $i<=5; $i++)
                        @if($i <= $ticket->rating->score)
                            <span class="iconify w-4 h-4" data-icon="heroicons:star-solid"></span>
                        @else
                            <span class="iconify w-4 h-4 text-gray-200" data-icon="heroicons:star-solid"></span>
                        @endif
                    @endfor
                </div>
            </div>
            
            @if($ticket->rating->feedback)
                <div class="bg-yellow-50/50 p-3 rounded-lg border border-yellow-100 text-xs text-gray-700 italic relative z-10">
                    "{{ $ticket->rating->feedback }}"
                </div>
            @else
                <p class="text-[11px] text-gray-400 relative z-10">Tidak ada pesan tambahan.</p>
            @endif
        </div>
    @endif
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-btn');
        const scoreInput = document.getElementById('scoreInput');
        const submitBtn = document.getElementById('submitRating');
        const form = document.getElementById('ratingForm');

        if(stars.length > 0) {
            stars.forEach(star => {
                // Hover effect preview
                star.addEventListener('mouseenter', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    stars.forEach(s => {
                        const sVal = parseInt(s.getAttribute('data-value'));
                        if(sVal <= val) {
                            s.classList.add('text-yellow-300'); // Preview color
                        } else {
                            s.classList.remove('text-yellow-300');
                        }
                    });
                });
                
                // Remove hover effect preview when leaving container
                document.getElementById('starContainer').addEventListener('mouseleave', function() {
                    stars.forEach(s => {
                        s.classList.remove('text-yellow-300');
                    });
                });

                // Click to set score
                star.addEventListener('click', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    scoreInput.value = val;
                    
                    stars.forEach(s => {
                        const sVal = parseInt(s.getAttribute('data-value'));
                        if(sVal <= val) {
                            s.classList.remove('text-gray-300');
                            s.classList.add('text-yellow-400');
                        } else {
                            s.classList.remove('text-yellow-400');
                            s.classList.add('text-gray-300');
                        }
                    });
                });
            });

            if (submitBtn) {
                submitBtn.addEventListener('click', function() {
                    if(scoreInput.value == 0) {
                        // Using browser alert, or custom modern toast if defined
                        alert('Silakan pilih bintang penilaian terlebih dahulu (1-5 Bintang).');
                        return;
                    }
                    form.submit();
                });
            }
        }
    });
</script>
@endpush
