@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER PAGE --}}
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Point Rules</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#"><i class="fa fa-dashboard"></i></a>
                    </li>
                    <li class="breadcrumb-item">Gamification</li>
                    <li class="breadcrumb-item active">Point Rules</li>
                </ul>
            </div>
        </div>
    </div>


    <div class="row clearfix">
        <div class="card">

            {{-- CARD HEADER --}}
            <div class="header">
                <h2>Daftar Aturan Poin</h2>
            </div>


            <div class="body">

                {{-- BUTTON TAMBAH --}}
                <a href="{{ route('admin.point-rules.create') }}"
                    class="btn btn-primary m-b-15">
                    <i class="fa fa-plus"></i>
                    Tambah Rule
                </a>


                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped"
                        id="addrowExample">

                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Rule</th>
                                <th>Target Role</th>
                                <th>Tipe Kondisi</th>
                                <th>Operator</th>
                                <th>Nilai Kondisi</th>
                                <th>Modifier Poin</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Rule</th>
                                <th>Target Role</th>
                                <th>Tipe Kondisi</th>
                                <th>Operator</th>
                                <th>Nilai Kondisi</th>
                                <th>Modifier Poin</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </tfoot>

                        <tbody>

                            @forelse($rules as $rule)

                            <tr>

                                {{-- NOMOR --}}
                                <td>{{ $loop->iteration }}</td>


                                {{-- NAMA RULE --}}
                                <td>
                                    <strong>{{ $rule->rule_name }}</strong>
                                </td>


                                {{-- TARGET ROLE --}}
                                <td>
                                    <span class="badge badge-info">
                                        {{ ucfirst($rule->target_role) }}
                                    </span>
                                </td>


                                {{-- CONDITIONAL TYPE --}}
                                <td>

                                    @if($rule->conditional_type == 'EARLY_MINUTES')

                                    <span class="badge badge-success">
                                        Early Minutes
                                    </span>

                                    @elseif($rule->conditional_type == 'LATE_MINUTES')

                                    <span class="badge badge-danger">
                                        Late Minutes
                                    </span>

                                    @else

                                    <span class="badge badge-secondary">
                                        Unknown
                                    </span>

                                    @endif

                                </td>


                                {{-- OPERATOR --}}
                                <td>
                                    <strong>
                                        {{ $rule->condition_operator }}
                                    </strong>
                                </td>


                                {{-- VALUE --}}
                                <td>
                                    {{ $rule->condition_value }}
                                </td>


                                {{-- POINT --}}
                                <td>

                                    @if($rule->point_modifier > 0)

                                    <span class="text-success font-weight-bold">
                                        +{{ $rule->point_modifier }}
                                    </span>

                                    @else

                                    <span class="text-danger font-weight-bold">
                                        {{ $rule->point_modifier }}
                                    </span>

                                    @endif

                                </td>


                                {{-- ACTION --}}
                                <td>

                                    <a href="{{ route('admin.point-rules.edit',$rule->id) }}"
                                        class="btn btn-warning btn-sm">
                                        Edit
                                    </a>


                                    <form action="{{ route('admin.point-rules.destroy',$rule->id) }}"
                                        method="POST"
                                        style="display:inline-block;">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin hapus rule ini?')">

                                            Hapus

                                        </button>

                                    </form>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="8" class="text-center">
                                    Belum ada data point rules.
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
@endsection