@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="block-header">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <h2>
                    <i class="fa fa-gift text-primary"></i>
                    Flexibility Reward Catalog
                </h2>

                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#"><i class="fa fa-dashboard"></i></a>
                    </li>
                    <li class="breadcrumb-item">Gamification</li>
                    <li class="breadcrumb-item active">Reward Catalog</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">

        <div class="col-md-12">

            <div class="card shadow-sm">

                <div class="header d-flex justify-content-between align-items-center">
                    <h2>
                        <i class="fa fa-ticket"></i>
                        Daftar Reward / Token Kelonggaran
                    </h2>

                    <a href="{{ route('admin.flexibility-items.create') }}"
                       class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i>
                        Tambah Reward
                    </a>
                </div>

                <div class="body">

                    <div class="table-responsive">

                        <table class="table table-hover table-bordered"
                               id="addrowExample">

                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Reward Item</th>
                                    <th>Harga</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th width="25%">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($items as $item)

                                <tr>

                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <strong>
                                            <i class="fa fa-gift text-warning"></i>
                                            {{ $item->item_name }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="badge badge-primary px-3 py-2">
                                            {{ $item->point_cost }} Point
                                        </span>
                                    </td>

                                    <td>
                                        @if($item->stock_limit)
                                            <span class="badge badge-info">
                                                {{ $item->stock_limit }} Slot
                                            </span>
                                        @else
                                            <span class="badge badge-success">
                                                Unlimited
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($item->is_active)
                                            <span class="badge badge-success">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td>

                                        <a href="{{ route('admin.flexibility-items.edit',$item->id) }}"
                                           class="btn btn-warning btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>

                                        @if($item->is_active)

                                        <form action="{{ route('admin.flexibility-items.deactivate',$item->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Nonaktifkan item ini?')">
                                                <i class="fa fa-ban"></i>
                                            </button>
                                        </form>

                                        @else

                                        <form action="{{ route('admin.flexibility-items.activate',$item->id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-success btn-sm"
                                                    onclick="return confirm('Aktifkan item ini?')">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </form>

                                        @endif

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada reward tersedia.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection