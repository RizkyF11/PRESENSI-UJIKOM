<!-- File master/ bingkai utama -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="{{ asset('assets/admin/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/charts-c3/plugin.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/admin/css/main.css') }}">
</head>

<body data-theme="light" class="font-nunito">
    <div id="wrapper" class="theme-cyan">

        <!-- Page Loader -->
        <!-- <div class="page-loader-wrapper">
            <div class="loader">
                <div class="m-t-30"><img src="{{ asset('assets/admin/images/logo-icon.svg') }}" width="48" height="48" alt="Iconic"></div>
                <p>Please wait...</p>
            </div>
        </div> -->

        @include('admin.layouts.partials.navbar')

        @include('admin.layouts.partials.sidebar')

        <div id="main-content">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('assets/admin/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/admin/bundles/vendorscripts.bundle.js') }}"></script>

    <script src="{{ asset('assets/admin/vendor/toastr/toastr.js') }}"></script>
    <script src="{{ asset('assets/admin/bundles/c3.bundle.js') }}"></script>

    <script src="{{ asset('assets/admin/bundles/mainscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/admin/js/index.js') }}"></script>

</body>

</html>