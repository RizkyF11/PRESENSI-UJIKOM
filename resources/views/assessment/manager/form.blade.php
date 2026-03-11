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

                        {{-- Hidden ID karyawan yang dinilai --}}
                        <input type="hidden" name="evaluatee_id" value="{{ $karyawan->id }}">

                        {{-- Info Karyawan --}}
                        <div class="card mb-4" data-info-card="true">
                            <div class="body py-3 px-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white
                                        d-flex align-items-center justify-content-center mr-3"
                                        style="width:50px; height:50px; font-size:22px;
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

                        {{-- Star Rating Per Kategori --}}
                        <div class="form-group">
                            <label>
                                Penilaian Per Indikator
                                <span class="text-danger">*</span>
                            </label>
                            <p class="text-muted small mb-3">
                                <i class="fa fa-info-circle"></i>
                                Klik bintang untuk memberikan nilai
                                (1 = Kurang, 5 = Istimewa)
                            </p>

                            @forelse($categories as $category)
                            @php
                                $existingScore = $existingScores[$category->id]
                                    ?? ($existing?->details
                                        ->where('category_id', $category->id)
                                        ->first()?->score)
                                    ?? 0;
                                $scoreLabels = [
                                    0 => 'Belum dinilai',
                                    1 => 'Kurang',
                                    2 => 'Cukup',
                                    3 => 'Baik',
                                    4 => 'Sangat Baik',
                                    5 => 'Istimewa'
                                ];
                            @endphp

                            <div class="card mb-2" data-category-card="true">
                                <div class="body py-3 px-3">
                                    <div class="d-flex justify-content-between
                                        align-items-center flex-wrap">

                                        {{-- Nama & Deskripsi Kategori --}}
                                        <div style="min-width: 200px;">
                                            <strong>{{ $category->nama }}</strong>
                                            @if($category->description)
                                                <br>
                                                <small class="text-muted">
                                                    {{ $category->description }}
                                                </small>
                                            @endif
                                        </div>

                                        {{-- Bintang --}}
                                        <div class="star-rating d-flex align-items-center mt-2"
                                            data-category="{{ $category->id }}"
                                            data-current="{{ $existingScore }}">
                                            @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star star"
                                                data-value="{{ $i }}"
                                                style="font-size:30px; cursor:pointer;
                                                margin: 0 2px; transition: color 0.1s;">
                                            </i>
                                            @endfor
                                            <input type="hidden"
                                                name="scores[{{ $category->id }}]"
                                                id="score_{{ $category->id }}"
                                                value="{{ $existingScore }}">
                                            <span class="ml-3"
                                                id="label_{{ $category->id }}"
                                                style="min-width: 120px;">
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
                                </div>
                            </div>
                            @empty
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                Belum ada kategori penilaian aktif.
                                Hubungi admin untuk menambahkan kategori.
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

                        {{-- Tombol Simpan & Lanjut (hanya saat tambah baru) --}}
                        @if(!isset($assessment) && isset($berikutnya))
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

    // Set warna bintang awal dari data-current
    document.querySelectorAll('.star-rating').forEach(container => {
        const current = parseInt(container.dataset.current) || 0;
        const categoryId = container.dataset.category;
        updateStars(categoryId, current);
    });

    function updateStars(categoryId, value) {
        const stars = document.querySelectorAll(
            '.star-rating[data-category="' + categoryId + '"] .star'
        );
        stars.forEach(star => {
            star.style.color = parseInt(star.dataset.value) <= value ? '#f39c12' : '#ddd';
        });
    }

    function setRating(categoryId, value) {
        // Update hidden input
        document.getElementById('score_' + categoryId).value = value;

        // Update label
        const labelEl = document.getElementById('label_' + categoryId);
        labelEl.innerHTML = '<strong class="text-warning">' + value + '/5</strong> — ' + labels[value];

        // Update bintang
        updateStars(categoryId, value);

        // Update data-current
        const container = document.querySelector('.star-rating[data-category="' + categoryId + '"]');
        if (container) container.dataset.current = value;
    }

    // Event click & hover pada bintang
    document.querySelectorAll('.star').forEach(star => {
        // Klik bintang
        star.addEventListener('click', function () {
            const categoryId = this.closest('.star-rating').dataset.category;
            setRating(categoryId, parseInt(this.dataset.value));
        });

        // Hover masuk
        star.addEventListener('mouseover', function () {
            const categoryId = this.closest('.star-rating').dataset.category;
            updateStars(categoryId, parseInt(this.dataset.value));
        });

        // Hover keluar → balik ke nilai yang sudah dipilih
        star.addEventListener('mouseout', function () {
            const categoryId = this.closest('.star-rating').dataset.category;
            const current = parseInt(
                document.querySelector('.star-rating[data-category="' + categoryId + '"]').dataset.current
            ) || 0;
            updateStars(categoryId, current);
        });
    });

    // Validasi semua kategori harus dinilai sebelum submit
    document.getElementById('assessmentForm').addEventListener('submit', function (e) {
        const inputs = document.querySelectorAll('[id^="score_"]');
        let allRated = true;

        inputs.forEach(input => {
            if (!input.value || input.value == 0) {
                allRated = false;
            }
        });

        if (!allRated) {
            e.preventDefault();
            alert('Harap berikan nilai bintang untuk semua indikator penilaian!');
        }
    });
</script>
@endsection
