@extends('layouts.karyawan')

@section('header-left')
<div class="flex items-center gap-3">
    <a href="{{ route('karyawan.dashboard') }}" class="text-gray-800 hover:text-teal-600 transition-colors">
        <span class="iconify" data-icon="heroicons:arrow-left-solid" data-width="24"></span>
    </a>
    <h1 class="text-[16px] font-bold text-gray-800 leading-tight mb-0">
        Helpdesk (Daftar Tiket)
    </h1>
</div>
@endsection

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-sm font-bold text-gray-700">Daftar Tiket Anda</h2>
    <a href="{{ route('karyawan.tickets.create') }}" class="inline-flex items-center gap-1 bg-teal-500 hover:bg-teal-600 text-white text-[11px] px-3 py-1.5 rounded-lg font-bold shadow-sm transition-colors">
        <span class="iconify text-sm" data-icon="heroicons:plus-circle"></span> Buat Aduan
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-4">
    @if($tickets->count() > 0)
        <!-- Wrapper overflow-x-auto untuk tabel di resolusi kecil (Tampilan Mobile) -->
        <div class="w-full overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse text-xs whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 font-semibold">
                        <th class="p-3 pl-4">No</th>
                        <th class="p-3">Subject</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Prioritas</th>
                        <th class="p-3">Status</th> 
                        <th class="p-3">Tanggal Buat</th>
                        <th class="p-3 pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @foreach($tickets as $index => $ticket)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-3 pl-4">{{ $tickets->firstItem() + $index }}</td>
                        <td class="p-3 font-medium text-gray-800 max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap" title="{{ $ticket->subject }}">
                            {{ $ticket->subject }}
                        </td>
                        <td class="p-3">{{ $ticket->category }}</td>
                        <td class="p-3">
                            @if($ticket->priority === 'High')
                                <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block">High</span>
                            @elseif($ticket->priority === 'Mid')
                                <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block">Mid</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block">Low</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @if($ticket->status === 'Open')
                                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block">Open</span>
                            @elseif($ticket->status === 'In-Progress')
                                <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block">In-Progress</span>
                            @elseif($ticket->status === 'Closed')
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block">Closed</span>
                            @else
                                <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block">{{ $ticket->status }}</span>
                            @endif
                        </td>
                        <td class="p-3">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3 pr-4">
                            <a href="{{ route('karyawan.tickets.show', $ticket->id) }}" class="inline-flex items-center justify-center p-1.5 w-7 h-7 bg-teal-50 text-teal-600 rounded shadow-sm hover:bg-teal-500 hover:text-white transition-colors" title="Lihat Detail">
                                <span class="iconify" data-icon="heroicons:eye"></span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <!-- Empty State -->
        <div class="p-8 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mb-3">
                <span class="iconify text-teal-400" data-icon="heroicons:inbox" data-width="32"></span>
            </div>
            <h3 class="text-sm font-bold text-gray-700 mb-1">Belum ada tiket aduan</h3>
            <p class="text-[11px] text-gray-500 mb-4">Jika Anda memiliki kendala, jangan ragu untuk membuat aduan baru.</p>
            <a href="{{ route('karyawan.tickets.create') }}" class="inline-flex items-center gap-1.5 bg-teal-500 hover:bg-teal-600 text-white text-[11px] font-bold px-4 py-2 rounded-lg shadow-sm transition">
                <span class="iconify text-sm" data-icon="heroicons:plus"></span> Buat Aduan Sekarang
            </a>
        </div>
    @endif
</div>

@if($tickets->hasPages())
    <div class="mb-6 pagination-wrapper overflow-x-auto no-scrollbar">
        {{ $tickets->links() }}
    </div>
@endif
@endsection
