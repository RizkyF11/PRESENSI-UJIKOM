<?php

use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\AdminQrController;
use App\Http\Controllers\Admin\CutiController;
use App\Http\Controllers\Admin\FlexibilityItemController;
use App\Http\Controllers\Admin\IzinController;
use App\Http\Controllers\Admin\KaryawanShiftController;
use App\Http\Controllers\Admin\LokasiKantorController;
use App\Http\Controllers\Admin\PointRuleController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\AssessmentCategoryController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentQuestionController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\ManagerDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// GROUP ROUTE YANG SUDAH LOGIN
Route::middleware('auth')->group(function () {

    // --- TAMBAHKAN ROUTE PENENGAH DI SINI ---
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'karyawan') {
            return redirect()->route('karyawan.dashboard');
        }

        if ($role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        return redirect('/'); // Default jika role tidak jelas
    })->name('dashboard');
    // ----------------------------------------

    // 1. ROUTE KHUSUS ADMIN (Pakai Gate 'admin')
    Route::middleware(['auth', 'can:admin'])
        ->prefix('admin') // URL jadi /admin/dashboard, dll
        ->as('admin.') // NAMA ROUTE jadi admin.dashboard, admin.karyawan.index, dll
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Resource Controller untuk Karyawan
            Route::resource('karyawan', KaryawanController::class);

            // Route QR Code
            Route::get('/qrcode', [AdminQrController::class, 'index'])->name('qrcode.index');
            Route::get('/qrcode/generate', [AdminQrController::class, 'generate'])->name('qrcode.generate');

            // Route pivot karyawan Shift
            Route::get('karyawan-shift', [KaryawanShiftController::class, 'index'])
                ->name('karyawan_shift.index');

            Route::post('karyawan-shift', [KaryawanShiftController::class, 'store'])
                ->name('karyawan_shift.store');

            Route::delete('karyawan-shift/{id}', [KaryawanShiftController::class, 'destroy'])
                ->name('karyawan_shift.destroy');

            // Route Lokasi Kantor
            Route::resource('lokasi-kantor', LokasiKantorController::class)->except(['show']);

            // Route Shift
            Route::resource('shift', ShiftController::class);
            Route::patch('shift/{id}/deactivate', [ShiftController::class, 'deactivate'])
                ->name('shift.deactivate');

            Route::patch('shift/{id}/activate', [ShiftController::class, 'activate'])
                ->name('shift.activate');

            // Route Absensi
            Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
            Route::delete('absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
            Route::delete('absensi-delete-all', [AbsensiController::class, 'destroyAll'])->name('absensi.destroyAll');

            //export excel absensi
            Route::get('absensi/export', [AbsensiController::class, 'export'])
                ->name('absensi.export');

            //expoert pdf rekap absensi
            Route::get(
                '/absensi/export-pdf',
                [AbsensiController::class, 'exportPdf']
            )->name('absensi.exportPdf');

            // Route Izin
            Route::get('izin', [IzinController::class, 'index'])->name('izin.index');

            Route::post('izin/{id}/approve', [IzinController::class, 'approve'])->name('izin.approve');

            Route::post('izin/{id}/reject', [IzinController::class, 'reject'])->name('izin.reject');

            Route::delete('izin/{id}', [IzinController::class, 'destroy'])->name('izin.destroy');

            // Route Cuti
            Route::get('cuti', [CutiController::class, 'index'])->name('cuti.index');

            Route::post('cuti/{id}/approve', [CutiController::class, 'approve'])->name('cuti.approve');

            Route::post('cuti/{id}/reject', [CutiController::class, 'reject'])->name('cuti.reject');

            Route::delete('cuti/{id}', [CutiController::class, 'destroy'])->name('cuti.destroy');

            // assessment categories
            Route::resource('assessment-categories', AssessmentCategoryController::class)->except(['show']);
            Route::patch(
                'assessment-categories/{assessmentCategory}/toggle',
                [AssessmentCategoryController::class, 'toggleActive']
            )
                ->name('assessment-categories.toggle');

            //assessment questions
            // 
            Route::resource(
                'assessment-categories.questions',
                AssessmentQuestionController::class
            )->except(['show'])->names([
                'index'   => 'assessment-questions.index',
                'create'  => 'assessment-questions.create',
                'store'   => 'assessment-questions.store',
                'edit'    => 'assessment-questions.edit',
                'update'  => 'assessment-questions.update',
                'destroy' => 'assessment-questions.destroy',
            ]);
            Route::patch(
                'assessment-categories/{assessmentCategory}/questions/{question}/toggle',
                [AssessmentQuestionController::class, 'toggleActive']
            )->name('assessment-questions.toggle');

            //assessment laporan read only - admin only
            Route::get('assessment/laporan', [AssessmentController::class, 'laporanAdmin'])
                ->name('assessment.laporan');

            // Admin bisa hapus penilaian
            Route::delete('assessment/{assessment}', [AssessmentController::class, 'destroy'])
                ->name('assessment.destroy');

            // point rules
            Route::resource('point-rules', PointRuleController::class)->except(['show']);

            // flexibility items
            Route::resource('flexibility-items', FlexibilityItemController::class)->except(['show', 'destroy']);

            // Route untuk activate/deactivate item fleksibilitas
            Route::patch(
                'flexibility-items/{id}/deactivate',
                [FlexibilityItemController::class, 'deactivate']
            )->name('flexibility-items.deactivate');

            Route::patch(
                'flexibility-items/{id}/activate',
                [FlexibilityItemController::class, 'activate']
            )->name('flexibility-items.activate');

            //leaderboard
            Route::get('/leaderboard', [LeaderboardController::class, 'index'])
                ->name('leaderboard.index');
        });




    // 2. ROUTE KHUSUS KARYAWAN (Pakai Gate 'karyawan')
    Route::middleware('can:karyawan')->group(function () {
        Route::get('/karyawan/dashboard', [App\Http\Controllers\Karyawan\AbsensiController::class, 'index'])
            ->name('karyawan.dashboard');

        // Route Scan Absen
        Route::get('/karyawan/scan', function () {
            return view('karyawan_fe.scan');
        })->name('karyawan.scan');

        // Route Proses Scan (Store)
        Route::post('/karyawan/scan', [App\Http\Controllers\Karyawan\AbsensiController::class, 'storeScan'])
            ->name('karyawan.scan.store');

        // Route Izin Karyawan
        Route::get('/karyawan/izin', [\App\Http\Controllers\Karyawan\IzinController::class, 'index'])->name('karyawan.izin.index');
        Route::post('/karyawan/izin', [\App\Http\Controllers\Karyawan\IzinController::class, 'store'])->name('karyawan.izin.store');

        Route::get('/karyawan/izin/{id}/edit', [\App\Http\Controllers\Karyawan\IzinController::class, 'edit'])->name('karyawan.izin.edit');
        Route::put('/karyawan/izin/{id}', [\App\Http\Controllers\Karyawan\IzinController::class, 'update'])->name('karyawan.izin.update');

        Route::delete('/karyawan/izin/{id}', [\App\Http\Controllers\Karyawan\IzinController::class, 'destroy'])->name('karyawan.izin.destroy');

        // Route Cuti Karyawan
        Route::get('/karyawan/cuti', [\App\Http\Controllers\Karyawan\CutiController::class, 'index'])->name('karyawan.cuti.index');
        Route::post('/karyawan/cuti', [\App\Http\Controllers\Karyawan\CutiController::class, 'store'])->name('karyawan.cuti.store');

        Route::get('/karyawan/cuti/{id}/edit', [\App\Http\Controllers\Karyawan\CutiController::class, 'edit'])->name('karyawan.cuti.edit');
        Route::put('/karyawan/cuti/{id}', [\App\Http\Controllers\Karyawan\CutiController::class, 'update'])->name('karyawan.cuti.update');

        Route::delete('/karyawan/cuti/{id}', [\App\Http\Controllers\Karyawan\CutiController::class, 'destroy'])->name('karyawan.cuti.destroy');

        // Route Riwayat Absensi
        Route::get('/karyawan/riwayat', [\App\Http\Controllers\Karyawan\RiwayatAbsensiController::class, 'index'])->name('karyawan.riwayat.index');

        // Route Jadwal Shift
        Route::get('/karyawan/jadwal-shift', [\App\Http\Controllers\Karyawan\JadwalShiftController::class, 'index'])->name('karyawan.jadwal.index');

        // Route Profile Karyawan
        Route::get('/karyawan/profile', [\App\Http\Controllers\Karyawan\ProfileClientController::class, 'show'])->name('karyawan.profile.show');
        Route::get('/karyawan/profile/edit', [\App\Http\Controllers\Karyawan\ProfileClientController::class, 'edit'])->name('karyawan.profile.edit');
        Route::put('/karyawan/profile/update', [\App\Http\Controllers\Karyawan\ProfileClientController::class, 'update'])->name('karyawan.profile.update');


        // Rapor Karyawan (Read Only)

        Route::get('/karyawan/rapor', [RaporController::class, 'index'])
            ->name('karyawan.rapor');
    });


    Route::middleware(['auth', 'can:manager'])
        ->prefix('manager')
        ->as('manager.')
        ->group(function () {

            // Dashboard manager
            Route::get('/dashboard', [AssessmentController::class, 'index'])
                ->name('dashboard');

            // Penilaian karyawan
            Route::resource('assessment', AssessmentController::class)->except(['show']);
        });

    // Route Profile (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
