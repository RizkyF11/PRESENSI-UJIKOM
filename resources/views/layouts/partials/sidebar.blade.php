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
                </li>
                <li class="col-6">
                    <small>Status</small>
                    <h6><span class="badge badge-success">Online</span></h6>
                </li>
            </ul>
        </div>

        <nav id="left-sidebar-nav" class="sidebar-nav">
            <ul id="main-menu" class="metismenu li_animation_delay">

                {{-- =============================================
                     SIDEBAR ADMIN
                ============================================= --}}
                @if(Auth::user()->role === 'admin')

                <li class="header">Main</li>
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa fa-tachometer"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('admin.point-rules.*') ? 'active' : '' }}">
                    <a href="#Gamification" class="has-arrow">
                        <i class="fa fa-trophy"></i>
                        <span>Gamification</span>
                    </a>

                    <ul>

                        <li class="{{ request()->routeIs('admin.point-rules.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.point-rules.index') }}">
                                Point Rules
                            </a>
                        </li>


                        <li class="{{ request()->routeIs('admin.flexibility-items.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.flexibility-items.index') }}">
                                Flexibility Items
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('admin.leaderboard.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.leaderboard.index') }}">
                                <span>Leaderboard</span>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.karyawan.*') || request()->routeIs('admin.shift.*') || request()->routeIs('admin.lokasi-kantor.*') ? 'active' : '' }}">
                    <a href="#MasterData" class="has-arrow"><i class="fa fa-database"></i><span>Master Data</span></a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.karyawan.index') }}">Data Karyawan</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.shift.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.shift.index') }}">Shift</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.lokasi-kantor.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.lokasi-kantor.index') }}">Lokasi Kantor</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.qrcode.*') || request()->routeIs('admin.karyawan_shift.*') || request()->routeIs('admin.absensi.*') || request()->routeIs('admin.izin.*') || request()->routeIs('admin.cuti.*') ? 'active' : '' }}">
                    <a href="#Absensi" class="has-arrow"><i class="fa fa-calendar-check-o"></i><span>Absensi</span></a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.qrcode.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.qrcode.index') }}">Generate QR Code</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.karyawan_shift.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.karyawan_shift.index') }}">Assign Shift</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.absensi.index') }}">Riwayat Absensi</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.izin.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.izin.index') }}">Pengajuan Izin</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.cuti.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.cuti.index') }}">Pengajuan Cuti</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('admin.assessment-categories.*') || request()->routeIs('admin.assessment.laporan') ? 'active' : '' }}">
                    <a href="#Assessments" class="has-arrow"><i class="fa fa-bar-chart"></i><span>Assessments</span></a>
                    <ul>
                        <li class="{{ request()->routeIs('admin.assessment-categories.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.assessment-categories.index') }}">Kategori Penilaian</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.assessment.laporan') ? 'active' : '' }}">
                            <a href="{{ route('admin.assessment.laporan') }}">Laporan Penilaian</a>
                        </li>
                    </ul>
                </li>

                {{-- =============================================
                     SIDEBAR MANAGER
                ============================================= --}}
                @elseif(Auth::user()->role === 'manager')

                <li class="header">Main</li>
                <li class="{{ request()->routeIs('manager.dashboard') || request()->routeIs('manager.assessment*') ? 'active' : '' }}">
                    <a href="{{ route('manager.dashboard') }}">
                        <i class="fa fa-tachometer"></i>
                        <span>Dashboard Penilaian</span>
                    </a>
                </li>

                @endif

            </ul>
        </nav>
    </div>
</div>