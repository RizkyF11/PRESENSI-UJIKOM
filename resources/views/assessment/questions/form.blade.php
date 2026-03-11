@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>{{ isset($question->id) ? 'Edit' : 'Tambah' }} Pertanyaan</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.assessment-categories.index') }}">
                            Kategori Penilaian
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.assessment-questions.index', $category->id) }}">
                            {{ $category->nama }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($question->id) ? 'Edit' : 'Tambah' }} Pertanyaan
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
                        {{ isset($question->id) ? 'Edit' : 'Tambah' }} Pertanyaan
                        — <span class="text-primary">{{ $category->nama }}</span>
                    </h2>
                </div>
                <div class="body">
                    <form action="{{ isset($question->id)
                            ? route('admin.assessment-questions.update', [$category->id, $question->id])
                            : route('admin.assessment-questions.store', $category->id) }}"
                        method="POST">
                        @csrf
                        @if(isset($question->id))
                            @method('PUT')
                        @endif

                        <div class="row">

                            {{-- Teks Pertanyaan --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>
                                        Pertanyaan
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        name="question"
                                        class="form-control @error('question') is-invalid @enderror"
                                        value="{{ old('question', $question->question ?? '') }}"
                                        placeholder="Cth: Apakah karyawan ini selalu hadir tepat waktu?"
                                        required>
                                    @error('question')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Urutan --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>
                                        Urutan Tampil
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        name="urutan"
                                        class="form-control @error('urutan') is-invalid @enderror"
                                        value="{{ old('urutan', $question->urutan ?? $nextUrutan) }}"
                                        min="1"
                                        required>
                                    <small class="text-muted">
                                        Angka kecil tampil lebih dulu
                                    </small>
                                    @error('urutan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <br>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            {{ isset($question->id) ? 'Simpan Perubahan' : 'Tambah Pertanyaan' }}
                        </button>
                        <a href="{{ route('admin.assessment-questions.index', $category->id) }}"
                            class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Batal
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection