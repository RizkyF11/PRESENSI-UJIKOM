<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class KaryawanController extends Controller
{
    /**
     * menampilkan data karyawan.
     */
    public function index()
    {
        // Mengambil user dengan role karyawan beserta data detailnya
        $data = User::with('karyawan')->where('role', 'karyawan')->latest()->get();
        return view('admin.karyawan.index', compact('data'));
    }

    /**
     * Form tambah karyawan
     */
    public function create()
    {
        return view('admin.karyawan.form', ['karyawan' => null]);
    }

    /**
     * Simpan data karyawan baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Gabungan (Termasuk Password)
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6', // Tambahkan validasi password
            'nip'      => 'required|numeric|unique:karyawan,nip',
            'jabatan'  => 'required|string|max:100',
            'no_hp'    => 'required|numeric|digits_between:10,15',
            'alamat'   => 'required|string|min:10',
        ]);

        // 2. Transaksi Database agar aman (All or Nothing)
        DB::beginTransaction();

        try {
            // 3. Simpan ke Tabel Users
            $user = User::create([
                'nama'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password), // Hash password dari input
                'role'     => 'karyawan',
            ]);

            // 4. Simpan ke Tabel Karyawan via Relasi
            // User_id otomatis terisi berkat relasi hasOne di Model User
            $user->karyawan()->create([
                'nip'     => $request->nip,
                'jabatan' => $request->jabatan,
                'no_hp'   => $request->no_hp,
                'alamat'  => $request->alamat,
                'status'  => 'aktif',
            ]);

            // Selesai & Simpan permanen
            DB::commit();

            return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil didaftarkan!');
        } catch (\Exception $e) {
            // Batalkan jika ada eror
            DB::rollback();

            return redirect()->back()
                ->withInput() // Agar user tidak perlu ngetik ulang form yang sudah diisi
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $karyawan = User::with('karyawan')->where('role', 'karyawan')->findOrFail($id);
        return view('admin.karyawan.show', compact('karyawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $karyawan = User::with('karyawan')->findOrFail($id);
        return view('admin.karyawan.form', compact('karyawan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Ambil user beserta relasi karyawannya secara eksplisit
        $user = User::with('karyawan')->findOrFail($id);

        // Ambil ID detail karyawan untuk proses "ignore" di validasi unique
        // Jika relasi tidak ada (null), kita kasih null agar tidak error
        $karyawanId = $user->karyawan ? $user->karyawan->id : null;

        $request->validate([
            'nama'     => 'required|string|max:255',
            // Gunakan ignore agar tidak error saat email tidak diganti
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6', // Password opsional saat update
            'nip'      => ['required', 'numeric', Rule::unique('karyawan', 'nip')->ignore($karyawanId)],
            'jabatan'  => 'required|string|max:100',
            'no_hp'    => 'required|numeric|digits_between:10,15',
            'alamat'   => 'required|string|min:10',
            'status'   => 'required|in:aktif,non-aktif',
        ]);

        DB::beginTransaction();
        try {
            // 3. Update Data User (Akun)
            $userData = [
                'nama'  => $request->nama,
                'email' => $request->email,
            ];

            // Hanya update password jika diisi di form
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // 4. Update Data Detail Karyawan
            // Menggunakan updateOrCreate untuk berjaga-jaga jika data di tabel karyawan belum ada
            $user->karyawan()->updateOrCreate(
                ['user_id' => $user->id], // Cari berdasarkan user_id
                [
                    'nip'     => $request->nip,
                    'jabatan' => $request->jabatan,
                    'no_hp'   => $request->no_hp,
                    'alamat'  => $request->alamat,
                    'status'  => $request->status,
                ]
            );

            DB::commit();
            return redirect()->route('admin.karyawan.index')->with('success', 'Data karyawan diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            // Gunakan logger helper agar tidak perlu import class Log
            logger()->error("Update Error: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        DB::beginTransaction();
        try {
            //hapus detail karyawan dulu baru usernya
            $user->karyawan()->delete();
            $user->delete();

            DB::commit();
            return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal hapus karyawan.');
        }
    }
}
