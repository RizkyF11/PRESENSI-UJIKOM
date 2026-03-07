<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use App\Models\Izin;
use App\Models\Karyawan;
use Carbon\Carbon;

class IzinController extends Controller
{

    // =====================================================
    // LIST DATA IZIN
    // =====================================================

    public function index(Request $request)
    {
        $query = Izin::with('karyawan.user')
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

        $izin = $query->paginate(10)->withQueryString();

        $karyawan = Karyawan::with('user')
            ->join('users', 'karyawan.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('karyawan.*')
            ->get();

        return view('admin.izin.index', compact(
            'izin',
            'karyawan'
        ));
    }


    // =====================================================
    // APPROVE IZIN
    // =====================================================

    public function approve($id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->status == 'approved') {
            return back()->with('info', 'Izin sudah disetujui sebelumnya.');
        }

        //update status izin
        $izin->update([
            'status' => 'approved'
        ]);

        //tanggal mulai dan selesai
        $tanggalMulai = Carbon::parse($izin->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($izin->tanggal_selesai);

        //looping setiap tanggal izin
        while ($tanggalMulai <= $tanggalSelesai) {

            //cek apakah absensi sudah ada
            $sudahAda = Absensi::where('karyawan_id', $izin->karyawan_id)
                ->whereDate('tanggal', $tanggalMulai->toDateString())
                ->exists();

            if (!$sudahAda) {
                Absensi::create([
                    'karyawan_id' => $izin->karyawan_id,
                    'tanggal' => $tanggalMulai->toDateString(),
                    'status' => 'izin',
                ]);
            }

            $tanggalMulai->addDay();
        }

        return redirect()->back()->with('success', 'Pengajuan izin berhasil disetujui.');
    }


    // =====================================================
    // REJECT IZIN
    // =====================================================

    public function reject($id)
    {
        $izin = Izin::findOrFail($id);

        if ($izin->status == 'reject') {
            return back()->with('info', 'Izin sudah ditolak sebelumnya.');
        }

        $izin->update([
            'status' => 'reject'
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin berhasil ditolak.');
    }


    // =====================================================
    // HAPUS IZIN
    // =====================================================

    public function destroy($id)
    {
        $izin = Izin::findOrFail($id);

        $izin->delete();

        return redirect()->back()->with('success', 'Data izin berhasil dihapus.');
    }
}