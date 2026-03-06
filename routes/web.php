<?php

use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\AdminQrController;
use App\Http\Controllers\Admin\KaryawanShiftController;
use App\Http\Controllers\Admin\LokasiKantorController;
use App\Http\Controllers\Admin\ShiftController;
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

        return redirect('/'); // Default jika role tidak jelas
    })->name('dashboard');
    // ----------------------------------------

    // 1. ROUTE KHUSUS ADMIN (Pakai Gate 'admin')
    Route::middleware(['auth', 'can:admin'])
        ->prefix('admin') // URL jadi /admin/dashboard, dll
        ->as('admin.') // NAMA ROUTE jadi admin.dashboard, admin.karyawan.index, dll
        ->group(function () {
            Route::get('/dashboard', function () {
                return view('admin.dashboard'); // Arahkan ke view dashboard admin kamu
            })->name('dashboard'); // Karena ada ->as('admin.'), ini otomatis jadi admin.dashboard

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

            // Route Absensi
            Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
            Route::delete('absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
            Route::delete('absensi-delete-all', [AbsensiController::class, 'destroyAll'])->name('absensi.destroyAll');

            //export excel
            Route::get('absensi/export', [AbsensiController::class, 'export'])
                ->name('absensi.export');
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
    });

    // Route Profile (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
