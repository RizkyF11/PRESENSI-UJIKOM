@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="block-header">
        <h2>Leaderboard Integritas Bulan Ini</h2>
    </div>

    {{-- TOP & LOW CARD --}}
    <div class="row clearfix">

        {{-- TOP USER --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0"
                 style="border-radius:12px; background: linear-gradient(135deg,#28a745,#20c997); color:white;">
                <div class="body">
                    <h5 class="mb-2">🏆 Poin Tertinggi</h5>
                    <h3 class="mb-1">{{ $topUser->nama ?? '-' }}</h3>
                    <span style="font-size:18px;">
                        {{ $topUser->total_points ?? 0 }} Points
                    </span>
                </div>
            </div>
        </div>

        {{-- LOW USER --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0"
                 style="border-radius:12px; background: linear-gradient(135deg,#dc3545,#fd7e14); color:white;">
                <div class="body">
                    <h5 class="mb-2">⚠ Poin Terendah</h5>
                    <h3 class="mb-1">{{ $lowUser->nama ?? '-' }}</h3>
                    <span style="font-size:18px;">
                        {{ $lowUser->total_points ?? 0 }} Points
                    </span>
                </div>
            </div>
        </div>

    </div>


    {{-- TABLE LEADERBOARD --}}
    <div class="card shadow-sm" style="border-radius:12px;">
        <div class="header">
            <h2>Ranking Semua Karyawan</h2>
        </div>

        <div class="body table-responsive">

            <table class="table table-hover align-middle">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th>Rank</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>Total Poin</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($leaderboard as $user)
                    <tr style="transition:0.2s;">

                        {{-- RANK --}}
                        <td>
                            @if($loop->iteration == 1)
                                <span class="badge badge-warning">🥇 1</span>
                            @elseif($loop->iteration == 2)
                                <span class="badge badge-secondary">🥈 2</span>
                            @elseif($loop->iteration == 3)
                                <span class="badge badge-danger">🥉 3</span>
                            @else
                                <span class="badge badge-light text-dark">
                                    {{ $loop->iteration }}
                                </span>
                            @endif
                        </td>

                        {{-- NAMA --}}
                        <td style="font-weight:600;">
                            {{ $user->nama }}
                        </td>

                        {{-- ROLE --}}
                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        {{-- POINT --}}
                        <td>
                            <span style="font-weight:700; font-size:16px;">
                                {{ $user->total_points }}
                            </span>
                        </td>

                    </tr>
                    @endforeach

                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection