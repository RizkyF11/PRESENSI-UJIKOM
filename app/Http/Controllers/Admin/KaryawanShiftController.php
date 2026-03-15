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
            'shifts' => Shift::where('is_active', true)->get(),
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

        //pastikan shift masih aktif
        $shift = Shift::where('id', $request->shift_id)
            ->where('is_active', true)
            ->firstOrFail();

        DB::transaction((function () use ($request) {

            $tanggalMulai = Carbon::parse($request->tanggal_mulai);

            // tutup semua shift lama yang masih aktif
            KaryawanShift::where('karyawan_id', $request->karyawan_id)
            ->where(function ($q) use ($tanggalMulai) {
                $q->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', $tanggalMulai);
            })
            ->update([
                'tanggal_selesai' => $tanggalMulai->copy()->subDay()
            ]);

            //simpan shift baru
            KaryawanShift::create([
                'karyawan_id'   => $request->karyawan_id,
                'shift_id'      => $request->shift_id,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $request->tanggal_selesai
            ]);

            // reset absensi hari ini jika shift diganti hari ini
            if ($tanggalMulai->isToday()) {
                \App\Models\Absensi::where('karyawan_id', $request->karyawan_id)
                    ->whereDate('tanggal', now())
                    ->delete();
            }
        }));

        return redirect()->back()->with('success', 'Assign shift berhasil disimpan');
    }

    public function destroy($id)
    {
        $data = KaryawanShift::findOrFail($id);

        $data->delete();

        return redirect()->back()->with('success', 'Assign shift berhasil dihapus');
    }
}
