@extends('layouts.admin')

@section('content')
<div class="container-fluid ">
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Pengajuan Cuti</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.cuti.index') }}"><i class="fa fa-dashboard"></i></a>
                    </li>
                    <li class="breadcrumb-item">Absensi</li>
                    <li class="breadcrumb-item active">Cuti</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row clearfix">

        <div class="card ">
            <div class="header">
                <h2>Daftar Pengajuan Cuti</h2>
            </div>

            <div class="body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover table-striped" cellspacing="0" id="addrowExample">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Lama Hari</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th width="220">Action</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Lama Hari</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>

                        <tbody>

                            @foreach ($cuti as $item)

                            @php
                            $mulai = \Carbon\Carbon::parse($item->tanggal_mulai);
                            $selesai = \Carbon\Carbon::parse($item->tanggal_selesai);
                            $lama = $mulai->diffInDays($selesai) + 1;
                            @endphp

                            <tr>

                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ $item->karyawan->user->nama ?? '-' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $lama }} Hari
                                </td>

                                <td>
                                    {{ $item->alasan }}
                                </td>

                                <td>

                                    @if ($item->status == 'pending')

                                    <span class="badge badge-warning">
                                        Pending
                                    </span>

                                    @elseif ($item->status == 'approved')

                                    <span class="badge badge-success">
                                        Approved
                                    </span>

                                    @elseif ($item->status == 'reject')

                                    <span class="badge badge-danger">
                                        Reject
                                    </span>

                                    @endif

                                </td>

                                <td>

                                    {{-- APPROVE --}}
                                    @if($item->status == 'pending')
                                    <form action="{{ route('admin.cuti.approve',$item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-success btn-sm"
                                            onclick="return confirm('Setujui pengajuan cuti ini?')">
                                            Approve
                                        </button>
                                    </form>
                                    @endif


                                    {{-- REJECT --}}
                                    @if($item->status == 'pending')
                                    <form action="{{ route('admin.cuti.reject',$item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button class="btn btn-warning btn-sm"
                                            onclick="return confirm('Tolak pengajuan cuti ini?')">
                                            Reject
                                        </button>
                                    </form>
                                    @endif


                                    {{-- DELETE --}}
                                    <form action="{{ route('admin.cuti.destroy',$item->id) }}"
                                        method="POST"
                                        style="display:inline;">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">

                                            Hapus

                                        </button>

                                    </form>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
</div>
@endsection