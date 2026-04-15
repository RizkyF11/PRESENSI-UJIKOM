@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>{{ isset($pointRule->id) ? 'Edit' : 'Tambah' }} Point Rule</h2>

                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.point-rules.index') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>

                    <li class="breadcrumb-item">Gamification</li>

                    <li class="breadcrumb-item active">
                        {{ isset($pointRule->id) ? 'Edit' : 'Tambah' }} Rule
                    </li>
                </ul>
            </div>
        </div>
    </div>


    <div class="row clearfix">
        <div class="col-md-12">

            <div class="card">

                <div class="header">
                    <h2>Form Point Rule</h2>
                </div>

                <div class="body">

                    <form
                        action="{{ isset($pointRule->id) ? route('admin.point-rules.update',$pointRule->id) : route('admin.point-rules.store') }}"
                        method="POST">

                        @csrf
                        @if(isset($pointRule->id))
                        @method('PUT')
                        @endif


                        <div class="row">

                            {{-- RULE NAME --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Rule</label>
                                    <input type="text"
                                        name="rule_name"
                                        class="form-control @error('rule_name') is-invalid @enderror"
                                        value="{{ old('rule_name',$pointRule->rule_name ?? '') }}"
                                        required>

                                    @error('rule_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- TARGET ROLE --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Target Role</label>

                                    <select name="target_role"
                                        class="form-control @error('target_role') is-invalid @enderror"
                                        required>

                                        <option value="">-- Pilih Role --</option>

                                        <option value="karyawan"
                                            {{ old('target_role',$pointRule->target_role ?? '') == 'karyawan' ? 'selected' : '' }}>
                                            Karyawan
                                        </option>

                                        <option value="manager"
                                            {{ old('target_role',$pointRule->target_role ?? '') == 'manager' ? 'selected' : '' }}>
                                            Manager
                                        </option>

                                    </select>

                                    @error('target_role')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- CONDITIONAL TYPE --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipe Kondisi</label>

                                    <select name="conditional_type"
                                        class="form-control @error('conditional_type') is-invalid @enderror"
                                        required>

                                        <option value="">-- Pilih Tipe --</option>

                                        <option value="EARLY_MINUTES"
                                            {{ old('conditional_type',$pointRule->conditional_type ?? '') == 'EARLY_MINUTES' ? 'selected' : '' }}>
                                            Early Minutes
                                        </option>

                                        <option value="LATE_MINUTES"
                                            {{ old('conditional_type',$pointRule->conditional_type ?? '') == 'LATE_MINUTES' ? 'selected' : '' }}>
                                            Late Minutes
                                        </option>

                                    </select>

                                    @error('conditional_type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- OPERATOR --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Operator</label>

                                    <select name="condition_operator"
                                        class="form-control @error('condition_operator') is-invalid @enderror"
                                        required>

                                        <option value="">-- Pilih Operator --</option>

                                        <option value="<"
                                            {{ old('condition_operator',$pointRule->condition_operator ?? '') == '<' ? 'selected' : '' }}>
                                            Lebih Kecil (&lt;)
                                        </option>

                                        <option value=">"
                                            {{ old('condition_operator',$pointRule->condition_operator ?? '') == '>' ? 'selected' : '' }}>
                                            Lebih Besar (&gt;)
                                        </option>

                                        <option value="BETWEEN"
                                            {{ old('condition_operator',$pointRule->condition_operator ?? '') == 'BETWEEN' ? 'selected' : '' }}>
                                            Between / Range
                                        </option>

                                    </select>

                                    @error('condition_operator')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- CONDITION VALUE --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Condition Value</label>

                                    <input type="text"
                                        name="condition_value"
                                        class="form-control @error('condition_value') is-invalid @enderror"
                                        value="{{ old('condition_value',$pointRule->condition_value ?? '') }}"
                                        placeholder="Contoh: 15 / 1,10 / 30"
                                        required>

                                    @error('condition_value')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            {{-- POINT MODIFIER --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Modifier Poin</label>

                                    <input type="number"
                                        name="point_modifier"
                                        class="form-control @error('point_modifier') is-invalid @enderror"
                                        value="{{ old('point_modifier',$pointRule->point_modifier ?? '') }}"
                                        placeholder="Contoh: 5 / -3"
                                        required>

                                    @error('point_modifier')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>

                        </div>


                        <br>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            {{ isset($pointRule->id) ? 'Update Rule' : 'Simpan Rule' }}
                        </button>

                        <a href="{{ route('admin.point-rules.index') }}"
                            class="btn btn-secondary">
                            Batal
                        </a>

                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@endsection