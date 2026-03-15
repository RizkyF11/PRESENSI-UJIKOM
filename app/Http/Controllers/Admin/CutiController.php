<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\Karyawan;
use Carbon\Carbon;
class CutiController extends Controller
{

    // =====================================================
    // LIST DATA CUTI
    // =====================================================

    public function index(Request $request)
    {
        $query = Cuti::with('karyawan.user')
            ->orderBy('created_at', 'desc');

        // filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // filter karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // filter tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {

            $mulai = Carbon::parse($request->tanggal_mulai);
            $selesai = Carbon::parse($request->tanggal_selesai);

            $query->where(function ($q) use ($mulai, $selesai) {

                $q->whereBetween('tanggal_mulai', [$mulai, $selesai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                    ->orWhere(function ($q2) use ($mulai, $selesai) {

                        $q2->where('tanggal_mulai', '<=', $mulai)
                            ->where('tanggal_selesai', '>=', $selesai);

                    });

            });
        }

        $cuti = $query->paginate(10)->withQueryString();

        $karyawan = Karyawan::with('user')
            ->join('users', 'karyawan.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('karyawan.*')
            ->get();

        return view('admin.cuti.index', compact(
            'cuti',
            'karyawan'
        ));
    }


    // =====================================================
    // APPROVE CUTI
    // =====================================================

    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status == 'approved') {
            return back()->with('info', 'Cuti sudah disetujui sebelumnya.');
        }

        //update status izin
        $cuti->update([
            'status' => 'approved'
        ]);

        //tanggal mulai dan selesai
        $tanggalMulai = Carbon::parse($cuti->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($cuti->tanggal_selesai);

        //looping setiap tanggal cuti
        while ($tanggalMulai <= $tanggalSelesai) {

            //cek apakah absensi sudah ada
            $sudahAda = Absensi::where('karyawan_id', $cuti->karyawan_id)
                ->whereDate('tanggal', $tanggalMulai->toDateString())
                ->exists();

            if (!$sudahAda) {
                Absensi::create([
                    'karyawan_id' => $cuti->karyawan_id,
                    'tanggal' => $tanggalMulai->toDateString(),
                    'status' => 'izin',
                ]);
            }

            $tanggalMulai->addDay();
        }

        return redirect()->back()->with('success', 'Pengajuan Cuti berhasil disetujui.');
    }


    // =====================================================
    // REJECT IZIN
    // =====================================================

    public function reject($id)
    {
        $cuti = Cuti::findOrFail($id);

        if ($cuti->status == 'reject') {
            return back()->with('info', 'Cuti sudah ditolak sebelumnya.');
        }

        $cuti->update([
            'status' => 'reject'
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin berhasil ditolak.');
    }


    // =====================================================
    // HAPUS IZIN
    // =====================================================

    public function destroy($id)
    {
        $cuti = Cuti::findOrFail($id);

        $cuti->delete();

        return redirect()->back()->with('success', 'Data cuti berhasil dihapus.');
    }
}