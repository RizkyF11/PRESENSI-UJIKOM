<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LokasiKantor;
use Illuminate\Http\Request;
class LokasiKantorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = LokasiKantor::latest()->get();
        return view('admin.lokasi-kantor.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.lokasi-kantor.form', [
            'lokasi'    => new LokasiKantor(),
            'method'    => 'POST',
            'route'     => route('admin.lokasi-kantor.store'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',
        ]);

        LokasiKantor::create($request->all());

        return redirect()->route('admin.lokasi-kantor.index')
            ->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LokasiKantor $lokasi_kantor)
    {
        return view('admin.lokasi-kantor.form', [
            'lokasi'    => $lokasi_kantor,
            'method'    => 'PUT',
            'route'     => route('admin.lokasi-kantor.update', $lokasi_kantor->id),
        ]);
     }
  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LokasiKantor $lokasi_kantor)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|integer|min:1',
        ]);

        $lokasi_kantor->update($request->all());

        return redirect()->route('admin.lokasi-kantor.index')
            ->with('success', 'Lokasi kantor berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LokasiKantor $lokasi_kantor)
    {
        $lokasi_kantor->delete();
        return back()->with('success', 'Lokasi kantor berhasil dihapus.');
    }
}
