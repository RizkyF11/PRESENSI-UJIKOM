<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\AdminQrController;
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

            Route::resource('karyawan', KaryawanController::class);
            Route::get('/qrcode', [AdminQrController::class, 'index'])->name('qrcode.index');
            Route::get('/qrcode/generate', [AdminQrController::class, 'generate'])->name('qrcode.generate');
        });

    // 2. ROUTE KHUSUS KARYAWAN (Pakai Gate 'karyawan')
    Route::middleware('can:karyawan')->group(function () {
        Route::get('/karyawan/dashboard', function () {
            return view('karyawan_fe.dashboard'); // Buat view khusus karyawan
        })->name('karyawan.dashboard');

        // Nanti taruh route scan absen di sini
    });

    // Route Profile (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
