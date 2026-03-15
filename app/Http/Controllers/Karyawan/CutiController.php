<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class CutiController extends Controller
{

    // =====================================================
    // LIST CUTI MILIK KARYAWAN
    // =====================================================

    public function index()
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->first();

        $cuti = Cuti::where('karyawan_id', $karyawan->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('karyawan_fe.pengajuan_cuti.index', compact('cuti'));
    }


    // =====================================================
    // SIMPAN PENGAJUAN CUTI
    // =====================================================

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
        ]);

        $karyawan = Karyawan::where('user_id', Auth::id())->first();

        Cuti::create([
            'karyawan_id' => $karyawan->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil dikirim.');
    }


    // =====================================================
    // FORM EDIT CUTI
    // =====================================================

    public function edit($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->back()->with('error', 'Cuti yang sudah diproses tidak bisa diedit.');
        }

        return redirect()->route('karyawan.cuti.index')->with('open_edit_modal', $cuti->id);
    }


    // =====================================================
    // UPDATE CUTI
    // =====================================================

    public function update(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->back()->with('error', 'Cuti yang sudah diproses tidak bisa diedit.');
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
        ]);

        $cuti->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan
        ]);

        return redirect()->route('karyawan.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil diperbarui.');
    }


    // =====================================================
    // HAPUS CUTI
    // =====================================================

    public function destroy($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status != 'pending') {
            return redirect()->back()->with('error', 'Cuti yang sudah diproses tidak bisa dihapus.');
        }

        $cuti->delete();

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil dihapus.');
    }
}
