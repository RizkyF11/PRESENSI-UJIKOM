<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointRule;
use Illuminate\Http\Request;

class PointRuleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $rules = PointRule::latest()->get();

        return view('admin.point-rules.index', compact('rules'));
    }


    /*
    |--------------------------------------------------------------------------
    | 2. FORM CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.point-rules.form');
    }


    /*
    |--------------------------------------------------------------------------
    | 3. STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',

            'target_role' => 'required|string|max:50',

            'conditional_type' => 'required|in:EARLY_MINUTES,LATE_MINUTES',

            'condition_operator' => 'required|in:<,>,BETWEEN',

            'condition_value' => 'required|string|max:255',

            'point_modifier' => 'required|integer',
        ]);


        $this->validateConditionValue($validated);

        PointRule::create($validated);

        return redirect()
            ->route('admin.point-rules.index')
            ->with('success', 'Rule berhasil dibuat.');
    }


    /*
    |--------------------------------------------------------------------------
    | 4. FORM EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(PointRule $pointRule)
    {
        return view('admin.point-rules.form', compact('pointRule'));
    }


    /*
    |--------------------------------------------------------------------------
    | 5. UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, PointRule $pointRule)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',

            'target_role' => 'required|string|max:50',

            'conditional_type' => 'required|in:EARLY_MINUTES,LATE_MINUTES',

            'condition_operator' => 'required|in:<,>,BETWEEN',

            'condition_value' => 'required|string|max:255',

            'point_modifier' => 'required|integer',
        ]);


        $this->validateConditionValue($validated);

        $pointRule->update($validated);

        return redirect()
            ->route('admin.point-rules.index')
            ->with('success', 'Rule berhasil diperbarui.');
    }


    /*
    |--------------------------------------------------------------------------
    | 6. DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(PointRule $pointRule)
    {
        $pointRule->delete();

        return redirect()
            ->route('admin.point-rules.index')
            ->with('success', 'Rule berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | PRIVATE VALIDATION CONDITION VALUE
    |--------------------------------------------------------------------------
    */
    private function validateConditionValue(array $validated): void
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI BETWEEN HARUS FORMAT: X,Y
        |--------------------------------------------------------------------------
        */
        if (
            $validated['condition_operator'] === 'BETWEEN' &&
            !str_contains($validated['condition_value'], ',')
        ) {
            abort(
                back()->withInput()->withErrors([
                    'condition_value' => 'Format BETWEEN wajib pakai koma. Contoh: 5,15'
                ])
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI EARLY/LATE MINUTES HARUS ANGKA
        |--------------------------------------------------------------------------
        */
        if (
            in_array($validated['conditional_type'], ['EARLY_MINUTES', 'LATE_MINUTES'])
        ) {

            if ($validated['condition_operator'] !== 'BETWEEN') {

                if (!is_numeric($validated['condition_value'])) {
                    abort(
                        back()->withInput()->withErrors([
                            'condition_value' => 'Value harus berupa angka.'
                        ])
                    );
                }
            }
        }
    }
}