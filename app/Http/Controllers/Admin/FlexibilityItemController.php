<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlexibilityItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FlexibilityItemController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. INDEX
    |--------------------------------------------------------------------------
    | Tampilkan semua item katalog kelonggaran.
    */
    public function index()
    {
        $items = FlexibilityItem::latest()->get();

        return view('admin.flexibility-items.index', compact('items'));
    }


    /*
    |--------------------------------------------------------------------------
    | 2. FORM CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.flexibility-items.form');
    }


    /*
    |--------------------------------------------------------------------------
    | 3. STORE
    |--------------------------------------------------------------------------
    | Simpan item baru ke database.
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'   => 'required|string|max:255|unique:flexibility_items,item_name',
            'point_cost'  => 'required|integer|min:1',
            'stock_limit' => 'nullable|integer|min:1',
            'is_active'  => 'required|boolean',
        ]);

        FlexibilityItem::create($validated);

        return redirect()
            ->route('admin.flexibility-items.index')
            ->with('success', 'Item berhasil ditambahkan.');
    }


    /*
    |--------------------------------------------------------------------------
    | 4. FORM EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(FlexibilityItem $flexibilityItem)
    {
        return view('admin.flexibility-items.form', compact('flexibilityItem'));
    }


    /*
    |--------------------------------------------------------------------------
    | 5. UPDATE
    |--------------------------------------------------------------------------
    | Update data item yang sudah ada.
    */
    public function update(Request $request, FlexibilityItem $flexibilityItem)
    {
        $validated = $request->validate([
            'item_name'   => 'required|string|max:255|unique:flexibility_items,item_name,' . $flexibilityItem->id,
            'point_cost'  => 'required|integer|min:1',
            'stock_limit' => 'nullable|integer|min:1',
            'is_active'  => 'required|boolean',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi: point_cost tidak boleh diturunkan jika item sudah pernah dibeli
        |--------------------------------------------------------------------------
        | Mencegah admin curang menurunkan harga setelah user sudah beli di harga lama.
        | Jika ingin ubah harga, admin harus nonaktifkan item lama dan buat yang baru.
        */
        if (
            $validated['point_cost'] < $flexibilityItem->point_cost &&
            $flexibilityItem->userTokens()->exists()
        ) {
            throw ValidationException::withMessages([
                'point_cost' => 'Harga tidak bisa diturunkan karena item ini sudah pernah dibeli user. Nonaktifkan item ini dan buat item baru jika ingin mengubah harga.'
            ]);
        }

        $flexibilityItem->update($validated);

        return redirect()
            ->route('admin.flexibility-items.index')
            ->with('success', 'Item berhasil diperbarui.');
    }


    /*
    |--------------------------------------------------------------------------
    | 6. DESTROY
    |--------------------------------------------------------------------------
    | Hapus item dari katalog.
    |
    | Item yang sudah pernah dibeli (ada user_tokens) tidak boleh dihapus
    | karena akan merusak relasi user_tokens.item_id.
    */
    public function deactivate($id)
    {
        $item = FlexibilityItem::findOrFail($id);

        $item->update([
            'is_active' => false
        ]);

        return redirect()
            ->route('admin.flexibility-items.index')
            ->with('success', 'Item berhasil dinonaktifkan.');
    }

    public function activate($id)
    {
        $item = FlexibilityItem::findOrFail($id);

        $item->update([
            'is_active' => true
        ]);

        return redirect()
            ->route('admin.flexibility-items.index')
            ->with('success', 'Item berhasil diaktifkan.');
    }
}
