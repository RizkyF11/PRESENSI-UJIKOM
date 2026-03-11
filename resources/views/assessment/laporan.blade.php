@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Laporan Penilaian Karyawan</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Laporan Penilaian</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>
                        Semua Data Penilaian
                        <small>Total: {{ $assessments->count() }} penilaian</small>
                    </h2>
                </div>
                <div class="body">

                    {{-- Search --}}
                    <div class="mb-3">
                        <input type="text"
                            id="searchInput"
                            class="form-control"
                            placeholder="Cari nama karyawan atau periode..."
                            style="max-width: 400px;">
                    </div>

                    @forelse($assessments as $item)
                    @php
                        $rataRata = round($item->details->avg('score'), 1);

                        if ($rataRata >= 4.5)      { $badge = 'badge-success'; $label = 'Istimewa'; }
                        elseif ($rataRata >= 3.5)  { $badge = 'badge-primary'; $label = 'Sangat Baik'; }
                        elseif ($rataRata >= 2.5)  { $badge = 'badge-info';    $label = 'Baik'; }
                        elseif ($rataRata >= 1.5)  { $badge = 'badge-warning'; $label = 'Cukup'; }
                        else                       { $badge = 'badge-danger';  $label = 'Kurang'; }

                        // Kelompokkan detail per kategori
                        $detailsPerKategori = $item->details->groupBy(
                            fn($d) => $d->question->category->nama ?? 'Lainnya'
                        );
                    @endphp

                    <div class="card mb-3 laporan-card"
                        data-nama="{{ strtolower($item->evaluatee->nama ?? '') }}"
                        data-periode="{{ strtolower($item->period ?? '') }}">
                        <div class="body">

                            {{-- Header Card --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white
                                        d-flex align-items-center justify-content-center mr-3"
                                        style="width:45px; height:45px; font-size:18px;
                                        font-weight:bold; flex-shrink:0;">
                                        {{ strtoupper(substr($item->evaluatee->nama ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $item->evaluatee->nama ?? '-' }}</h6>
                                        <small class="text-muted">
                                            {{ $item->evaluatee->karyawan->jabatan ?? '-' }} |
                                            Dinilai oleh:
                                            <strong>{{ $item->evaluator->nama ?? '-' }}</strong>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="badge {{ $badge }} p-2" style="font-size:13px;">
                                        ⭐ {{ $rataRata }}/5 — {{ $label }}
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $item->period }} |
                                        {{ $item->assessment_date->format('d M Y') }}
                                    </small>
                                </div>
                            </div>

                            {{-- Detail Nilai Per Kategori → Per Pertanyaan --}}
                            @foreach($detailsPerKategori as $namaKategori => $details)
                            <div class="mb-3">
                                {{-- Nama Kategori + Rata-rata Kategori --}}
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="font-weight-bold text-primary">
                                        <i class="fa fa-folder-open-o"></i>
                                        {{ $namaKategori }}
                                    </small>
                                    <small class="text-muted">
                                        Rata-rata:
                                        <strong>{{ round($details->avg('score'), 1) }}/5</strong>
                                    </small>
                                </div>

                                {{-- List Pertanyaan + Bintang --}}
                                @foreach($details as $detail)
                                <div class="d-flex justify-content-between align-items-center
                                    py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <small class="text-muted" style="max-width: 60%;">
                                        {{ $loop->iteration }}.
                                        {{ $detail->question->question ?? '-' }}
                                    </small>
                                    <div class="d-flex align-items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star"
                                                style="font-size:13px; margin-right:1px;
                                                color: {{ $i <= $detail->score ? '#f39c12' : '#ddd' }};">
                                            </i>
                                        @endfor
                                        <small class="ml-1 text-muted">
                                            ({{ $detail->score }}/5)
                                        </small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endforeach

                            {{-- Catatan --}}
                            @if($item->general_notes)
                            <div class="mt-2 p-2 bg-light rounded">
                                <small>
                                    <i class="fa fa-comment-o"></i>
                                    <strong>Catatan:</strong>
                                    {{ $item->general_notes }}
                                </small>
                            </div>
                            @endif

                            {{-- Tombol Hapus (Admin Only) --}}
                            <div class="mt-2 text-right">
                                <form action="{{ route('admin.assessment.destroy', $item->id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin ingin menghapus penilaian ini?')">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fa fa-bar-chart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data penilaian</p>
                    </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter pencarian nama / periode
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.laporan-card').forEach(card => {
            const nama    = card.dataset.nama;
            const periode = card.dataset.periode;
            card.style.display =
                (nama.includes(keyword) || periode.includes(keyword)) ? 'block' : 'none';
        });
    });
</script>
@endsection