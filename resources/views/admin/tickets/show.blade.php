@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2><i class="fa fa-ticket"></i> Detail Tiket #{{ $ticket->id }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.helpdesk.tickets.index') }}">Helpdesk</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <!-- Bagian Kiri: Info Tiket & Rating -->
        <div class="col-lg-4 col-md-12">
            <div class="card top_widget" style="border-top: 4px solid #4099FF;">
                <div class="header">
                    <h2>Informasi Tiket</h2>
                </div>
                <div class="body">
                    <h5 class="font-weight-bold">{{ $ticket->subject }}</h5>
                    
                    <div class="mt-2 text-muted pb-3 mb-3 border-bottom">
                        <span class="badge {{ $ticket->status == 'Open' ? 'badge-info' : ($ticket->status == 'In-Progress' ? 'badge-warning' : 'badge-success') }}">
                            {{ $ticket->status }}
                        </span>
                        <span class="badge {{ $ticket->priority == 'High' ? 'badge-danger' : ($ticket->priority == 'Mid' ? 'badge-warning' : 'badge-secondary') }}">
                            Prioritas: {{ $ticket->priority }}
                        </span>
                    </div>

                    <ul class="list-unstyled flex-column mb-0">
                        <li class="mb-3">
                            <strong class="d-block text-muted mb-1"><i class="fa fa-user"></i> Pelapor</strong>
                            <span>{{ $ticket->reporter->nama ?? '-' }}</span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted mb-1"><i class="fa fa-headphones"></i> Operator</strong>
                            <span>{{ $ticket->operator->nama ?? 'Belum ditugaskan' }}</span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted mb-1"><i class="fa fa-tags"></i> Kategori</strong>
                            <span>{{ ucfirst($ticket->category) }}</span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted mb-1"><i class="fa fa-calendar"></i> Tanggal Masuk</strong>
                            <span>{{ $ticket->created_at->format('d M Y H:i') }}</span>
                        </li>
                        <li class="mb-3">
                            <strong class="d-block text-muted mb-1"><i class="fa fa-clock-o"></i> Response Time</strong>
                            <span>
                                @if($ticket->response_time_minutes !== null)
                                    {{ $ticket->response_time_minutes }} Menit
                                @else
                                    <span class="text-danger">Belum direspon</span>
                                @endif
                            </span>
                        </li>
                        <li>
                            <strong class="d-block text-muted mb-1"><i class="fa fa-check-square-o"></i> Resolution Time</strong>
                            <span>
                                @if($ticket->resolution_time_minutes !== null)
                                    {{ $ticket->resolution_time_minutes }} Menit
                                @else
                                    -
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Form Edit Status Tanpa Reply -->
            @if($ticket->status !== 'Closed')
            <div class="card">
                <div class="header">
                    <h2>Ubah Status Cepat</h2>
                </div>
                <div class="body">
                    <form action="{{ route('admin.helpdesk.tickets.update-status', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="input-group">
                            <select name="status" class="form-control" required>
                                <option value="Open" {{ $ticket->status == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="In-Progress" {{ $ticket->status == 'In-Progress' ? 'selected' : '' }}>In-Progress</option>
                                <option value="Closed" {{ $ticket->status == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Ubah</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Rating Section -->
            @if($ticket->rating)
            <div class="card top_widget" style="border-top: 4px solid #FFC107;">
                <div class="header">
                    <h2><i class="fa fa-star text-warning"></i> Penilaian Karyawan</h2>
                </div>
                <div class="body text-center pb-4">
                    <div class="text-warning mb-2" style="font-size: 24px;">
                        @for($i=1; $i<=5; $i++)
                            @if($i <= $ticket->rating->score)
                                <i class="fa fa-star"></i>
                            @else
                                <i class="fa fa-star-o text-muted"></i>
                            @endif
                        @endfor
                    </div>
                    @if($ticket->rating->feedback)
                        <div class="alert alert-light border font-italic text-muted p-3 mb-0 text-left">
                            "{{ $ticket->rating->feedback }}"
                        </div>
                    @else
                        <p class="text-muted mb-0"><small>Tidak ada pesan tambahan.</small></p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Bagian Kanan: Percakapan & Deskripsi -->
        <div class="col-lg-8 col-md-12">
            
            <div class="card">
                <div class="header bg-light">
                    <h2><i class="fa fa-file-text-o"></i> Deskripsi Kendala</h2>
                </div>
                <div class="body">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->description }}</p>
                </div>
            </div>

            <div class="card">
                <div class="header">
                    <h2><i class="fa fa-comments-o"></i> Thread Percakapan</h2>
                </div>
                <div class="body p-4" style="background-color: #f4f7f6; max-height: 500px; overflow-y: auto;">
                    
                    @forelse($ticket->responses as $resp)
                        @php 
                            // Cek apakah balasan dari admin/manager
                            $isAdmin = in_array($resp->responder->role ?? '', ['admin', 'manager']);
                        @endphp
                        
                        <div class="mb-4 d-flex {{ $isAdmin ? 'flex-row' : 'flex-row-reverse' }}">
                            <!-- Avatar placeholder -->
                            <div class="mr-3 ml-3 flex-shrink-0">
                                <div style="width: 40px; height: 40px; background-color: {{ $isAdmin ? '#4099ff' : '#9E9E9E' }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                    {{ substr($resp->responder->nama ?? 'A', 0, 1) }}
                                </div>
                            </div>
                            <!-- Bubble Message -->
                            <div class="{{ $isAdmin ? 'text-left' : 'text-right' }}" style="max-width: 75%;">
                                <div class="mb-1">
                                    <strong class="text-dark">{{ $resp->responder->nama ?? ($isAdmin ? 'Admin' : 'Karyawan') }}</strong>
                                    <small class="text-muted ml-2">
                                        {{ $resp->created_at->format('d M y H:i') }}
                                        @if($resp->is_auto_reply)
                                            <span class="badge badge-secondary ml-1" style="font-size: 9px;"><i class="fa fa-robot"></i> Auto-Reply</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="p-3 shadow-sm text-left" style="border-radius: 12px; background-color: {{ $isAdmin ? '#fff' : '#e2e8f0' }}; border: 1px solid {{ $isAdmin ? '#e3ebf6' : '#cbd5e1' }}; word-break: break-word;">
                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $resp->message }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted p-5 bg-white border" style="border-radius: 8px;">
                            <i class="fa fa-comments fa-3x mb-3 text-light"></i>
                            <p class="mb-0">Belum ada thread percakapan atas tiket ini.</p>
                        </div>
                    @endforelse

                </div>
                
                @if($ticket->status !== 'Closed')
                <div class="body border-top bg-white">
                    <!-- Auto Reply Suggestion Box -->
                    <div class="alert alert-info border border-info mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><i class="fa fa-lightbulb-o text-warning mr-1"></i> <strong>Saran Balasan (Auto-Reply)</strong></h6>
                            <button type="button" class="btn btn-sm btn-info" id="btnUseAutoReply">
                                <i class="fa fa-magic"></i> Gunakan Saran
                            </button>
                        </div>
                        <p class="mb-0" id="autoReplyText">{{ $autoReply }}</p>
                    </div>

                    <!-- Form Reply -->
                    <form action="{{ route('admin.helpdesk.tickets.reply', $ticket->id) }}" method="POST" id="formReply">
                        @csrf
                        <input type="hidden" name="is_auto_reply" id="isAutoReply" value="0">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Balasan Anda</label>
                            <textarea name="message" id="messageTextarea" rows="4" class="form-control" placeholder="Tulis balasan di sini..." required></textarea>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="form-group mb-md-0">
                                    <label class="font-weight-bold text-muted small mb-1">Set Status Setelah Balasan</label>
                                    <select name="new_status" class="form-control">
                                        <option value="In-Progress" {{ $ticket->status == 'In-Progress' ? 'selected' : '' }}>In-Progress (Berlangsung)</option>
                                        <option value="Open" {{ $ticket->status == 'Open' ? 'selected' : '' }}>Open (Berikan ke agen lain)</option>
                                        <option value="Closed" class="text-success font-weight-bold">Closed (Selesai)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary mt-3 mt-md-0 px-4">
                                    <i class="fa fa-paper-plane"></i> Kirim Balasan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

<style>
    .top_widget {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnUseAutoReply = document.getElementById('btnUseAutoReply');
        if(btnUseAutoReply) {
            btnUseAutoReply.addEventListener('click', function() {
                const autoReplyText = document.getElementById('autoReplyText').innerText;
                const messageTextarea = document.getElementById('messageTextarea');
                const isAutoReplyInput = document.getElementById('isAutoReply');
                
                messageTextarea.value = autoReplyText;
                isAutoReplyInput.value = '1'; // Flag as auto-reply
                
                // Tambahkan efek highlight singkat UI
                messageTextarea.style.backgroundColor = '#e8f4fa';
                setTimeout(() => {
                    messageTextarea.style.backgroundColor = '#fff';
                    messageTextarea.focus();
                }, 400);
            });
        }
        
        // Reset auto_reply flag jika admin ngedit manual teksnya
        const textarea = document.getElementById('messageTextarea');
        if(textarea) {
            textarea.addEventListener('input', function() {
                document.getElementById('isAutoReply').value = '0';
            });
        }
    });
</script>
@endpush
