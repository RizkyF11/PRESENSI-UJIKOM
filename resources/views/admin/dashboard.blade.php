@extends('layouts.admin')  

@section('content')
    <div class="container-fluid">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <h2>Dashboard</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                        <li class="breadcrumb-item">App</li>
                        <li class="breadcrumb-item active">Blog</li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                    <div class="d-flex flex-row-reverse">
                        <div class="page_action">
                            <button type="button" class="btn btn-primary">Generate Report</button>
                            <a href="#" class="btn btn-secondary" title="new post">New post</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row clearfix row-deck">
            @php
                $widgets = [
                    ['title' => 'Likes', 'value' => '22,500', 'percent' => '4%', 'icon' => 'fa-thumbs-o-up', 'color' => '#73cec7', 'data' => '2,3,1,5,4,2,3,1,5,4,7,8,2,3,1,4,6,5,4,4,4,7,8,2,3,1,4,6,5,4'],
                    ['title' => 'Comments', 'value' => '500', 'percent' => '11%', 'icon' => 'fa-comment', 'color' => '#7ea7de', 'data' => '7,8,2,3,1,4,6,2,3,1,5,4,7,8,2,3,1,4,6,5,4,4,2,3,1,5,4,5,4,4'],
                    ['title' => 'Share', 'value' => '2,215', 'percent' => '9%', 'icon' => 'fa-share-alt', 'color' => '#84d4a6', 'data' => '2,3,1,5,4,7,8,2,3,1,4,6,5,4,4,2,3,1,5,4,7,8,2,3,1,4,6,5,4,4'],
                    ['title' => 'View', 'value' => '421,215', 'percent' => '2%', 'icon' => 'fa-eye', 'color' => '#efc26b', 'data' => '2,3,1,5,4,7,8,2,3,1,4,6,5,4,4,1,5,4,7,8,2,3,1,4,6,5,4,4,2,3'],
                ];
            @endphp

            @foreach($widgets as $widget)
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card top_widget">
                    <div class="body">
                        <div class="icon"><i class="fa {{ $widget['icon'] }}"></i> </div>
                        <div class="content">
                            <div class="text mb-2 text-uppercase">{{ $widget['title'] }}</div>
                            <h4 class="number mb-0">{{ $widget['value'] }} <span class="font-12 text-muted"><i class="fa fa-level-up"></i> {{ $widget['percent'] }}</span></h4>
                            <small class="text-muted">Analytics for last Month</small>
                        </div>
                    </div>
                    <div class="sparkline" data-type="line" data-spot-Radius="0" data-offset="90" data-width="100%" data-height="50px"
                    data-line-Width="1" data-line-Color="{{ $widget['color'] }}" data-fill-Color="{{ $widget['color'] }}">{{ $widget['data'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        
    </div>

@endsection

@section('scripts')
<script>
    // Inisialisasi Chart/Sparkline di sini jika diperlukan
</script>
@endsection