<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{

    // =====================================================
    // LIST IZIN MILIK KARYAWAN
    // =====================================================

    public function index()
    {
        $karyawan = Karyawan::where('user_id', Auth::id())->first();

        $izin = Izin::where('karyawan_id', $karyawan->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('karyawan_fe.pengajuan_izin.index', compact('izin'));
    }


    // =====================================================
    // SIMPAN PENGAJUAN IZIN
    // =====================================================

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
        ]);

        $karyawan = Karyawan::where('user_id', Auth::id())->first();

        Izin::create([
            'karyawan_id' => $karyawan->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin berhasil dikirim.');
    }


    // =====================================================
    // FORM EDIT IZIN
    // =====================================================

    public function edit($id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->status != 'pending') {
            return redirect()->back()->with('error', 'Izin yang sudah diproses tidak bisa diedit.');
        }

        return redirect()->route('karyawan.izin.index')->with('open_edit_modal', $izin->id);
    }


    // =====================================================
    // UPDATE IZIN
    // =====================================================

    public function update(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->status != 'pending') {
            return redirect()->back()->with('error', 'Izin yang sudah diproses tidak bisa diedit.');
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:500',
        ]);

        $izin->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan
        ]);

        return redirect()->route('karyawan.izin.index')
            ->with('success', 'Pengajuan izin berhasil diperbarui.');
    }


    // =====================================================
    // HAPUS IZIN
    // =====================================================

    public function destroy($id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->status != 'pending') {
            return redirect()->back()->with('error', 'Izin yang sudah diproses tidak bisa dihapus.');
        }

        $izin->delete();

        return redirect()->back()->with('success', 'Pengajuan izin berhasil dihapus.');
    }
}
