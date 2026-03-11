@extends('layouts.karyawan')

@section('header-left')
<div class="flex items-center gap-3">
    <button onclick="window.history.back()" class="flex items-center justify-center w-9 h-9 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 border border-gray-200">
        <span class="iconify" data-icon="heroicons:arrow-left" data-width="20"></span>
    </button>
    <div class="flex flex-col">
        <h1 class="text-[15px] font-bold text-gray-800 leading-tight mb-0">
            Rapor Kinerja
        </h1>
        <p class="text-[12px] font-medium text-gray-500 mb-0">
            Lihat kekuatan & area pengembangan
        </p>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid pt-2">
    {{-- Kartu ringkasan singkat --}}
    <div class="card border-0 shadow-sm mb-4"
         style="border-radius: 16px; background: linear-gradient(135deg, #4DB6AC 0%, #2C7A7B 100%); overflow: hidden; position: relative;">
        <div style="position: absolute; right: -20px; top: -20px; opacity: 0.08;">
            <span class="iconify" data-icon="heroicons:chart-bar-square" data-width="150" style="color: white;"></span>
        </div>
        <div class="card-body p-4 position-relative z-10 text-white">
            <h5 class="font-medium mb-1" style="font-size: 14px; color: rgba(255,255,255,0.85);">
                Rapor Sikap Karyawan
            </h5>
            <h4 class="font-weight-bold mb-2">
                {{ auth()->user()->name ?? auth()->user()->nama }}
            </h4>

            <div class="d-flex flex-wrap align-items-center text-sm" style="font-size: 12px; gap: 8px;">
                <span class="badge bg-white text-teal-600 font-weight-bold px-2 py-1"
                      style="border-radius: 999px; font-size: 11px;">
                    {{ $assessments->count() }}x penilaian tersimpan
                </span>
                @if($assessments->isNotEmpty())
                    @php
                        $last = $assessments->first();
                    @endphp
                    <span class="badge bg-white text-gray-700 font-weight-semibold px-2 py-1"
                          style="border-radius: 999px; font-size: 11px;">
                        Penilaian terakhir:
                        {{ \Carbon\Carbon::parse($last->assessment_date)->translatedFormat('d M Y') }}
                    </span>
                @else
                    <span class="badge bg-white text-gray-700 font-weight-semibold px-2 py-1"
                          style="border-radius: 999px; font-size: 11px;">
                        Belum ada data penilaian
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Grafik Radar Area Sikap --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 14px;">
                    Peta Kekuatan Sikap
                </h6>
                <span class="badge text-teal-600 bg-teal-50 px-2 py-1 font-weight-bold"
                      style="border-radius: 999px; font-size: 11px;">
                    Rata-rata per kategori
                </span>
            </div>

            @if($radarData->isEmpty())
                <p class="text-center text-gray-500 mb-0" style="font-size: 12px;">
                    Belum ada data penilaian untuk ditampilkan. Silakan tunggu hingga atasan melakukan penilaian.
                </p>
            @else
                <div class="pt-2">
                    <canvas id="radarChart"
                            height="260"
                            data-labels='@json($radarData->keys()->toArray())'
                            data-scores='@json($radarData->values()->toArray())'></canvas>
                </div>
                <div class="mt-3 text-xs text-gray-500">
                    <p class="mb-1">
                        Skala nilai 1–5. Semakin jauh dari tengah, semakin kuat area sikap tersebut.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Riwayat Penilaian Timeline --}}
    <div class="card border-0 shadow-sm mb-2" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="font-weight-bold text-gray-800 mb-0" style="font-size: 14px;">
                    Riwayat Penilaian
                </h6>
                <span class="badge bg-gray-100 text-gray-700 px-2 py-1"
                      style="border-radius: 999px; font-size: 11px;">
                    Bulan ke bulan
                </span>
            </div>

            @if($assessments->isEmpty())
                <p class="text-center text-gray-500 mb-0" style="font-size: 12px;">
                    Belum ada riwayat penilaian.
                </p>
            @else
                <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="bg-gray-50">
                            <tr>
                                <th style="font-size: 11px; font-weight: 600; color: #6B7280; border-top: none;">Periode</th>
                                <th style="font-size: 11px; font-weight: 600; color: #6B7280; border-top: none;">Tanggal</th>
                                <th style="font-size: 11px; font-weight: 600; color: #6B7280; border-top: none;">Penilai</th>
                                <th style="font-size: 11px; font-weight: 600; color: #6B7280; border-top: none;">Rata-rata</th>
                                <th style="font-size: 11px; font-weight: 600; color: #6B7280; border-top: none;">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessments as $assessment)
                                @php
                                    $avgScore = $assessment->details->avg('score');
                                    $badgeClass = 'bg-gray-100 text-gray-800';
                                    if ($avgScore >= 4.5) {
                                        $badgeClass = 'bg-green-50 text-green-700';
                                    } elseif ($avgScore >= 3.5) {
                                        $badgeClass = 'bg-teal-50 text-teal-700';
                                    } elseif ($avgScore >= 2.5) {
                                        $badgeClass = 'bg-yellow-50 text-yellow-700';
                                    } else {
                                        $badgeClass = 'bg-red-50 text-red-700';
                                    }
                                @endphp
                                <tr>
                                    <td style="font-size: 11px; color: #374151;">
                                        {{ $assessment->period ?? \Carbon\Carbon::parse($assessment->assessment_date)->translatedFormat('F Y') }}
                                    </td>
                                    <td style="font-size: 11px; color: #4B5563;">
                                        {{ \Carbon\Carbon::parse($assessment->assessment_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td style="font-size: 11px; color: #4B5563;">
                                        {{ $assessment->evaluator->karyawan->nama ?? $assessment->evaluator->nama ?? $assessment->evaluator->name ?? '-' }}
                                    </td>
                                    <td style="font-size: 11px;">
                                        <span class="badge px-2 py-1 {{ $badgeClass }}"
                                              style="border-radius: 999px; font-size: 11px;">
                                            {{ number_format($avgScore, 1) }}/5
                                        </span>
                                    </td>
                                    <td style="font-size: 11px; color: #6B7280; max-width: 160px;">
                                        {{ \Illuminate\Support\Str::limit($assessment->general_notes ?? '-', 60) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($radarData->isNotEmpty())
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('radarChart');
            if (!ctx) return;

            const labels = JSON.parse(ctx.dataset.labels || '[]');
            const scores = JSON.parse(ctx.dataset.scores || '[]');

            // Wrap label panjang jadi multi-line agar tetap readable di layar kecil
            function wrapLabel(label, maxLen) {
                if (!label) return '';
                const text = String(label).trim();
                if (text.length <= maxLen) return text;
                const words = text.split(/\s+/);
                const lines = [];
                let line = '';
                for (const w of words) {
                    const next = line ? (line + ' ' + w) : w;
                    if (next.length <= maxLen) {
                        line = next;
                    } else {
                        if (line) lines.push(line);
                        // jika satu kata sangat panjang, potong paksa
                        if (w.length > maxLen) {
                            lines.push(w.slice(0, maxLen - 1) + '…');
                            line = '';
                        } else {
                            line = w;
                        }
                    }
                }
                if (line) lines.push(line);
                return lines.length ? lines : text;
            }

            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Rata-rata Nilai Sikap',
                        data: scores,
                        backgroundColor: 'rgba(77, 182, 172, 0.25)',
                        borderColor: '#2C7A7B',
                        borderWidth: 2,
                        pointBackgroundColor: '#2C7A7B',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const lbl = Array.isArray(context.label) ? context.label.join(' ') : context.label;
                                    return lbl + ': ' + context.parsed.r + ' / 5';
                                }
                            }
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            suggestedMin: 0,
                            suggestedMax: 5,
                            ticks: {
                                stepSize: 1,
                                backdropColor: 'transparent',
                                color: '#6B7280',
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                color: 'rgba(156, 163, 175, 0.3)'
                            },
                            angleLines: {
                                color: 'rgba(156, 163, 175, 0.4)'
                            },
                            pointLabels: {
                                color: '#374151',
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                callback: function(label) {
                                    return wrapLabel(label, 14);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif
@endpush
