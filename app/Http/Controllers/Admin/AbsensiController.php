<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Izin;
use App\Models\Cuti;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // ===============================
        // DEFAULT PERIODE (BULAN INI)
        // ===============================
        $tanggalMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::now()->startOfMonth();

        $tanggalSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::now()->endOfMonth();

        // ===============================
        // QUERY ABSENSI 
        // ===============================
        $query = Absensi::with(['karyawan.user', 'shift'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal', 'desc');

        // Filter Karyawan
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $absensi = $query->paginate(15)->withQueryString();

        // ===============================
        // AMBIL DATA IZIN & CUTI APPROVED
        // ===============================

        // Izin
        $izin = Izin::where('status', 'approved')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function ($q2) use ($tanggalMulai, $tanggalSelesai) {
                        $q2->where('tanggal_mulai', '<=', $tanggalMulai)
                            ->where('tanggal_selesai', '>=', $tanggalSelesai);
                    });
            })
            ->get();

        $cuti = Cuti::where('status', 'approved')
            ->where(function ($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function ($q2) use ($tanggalMulai, $tanggalSelesai) {
                        $q2->where('tanggal_mulai', '<=', $tanggalMulai)
                            ->where('tanggal_selesai', '>=', $tanggalSelesai);
                    });
            })
            ->get();

        $karyawan = Karyawan::with('user')
            ->join('users', 'karyawan.user_id', '=', 'users.id')
            ->orderBy('users.nama', 'asc')
            ->select('karyawan.*')
            ->get();

        return view('admin.absensi.index', compact(
            'absensi',
            'karyawan',
            'tanggalMulai',
            'tanggalSelesai',
            'izin',
            'cuti'
        ));
    }

    // =====================================================
    // DELETE PER BARIS
    // =====================================================
    public function destroy($id)
    {
        $absensi = Absensi::findOrFail($id);
        $absensi->delete();

        return redirect()->route('admin.absensi.index')
            ->with('success', 'Data absensi berhasil dihapus.');
    }

    // =====================================================
    // DELETE MASSAL SESUAI FILTER 
    // =====================================================
    public function destroyAll(Request $request)
    {
        $tanggalMulai = $request->filled('tanggal_mulai')
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : Carbon::now()->startOfMonth();

        $tanggalSelesai = $request->filled('tanggal_selesai')
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : Carbon::now()->endOfMonth();

        $query = Absensi::whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $jumlah = $query->count();
        $query->delete();

        return redirect()->route('admin.absensi.index')
            ->with('success', $jumlah . " data absensi berhasil dihapus.");
    }

    public function export(Request $request)
    {
        // VALIDASI
        if (!$request->filled('tanggal_mulai') || !$request->filled('tanggal_selesai')) {
            return redirect()->back()->with('error', 'Tanggal harus dipilih terlebih dahulu');
        }
        
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;
        $karyawanId = $request->karyawan_id;


        $namaFile = 'rekap-absensi-' .
            Carbon::parse($tanggalMulai)->format('d-m-Y') .
            '_sampai_' .
            Carbon::parse($tanggalSelesai)->format('d-m-Y') .
            '.xlsx';

        return Excel::download(
            new RekapAbsensiExport($tanggalMulai, $tanggalSelesai, $karyawanId),
            $namaFile
        );
    }
}
