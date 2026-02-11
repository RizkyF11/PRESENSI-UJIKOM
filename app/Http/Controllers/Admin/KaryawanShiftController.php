<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\KaryawanShift;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use function Symfony\Component\Clock\now;

class KaryawanShiftController extends Controller
{
    // tampilkan form assign shift ke karyawan
    public function index()
    {
        return view('admin.karyawan_shift.index', [
            'karyawan' => Karyawan::all(),
            'shifts' => Shift::all(),
            'data' => KaryawanShift::with(['karyawan', 'shift'])->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id'      => 'required|exists:karyawan,id',
            'shift_id'         => 'required|exists:shift,id',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        // 🔴 Tutup shift lama jika masih aktif
        KaryawanShift::where('karyawan_id', $request->karyawan_id)
            ->where('tanggal_selesai', '>=', $request->tanggal_mulai)
            ->update([
                'tanggal_selesai' => Carbon::parse($request->tanggal_mulai)->subDay()
            ]);

        // 🟢 Simpan shift baru
        KaryawanShift::create([
            'karyawan_id'     => $request->karyawan_id,
            'shift_id'        => $request->shift_id,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->back()->with('success', 'Shift berhasil di-assign');
    }

    public function destroy($id)
    {
        $data = KaryawanShift::findOrFail($id);

        $data->delete();

        return redirect()->back()->with('success', 'Assign shift berhasil dihapus');
    }
}
