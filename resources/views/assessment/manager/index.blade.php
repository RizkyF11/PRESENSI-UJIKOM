@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Penilaian Karyawan</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('manager.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Penilaian Karyawan</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">

            {{-- Progress Bar --}}
            <div class="card">
                <div class="body">
                    <h6 class="mb-2">
                        Progress Penilaian Bulan {{ now()->translatedFormat('F Y') }}
                    </h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span>
                            Anda telah menilai
                            <strong>{{ $totalDinilai }}</strong>
                            dari
                            <strong>{{ $totalKaryawan }}</strong>
                            karyawan bulan ini
                        </span>
                        <span><strong>{{ $persentase }}%</strong></span>
                    </div>
                    <div class="progress" style="height: 20px; border-radius: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated
                            {{ $persentase == 100 ? 'bg-success' : 'bg-primary' }}"
                            role="progressbar"
                            data-width="{{ $persentase }}"
                            id="progressBar">
                        </div>
                    </div>
                    @if($persentase == 100)
                        <small class="text-success mt-1 d-block">
                            <i class="fa fa-check-circle"></i>
                            Semua karyawan sudah dinilai bulan ini! 🎉
                        </small>
                    @endif
                </div>
            </div>

            {{-- Card List Karyawan --}}
            <div class="card">
                <div class="header">
                    <h2>Daftar Karyawan</h2>
                </div>
                <div class="body">

                    {{-- Filter Tombol --}}
                    <div class="mb-3">
                        <button class="btn btn-sm btn-outline-secondary"
                            onclick="filterCards('all')">
                            <i class="fa fa-users"></i>
                            Semua ({{ $totalKaryawan }})
                        </button>
                        <button class="btn btn-sm btn-outline-warning"
                            onclick="filterCards('belum')">
                            <i class="fa fa-clock-o"></i>
                            Belum Dinilai ({{ $totalKaryawan - $totalDinilai }})
                        </button>
                        <button class="btn btn-sm btn-outline-success"
                            onclick="filterCards('sudah')">
                            <i class="fa fa-check"></i>
                            Sudah Dinilai ({{ $totalDinilai }})
                        </button>
                    </div>

                    {{-- List Karyawan --}}
                    @forelse($karyawans as $item)
                    @php
                        $sudah      = in_array($item->id, $sudahDinilaiIds);
                        $avatarBg   = $sudah ? 'bg-success' : 'bg-warning';
                        $cardClass  = $sudah ? 'sudah' : 'belum';
                        $borderData = $sudah ? '#28a745' : '#ffc107';
                        $assessment = $sudah
                            ? \App\Models\Assessment::where('evaluator_id', auth()->id())
                                ->where('evaluatee_id', $item->id)
                                ->whereMonth('assessment_date', now()->month)
                                ->whereYear('assessment_date', now()->year)
                                ->first()
                            : null;
                    @endphp

                    <div class="card mb-2 karyawan-card {{ $cardClass }}"
                        data-border="{{ $borderData }}">
                        <div class="body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center">

                                {{-- Info Karyawan --}}
                                <div class="d-flex align-items-center">
                                    {{-- Avatar Inisial --}}
                                    <div class="rounded-circle text-white d-flex align-items-center
                                        justify-content-center mr-3 {{ $avatarBg }}"
                                        style="width:40px; height:40px; font-size:16px;
                                        font-weight:bold; flex-shrink:0;">
                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $item->nama }}</h6>
                                        <small class="text-muted">
                                            {{ $item->karyawan->jabatan ?? '-' }} |
                                            NIP: {{ $item->karyawan->nip ?? '-' }}
                                        </small>
                                    </div>
                                </div>

                                {{-- Aksi --}}
                                <div class="d-flex align-items-center" style="gap: 6px;">
                                    @if($sudah)
                                        <span class="badge badge-success">
                                            <i class="fa fa-check"></i> Sudah Dinilai
                                        </span>
                                        @if($assessment)
                                        <a href="{{ route('manager.assessment.edit', $assessment->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('manager.assessment.destroy', $assessment->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Yakin ingin menghapus penilaian ini?')">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                        @endif
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fa fa-clock-o"></i> Belum Dinilai
                                        </span>
                                        <a href="{{ route('manager.assessment.create', ['karyawan_id' => $item->id]) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fa fa-star"></i> Nilai
                                        </a>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fa fa-users fa-3x text-muted mb-2"></i>
                        <p class="text-muted">Belum ada data karyawan</p>
                    </div>
                    @endforelse

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Set progress bar width via data attribute
    const bar = document.getElementById('progressBar');
    if (bar) bar.style.width = bar.dataset.width + '%';

    // Set border kiri tiap card via data attribute
    document.querySelectorAll('.karyawan-card').forEach(card => {
        card.style.borderLeft = '4px solid ' + card.dataset.border;
    });

    // Filter cards
    function filterCards(type) {
        const cards = document.querySelectorAll('.karyawan-card');
        cards.forEach(card => {
            if (type === 'all') {
                card.style.display = 'block';
            } else if (type === 'sudah') {
                card.style.display = card.classList.contains('sudah') ? 'block' : 'none';
            } else if (type === 'belum') {
                card.style.display = card.classList.contains('belum') ? 'block' : 'none';
            }
        });
    }
</script>
@endsection
