<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shift = Shift::latest()->get();
        return view('admin.shift.index', compact('shift'));
    }

    public function create() 
    {
        return view('admin.shift.form');
    }

    //STORE
    public function store(Request $request)
    {
        $request->validate([
            'nama_shift'    => 'required|string|max:255',
            'jam_masuk'     => 'required',
            'jam_keluar'    => 'required',
            'toleransi_menit' => 'required|integer|min:0',
        ]);

        Shift::create($request->all());

        return redirect()->route('admin.shift.index')->with('success', 'Shift berhasil ditambahkan');
    }

    //EDIT
    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('admin.shift.form', compact('shift'));
    }

    //UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_shift'    => 'required|string|max:255',
            'jam_masuk'     => 'required',
            'jam_keluar'    => 'required',
            'toleransi_menit' => 'required|integer|min:0',
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update($request->all());

        return redirect()->route('admin.shift.index')->with('success', 'Shift berhasil diupdate');
    }

    //DELETE
    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return redirect()->route('admin.shift.index')->with('success', 'Shift berhasil dihapus');
    }
}
