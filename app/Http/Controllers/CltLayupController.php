<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\CltLayup;
use Illuminate\Http\Request;

class CltLayupController extends Controller
{
    public function create(Supplier $supplier)
    {
        return view('layups.create', compact('supplier'));
    }

    public function store(Request $request, Supplier $supplier)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $supplier->layups()->create($request->only('name'));
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup created.');
    }

    public function edit(Supplier $supplier, CltLayup $layup)
    {
        return view('layups.edit', compact('supplier', 'layup'));
    }

    public function update(Request $request, Supplier $supplier, CltLayup $layup)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $layup->update($request->only('name'));
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup updated.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup)
    {
        $layup->delete();
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layup deleted.');
    }
}