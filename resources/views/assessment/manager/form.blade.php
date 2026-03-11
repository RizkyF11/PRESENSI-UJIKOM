@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>{{ isset($assessment) ? 'Edit' : 'Form' }} Penilaian</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('manager.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('manager.assessment.index') }}">
                            Penilaian Karyawan
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($assessment) ? 'Edit' : 'Nilai' }} {{ $karyawan->nama }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="header">
                    <h2>
                        {{ isset($assessment) ? 'Edit Penilaian' : 'Form Penilaian' }}
                        — {{ $karyawan->nama }}
                    </h2>
                </div>
                <div class="body">
                    <form action="{{ isset($assessment)
                            ? route('manager.assessment.update', $assessment->id)
                            : route('manager.assessment.store') }}"
                        method="POST" id="assessmentForm">
                        @csrf
                        @if(isset($assessment))
                            @method('PUT')
                        @endif

                        <input type="hidden" name="evaluatee_id" value="{{ $karyawan->id }}">

                        {{-- Info Karyawan --}}
                        <div class="card mb-4" style="border-left: 4px solid #007bff;">
                            <div class="body py-3 px-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white
                                        d-flex align-items-center justify-content-center mr-3"
                                        style="width:55px; height:55px; font-size:24px;
                                        font-weight:bold; flex-shrink:0;">
                                        {{ strtoupper(substr($karyawan->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $karyawan->nama }}</h5>
                                        <small class="text-muted">
                                            {{ $karyawan->karyawan->jabatan ?? '-' }} |
                                            NIP: {{ $karyawan->karyawan->nip ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Periode Penilaian --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        Periode Penilaian
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="period"
                                        class="form-control @error('period') is-invalid @enderror"
                                        value="{{ old('period', $assessment->period ?? 'Bulan ' . now()->translatedFormat('F Y')) }}"
                                        placeholder="Cth: Bulan Maret 2025"
                                        required>
                                    @error('period')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Penilaian Per Kategori & Pertanyaan --}}
                        <div class="form-group">
                            <label>
                                Penilaian Per Indikator
                                <span class="text-danger">*</span>
                            </label>
                            <p class="text-muted small mb-3">
                                <i class="fa fa-info-circle"></i>
                                Klik bintang untuk memberikan nilai pada setiap pertanyaan
                                (1 = Kurang, 5 = Istimewa)
                            </p>

                            @forelse($categories as $category)

                            {{-- Card per Kategori --}}
                            <div class="card mb-3" style="border-left: 4px solid #6c757d;">
                                <div class="body py-3 px-3">

                                    {{-- Header Kategori --}}
                                    <h6 class="mb-1 text-primary">
                                        <i class="fa fa-folder-open-o"></i>
                                        {{ $category->nama }}
                                    </h6>
                                    @if($category->description)
                                        <small class="text-muted d-block mb-3">
                                            {{ $category->description }}
                                        </small>
                                    @endif

                                    {{-- Loop Pertanyaan dalam Kategori --}}
                                    @forelse($category->activeQuestions as $question)
                                    @php
                                        $existingScore = $existingScores[$question->id] ?? 0;
                                        $scoreLabels   = [
                                            0 => 'Belum dinilai',
                                            1 => 'Kurang',
                                            2 => 'Cukup',
                                            3 => 'Baik',
                                            4 => 'Sangat Baik',
                                            5 => 'Istimewa',
                                        ];
                                    @endphp

                                    <div class="d-flex justify-content-between align-items-center
                                        flex-wrap py-2 {{ !$loop->last ? 'border-bottom' : '' }}">

                                        {{-- Teks Pertanyaan --}}
                                        <div style="min-width: 200px; max-width: 55%;">
                                            <span class="text-dark">
                                                {{ $loop->iteration }}. {{ $question->question }}
                                            </span>
                                        </div>

                                        {{-- Star Rating per Pertanyaan --}}
                                        <div class="star-rating d-flex align-items-center mt-2"
                                            data-question="{{ $question->id }}"
                                            data-current="{{ $existingScore }}">

                                            {{-- 5 Bintang --}}
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star star"
                                                data-value="{{ $i }}"
                                                style="font-size:28px; cursor:pointer;
                                                margin: 0 2px; transition: color 0.1s;">
                                            </i>
                                            @endfor

                                            {{-- Hidden input — scores[question_id] = nilai --}}
                                            <input type="hidden"
                                                name="scores[{{ $question->id }}]"
                                                id="score_{{ $question->id }}"
                                                value="{{ $existingScore }}">

                                            {{-- Label nilai --}}
                                            <span class="ml-2"
                                                id="label_{{ $question->id }}"
                                                style="min-width: 120px; font-size: 13px;">
                                                @if($existingScore > 0)
                                                    <strong class="text-warning">
                                                        {{ $existingScore }}/5
                                                    </strong>
                                                    — {{ $scoreLabels[$existingScore] }}
                                                @else
                                                    <span class="text-danger">Belum dinilai</span>
                                                @endif
                                            </span>
                                        </div>

                                    </div>
                                    @empty
                                    <div class="alert alert-warning mb-0">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        Belum ada pertanyaan aktif untuk kategori ini.
                                    </div>
                                    @endforelse

                                </div>
                            </div>

                            @empty
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                Belum ada kategori penilaian aktif.
                                Hubungi admin untuk menambahkan kategori dan pertanyaan.
                            </div>
                            @endforelse
                        </div>

                        {{-- Catatan/Feedback --}}
                        <div class="form-group">
                            <label>Catatan / Feedback Umum</label>
                            <textarea
                                name="general_notes"
                                class="form-control @error('general_notes') is-invalid @enderror"
                                rows="4"
                                placeholder="Tuliskan catatan atau feedback untuk karyawan ini...">{{ old('general_notes', $assessment->general_notes ?? '') }}</textarea>
                            @error('general_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <br>
                        {{-- Tombol Simpan --}}
                        <button type="submit" name="save" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan
                        </button>

                        {{-- Tombol Simpan & Lanjut (hanya saat tambah baru & ada karyawan berikutnya) --}}
                        @if(!isset($assessment) && isset($berikutnya) && $berikutnya)
                        <button type="submit" name="next" value="1" class="btn btn-success">
                            <i class="fa fa-arrow-right"></i>
                            Simpan & Lanjut → {{ $berikutnya->nama }}
                        </button>
                        @endif

                        <a href="{{ route('manager.assessment.index') }}"
                            class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const labels = ['', 'Kurang', 'Cukup', 'Baik', 'Sangat Baik', 'Istimewa'];

    // Set warna bintang awal dari data-current saat halaman dimuat
    document.querySelectorAll('.star-rating').forEach(container => {
        const current    = parseInt(container.dataset.current) || 0;
        const questionId = container.dataset.question;
        updateStars(questionId, current);
    });

    // Update warna bintang berdasarkan nilai
    function updateStars(questionId, value) {
        const stars = document.querySelectorAll(
            '.star-rating[data-question="' + questionId + '"] .star'
        );
        stars.forEach(star => {
            star.style.color = parseInt(star.dataset.value) <= value ? '#f39c12' : '#ddd';
        });
    }

    // Set nilai rating: update hidden input + label + bintang
    function setRating(questionId, value) {
        document.getElementById('score_' + questionId).value = value;

        const labelEl = document.getElementById('label_' + questionId);
        labelEl.innerHTML =
            '<strong class="text-warning">' + value + '/5</strong> — ' + labels[value];

        updateStars(questionId, value);

        const container = document.querySelector(
            '.star-rating[data-question="' + questionId + '"]'
        );
        if (container) container.dataset.current = value;
    }

    // Event click & hover pada setiap bintang
    document.querySelectorAll('.star').forEach(star => {

        // Klik → set rating permanen
        star.addEventListener('click', function () {
            const questionId = this.closest('.star-rating').dataset.question;
            setRating(questionId, parseInt(this.dataset.value));
        });

        // Hover masuk → preview warna
        star.addEventListener('mouseover', function () {
            const questionId = this.closest('.star-rating').dataset.question;
            updateStars(questionId, parseInt(this.dataset.value));
        });

        // Hover keluar → balik ke nilai yang sudah dipilih
        star.addEventListener('mouseout', function () {
            const questionId = this.closest('.star-rating').dataset.question;
            const current    = parseInt(
                document.querySelector(
                    '.star-rating[data-question="' + questionId + '"]'
                ).dataset.current
            ) || 0;
            updateStars(questionId, current);
        });
    });

    // Validasi: semua pertanyaan harus dinilai sebelum submit
    document.getElementById('assessmentForm').addEventListener('submit', function (e) {
        const inputs  = document.querySelectorAll('[id^="score_"]');
        let allRated  = true;

        inputs.forEach(input => {
            if (!input.value || input.value == 0) {
                allRated = false;
            }
        });

        if (!allRated) {
            e.preventDefault();
            alert('Harap berikan nilai bintang untuk semua pertanyaan penilaian!');
        }
    });
</script>
@endsection