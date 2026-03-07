<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sistem Absensi Karyawan</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Bawaan (Bootstrap & FontAwesome) yang dibutuhkan dashboard/child views lama agar styling tidak pecah -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">
    <!-- Catatan: SweetAlert CSS global dinonaktifkan di layout karyawan -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert.css') }}" /> -->

    <!-- Iconify for modern UI icons -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <!-- Tailwind CSS (via CDN karena Anda tidak menjalankan npm run dev) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            // Tailwind mematikan preflight supaya tidak merusak styling tabel & form bawaan Bootstrap
            corePlugins: {
                preflight: false,
            },
            theme: {
                extend: {
                    colors: {
                        teal: {
                            500: '#4DB6AC',
                            600: '#2C7A7B',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Custom Style Layout Mobile Apps -->
    <style>
        body {
            background-color: #E5E7EB;
            /* Background luar layar desktop */
            -webkit-font-smoothing: antialiased;
        }

        /* Container untuk behavior aplikasi mobile di desktop */
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            background-color: #F5F7FA;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        /* Menyembunyikan scrollbar tapi tetap bisa scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /* Menyesuaikan card dari bootstrap agar lebih modern mirip mobile app */
        .mobile-container .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .nav-item-bottom {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6B7280;
            text-decoration: none !important;
            transition: all 0.2s;
            font-size: 11px;
            font-weight: 500;
        }

        .nav-item-bottom:hover,
        .nav-item-bottom.active {
            color: #4DB6AC;
        }

        /* Navbar Bottom Styling Custom Floating Button */
        .scan-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scan-floating-btn {
            position: absolute;
            top: -25px;
            /* Angkat tombol ke atas supaya floating */
            width: 60px;
            height: 60px;
            background-color: #4DB6AC;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 10px rgba(77, 182, 172, 0.4);
            border: 5px solid #F5F7FA;
            text-decoration: none !important;
            transition: all 0.2s;
        }

        .scan-floating-btn:hover {
            background-color: #2C7A7B;
            color: white;
            transform: scale(1.05);
        }

        .scan-floating-btn.active {
            background-color: #2C7A7B;
            border-color: white;
        }

        .scan-text {
            margin-top: 35px;
            /* Geser teks scan ke bawah tombol */
            font-size: 11px;
            font-weight: bolder;
            color: #6B7280;
        }

        .scan-text.active {
            color: #4DB6AC;
        }
    </style>
</head>

<body>
    <!-- App Container (Mobile App Layout) -->
    <div class="mobile-container overflow-hidden">

        <!-- Header (Fixed Top) -->
        <header class="fixed top-0 w-full max-w-[480px] z-50 rounded-b-xl shadow-sm border-b border-gray-200" style="background: white;">
            <div class="px-4 py-3 flex justify-between items-center w-full relative">
                @hasSection('header-left')
                @yield('header-left')
                @else
                <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-teal-500 text-white font-bold text-lg shadow-sm">
                        {{ auth()->check() ? strtoupper(substr(auth()->user()->name ?? auth()->user()->nama, 0, 1)) : 'U' }}
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-[15px] font-bold text-gray-800 leading-tight mb-0">
                            {{ auth()->check() ? (auth()->user()->name ?? auth()->user()->nama) : 'Nama User' }}
                        </h1>
                        <p class="text-[12px] font-medium text-gray-500 mb-0">
                            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </header>

        <!-- Content Area (Scrollable) -->
        <!-- pb-[110px] memberikan ruang di bawah agar konten tidak tertutup bottom nav scan button -->
        <main class="flex-1 overflow-y-auto no-scrollbar pt-[80px] pb-[110px] px-3 w-full">
            @yield('content')
        </main>

        <!-- Bottom Navigation (Fixed Bottom) -->
        <nav class="fixed bottom-0 w-full max-w-[480px] z-60 bg-white border-t border-gray-200 shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.05)] rounded-t-xl" style="height: 65px;">
            <div style="display: flex; align-items: center; justify-content: space-around; height: 100%; position: relative;">

                <!-- Dashboard -->
                <a href="{{ route('karyawan.dashboard') }}" class="nav-item-bottom {{ request()->routeIs('karyawan.dashboard') ? 'active' : '' }}">
                    <span class="iconify mb-1" data-icon="{{ request()->routeIs('karyawan.dashboard') ? 'heroicons:home-solid' : 'heroicons:home' }}" data-width="24"></span>
                    <span>Home</span>
                </a>

                <!-- Riwayat -->
                <a href="#" class="nav-item-bottom">
                    <span class="iconify mb-1" data-icon="heroicons:clock" data-width="24"></span>
                    <span>Riwayat</span>
                </a>

                <!-- Scan QR Floating Button (Center) -->
                <div class="scan-wrapper">
                    <a href="{{ route('karyawan.scan') }}" class="scan-floating-btn {{ request()->routeIs('karyawan.scan') ? 'active' : '' }}">
                        <span class="iconify" data-icon="heroicons:qr-code" data-width="28"></span>
                    </a>
                    <span class="scan-text {{ request()->routeIs('karyawan.scan') ? 'active' : '' }}">Scan</span>
                </div>

                <!-- Profil -->
                <a href="{{ route('profile.edit') }}" class="nav-item-bottom {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <span class="iconify mb-1" data-icon="{{ request()->routeIs('profile.edit') ? 'heroicons:user-solid' : 'heroicons:user' }}" data-width="24"></span>
                    <span>Profil</span>
                </a>

                <!-- Logout -->
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();" class="nav-item-bottom text-danger">
                    <span class="iconify mb-1" data-icon="heroicons:arrow-right-on-rectangle" data-width="24"></span>
                    <span>Keluar</span>
                </a>
                <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

            </div>
        </nav>
    </div>

    <!-- Scripts JQuery & Bootstrap untuk fitur JS dari template lama (Modal, Dropdown, dll) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- CDN Alert dihapus agar pure menggunakan Modern UI Flowbite Component -->
    <!-- <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> -->

    <!-- Komponen Toast Alert Modern (Bukan SweetAlert) -->
    @include('components.alert-modern')
    @stack('scripts')
</body>

</html>