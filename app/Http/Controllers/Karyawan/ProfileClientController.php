<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class ProfileClientController extends Controller
{
    /**
     * Menampilkan profile karyawan
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;

        return view('karyawan_fe.profile.index', compact('user', 'karyawan'));
    }

    /**
     * Menampilkan form edit profile
     */
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;

        return view('karyawan_fe.profile.edit', compact('user', 'karyawan'));
    }

    /**
     * Update profile karyawan (email & password)
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Validasi
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Update email
        $user->email = $validated['email'];

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('karyawan.profile.show')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Generate avatar berdasarkan nama
     */
    public static function generateAvatar($nama)
    {
        $initials = '';
        $parts = explode(' ', trim($nama));

        if (count($parts) > 0) {
            $initials = strtoupper(substr($parts[0], 0, 1));
        }

        if (count($parts) > 1) {
            $initials .= strtoupper(substr(end($parts), 0, 1));
        }

        return $initials;
    }

    /**
     * Mendapatkan warna avatar konsisten berdasarkan nama
     */
    public static function getAvatarColor($nama)
    {
        $colors = [
            'bg-red-500',
            'bg-orange-500',
            'bg-yellow-500',
            'bg-green-500',
            'bg-teal-500',
            'bg-blue-500',
            'bg-indigo-500',
            'bg-purple-500',
            'bg-pink-500',
            'bg-rose-500',
        ];

        $hash = crc32($nama) % count($colors);

        return $colors[$hash];
    }
} 