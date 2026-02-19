<div id="left-sidebar" class="sidebar">
    <button type="button" class="btn-toggle-offcanvas"><i class="fa fa-arrow-left"></i></button>
    <div class="sidebar-scroll">
        <div class="user-account">
            <img src="{{ asset('assets/images/user.png') }}" class="rounded-circle user-photo" alt="User Profile Picture">
            <div class="dropdown">
                <span>Welcome,</span>
                <a href="javascript:void(0);" class="dropdown-toggle user-name" data-toggle="dropdown">
                    <strong>{{ Auth::user()->nama }}</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-right account">
                    <li><a href="{{ route('profile.edit') }}"><i class="icon-user"></i>My Profile</a></li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                            <i class="icon-power"></i>Logout
                        </a>
                        <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
            <hr>
            <ul class="row list-unstyled">
                <li class="col-6">
                    <small>Role</small>
                    <h6>{{ ucfirst(Auth::user()->role ?? 'Admin') }}</h6>
                    </h6>
                <li class="col-6">
                    <small>Status</small>
                    <h6><span class="badge badge-success">Online</span></h6>
                </li>
            </ul>
        </div>

        <nav id="left-sidebar-nav" class="sidebar-nav">
            <ul id="main-menu" class="metismenu li_animation_delay">
                <li class="header">Main</li>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"><i class="fa fa-tachometer"></i><span>Dashboard</span></a>
                </li>

                <!-- GROUP MASTER DATA -->
                <li class="header">Master Data</li>
                <li class="{{ request()->is('admin/karyawan') || request()->is('admin/karyawan/*') ? 'active' : '' }}">
                    <a href="{{ route('admin.karyawan.index') }}"><i class="fa fa-users"></i><span>Data Karyawan</span></a>
                </li>
                <li></li>
                <li class="{{ request()->is('admin/shift') || request()->is('admin/shift/*') ? 'active' : '' }}">
                    <a href="{{ route('admin.shift.index') }}"><i class="fa fa-clock-o"></i><span>Shift</span></a>
                </li>
                <li class="{{ request()->is('admin/lokasi-kantor') || request()->is('admin/lokasi-kantor/*') ? 'active' : '' }}">
                    <a href="{{ route('admin.lokasi-kantor.index') }}"><i class="fa fa-map-marker"></i><span>Lokasi Kantor</span></a>
                </li>

                <!-- GROUP ABSENSI -->
                <li class="header">Absensi</li>
                <li class="{{ request()->routeIs('admin.qrcode*') ? 'active' : '' }}">
                    <a href="{{ route('admin.qrcode.index') }}"><i class="fa fa-qrcode"></i><span>Generate QR Code</span></a>
                </li>
                <li class="{{ request()->routeIs('admin.karyawan_shift*') ? 'active' : '' }}">
                    <a href="{{ route('admin.karyawan_shift.index') }}"><i class="fa fa-list-alt"></i><span>Assign Shift</span></a>
                </li>
            </ul>
        </nav>
    </div>
</div>