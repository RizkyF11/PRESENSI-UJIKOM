@extends('layouts.karyawan')

@section('header-left')
<div class="flex items-center gap-3">
    <a href="{{ route('karyawan.tickets.index') }}" class="text-gray-800 hover:text-teal-600 transition-colors">
        <span class="iconify" data-icon="heroicons:arrow-left-solid" data-width="24"></span>
    </a>
    <h1 class="text-[16px] font-bold text-gray-800 leading-tight mb-0">
        Buat Aduan Baru
    </h1>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form action="{{ route('karyawan.tickets.store') }}" method="POST" id="ticketForm">
        @csrf
        
        <!-- Field Subject -->
        <div class="mb-4">
            <label for="subject" class="block text-xs font-bold text-gray-700 mb-1">Subject / Judul Aduan <span class="text-red-500">*</span></label>
            <input type="text" name="subject" id="subject" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all @error('subject') border-red-500 ring-red-500 @enderror" placeholder="Contoh: AC Ruangan HRD Mati" value="{{ old('subject') }}" required>
            @error('subject')
                <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Field Kategori -->
        <div class="mb-4">
            <label for="category" class="block text-xs font-bold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
            <select name="category" id="category" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all @error('category') border-red-500 ring-red-500 @enderror" required>
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            @error('category')
                <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Radio Prioritas -->
        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-700 mb-2">Prioritas <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="radio" name="priority" value="Low" class="text-teal-500 focus:ring-teal-500 w-4 h-4" {{ old('priority', 'Low') == 'Low' ? 'checked' : '' }}>
                    <span class="text-sm text-gray-600">Low</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="radio" name="priority" value="Mid" class="text-teal-500 focus:ring-teal-500 w-4 h-4" {{ old('priority') == 'Mid' ? 'checked' : '' }}>
                    <span class="text-sm text-gray-600">Mid</span>
                </label>
                <label class="flex items-center gap-1.5 cursor-pointer">
                    <input type="radio" name="priority" value="High" class="text-teal-500 focus:ring-teal-500 w-4 h-4" {{ old('priority') == 'High' ? 'checked' : '' }}>
                    <span class="text-sm text-gray-600">High</span>
                </label>
            </div>
            @error('priority')
                <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Field Deskripsi -->
        <div class="mb-4">
            <label for="description" class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Lengkap <span class="text-red-500">*</span></label>
            <textarea name="description" id="description" rows="4" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all resize-none @error('description') border-red-500 ring-red-500 @enderror" placeholder="Jelaskan detail kendala Anda..." required>{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Panel Kuning Anti-Duplikasi (Hidden by default) -->
        <div id="similarTicketsPanel" class="hidden mb-4 bg-amber-50 border border-amber-200 rounded-lg p-3">
            <div class="flex items-start gap-2 mb-2">
                <span class="iconify text-amber-500 shrink-0 mt-0.5" data-icon="heroicons:exclamation-triangle" data-width="18"></span>
                <p class="text-xs font-bold text-amber-800 leading-tight">Membantu mempercepat! Mungkin kendala Anda sudah pernah dilaporkan? Cek tiket serupa ini:</p>
            </div>
            <ul id="similarTicketsList" class="space-y-2 max-h-40 overflow-y-auto no-scrollbar ml-1">
                <!-- Data dari AJAX -->
            </ul>
        </div>

        <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold text-sm py-2.5 rounded-lg shadow-sm transition-colors flex items-center justify-center gap-2">
            <span class="iconify" data-icon="heroicons:paper-airplane" data-width="18"></span> Kirim Aduan
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectInput = document.getElementById('subject');
        const descInput = document.getElementById('description');
        const panel = document.getElementById('similarTicketsPanel');
        const list = document.getElementById('similarTicketsList');
        let timeout = null;

        function checkSimilar() {
            clearTimeout(timeout);
            const keyword = subjectInput.value + ' ' + descInput.value;
            // Hanya fetch jika keyword minimal 5 karakter
            if(keyword.trim().length < 5) {
                panel.classList.add('hidden');
                return;
            }

            timeout = setTimeout(() => {
                const url = `{{ route('karyawan.tickets.search-similar') }}?keyword=${encodeURIComponent(keyword)}`;
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.found === true && data.tickets && data.tickets.length > 0) {
                        list.innerHTML = '';
                        data.tickets.forEach(ticket => {
                            let statusClass = 'bg-gray-100 text-gray-700';
                            if(ticket.status === 'Open') statusClass = 'bg-blue-100 text-blue-700';
                            else if(ticket.status === 'In-Progress') statusClass = 'bg-yellow-100 text-yellow-700';
                            else if(ticket.status === 'Closed') statusClass = 'bg-green-100 text-green-700';

                            let prioClass = 'bg-gray-100 text-gray-700';
                            if(ticket.priority === 'High') prioClass = 'bg-red-100 text-red-700';
                            else if(ticket.priority === 'Mid') prioClass = 'bg-yellow-100 text-yellow-700';

                            const tkUrl = `/karyawan/tickets/${ticket.id}`;
                            list.innerHTML += `
                                <li class="bg-white p-2 rounded-lg border border-amber-100 shadow-sm flex flex-col gap-1.5 hover:shadow transition-shadow">
                                    <div class="flex justify-between gap-2 items-start">
                                        <a href="${tkUrl}" target="_blank" class="text-xs font-bold text-teal-600 hover:text-teal-700 underline line-clamp-1 block leading-tight">
                                            #${ticket.id} - ${ticket.subject}
                                        </a>
                                        <div class="flex gap-1 shrink-0">
                                            <span class="${statusClass} text-[9px] font-bold px-1.5 py-0.5 rounded-md whitespace-nowrap">${ticket.status}</span>
                                            <span class="${prioClass} text-[9px] font-bold px-1.5 py-0.5 rounded-md whitespace-nowrap">${ticket.priority}</span>
                                        </div>
                                    </div>
                                </li>
                            `;
                        });
                        panel.classList.remove('hidden');
                    } else {
                        panel.classList.add('hidden');
                    }
                })
                .catch(err => {
                    console.error('Error fetching similar tickets:', err);
                });
            }, 600); // Debounce 600ms sesuai instruksi
        }

        subjectInput.addEventListener('input', checkSimilar);
        descInput.addEventListener('input', checkSimilar);
    });
</script>
@endpush
