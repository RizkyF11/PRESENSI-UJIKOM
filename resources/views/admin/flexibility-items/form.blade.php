@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="block-header">
        <div class="row">
            <div class="col-lg-8">
                <h2>
                    <i class="fa fa-gift text-primary"></i>
                    {{ isset($flexibilityItem->id) ? 'Edit Reward' : 'Tambah Reward' }}
                </h2>

                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.flexibility-items.index') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Gamification</li>
                    <li class="breadcrumb-item active">
                        Reward Form
                    </li>
                </ul>
            </div>
        </div>
    </div>


    <div class="row clearfix">

        <div class="col-md-12">

            <div class="card shadow-sm">

                <div class="header">
                    <h2>
                        <i class="fa fa-ticket"></i>
                        Form Reward Catalog
                    </h2>
                </div>

                <div class="body">

                    <form action="{{ isset($flexibilityItem->id)
                        ? route('admin.flexibility-items.update',$flexibilityItem->id)
                        : route('admin.flexibility-items.store') }}"
                        method="POST">

                        @csrf
                        @if(isset($flexibilityItem->id))
                            @method('PUT')
                        @endif


                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Reward</label>

                                    <input type="text"
                                           name="item_name"
                                           class="form-control"
                                           value="{{ old('item_name',$flexibilityItem->item_name ?? '') }}"
                                           placeholder="Contoh: Late Pass / WFH Token"
                                           required>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Harga Point</label>

                                    <input type="number"
                                           name="point_cost"
                                           class="form-control"
                                           value="{{ old('point_cost',$flexibilityItem->point_cost ?? '') }}"
                                           placeholder="Contoh: 50"
                                           required>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Stock Limit</label>

                                    <input type="number"
                                           name="stock_limit"
                                           class="form-control"
                                           value="{{ old('stock_limit',$flexibilityItem->stock_limit ?? '') }}"
                                           placeholder="Kosongkan jika unlimited">
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label>Status</label>

                                    <select name="is_active"
                                            class="form-control">

                                        <option value="1"
                                            {{ old('is_active',$flexibilityItem->is_active ?? 1) == 1 ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="0"
                                            {{ old('is_active',$flexibilityItem->is_active ?? 1) == 0 ? 'selected' : '' }}>
                                            Inactive
                                        </option>

                                    </select>

                                </div>
                            </div>

                        </div>


                        <button type="submit"
                                class="btn btn-primary">
                            <i class="fa fa-save"></i>
                            {{ isset($flexibilityItem->id) ? 'Update Reward' : 'Simpan Reward' }}
                        </button>

                        <a href="{{ route('admin.flexibility-items.index') }}"
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