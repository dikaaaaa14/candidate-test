<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\CltLayup;
use App\Models\CltLayer;
use Illuminate\Http\Request;

class CltLayerController extends Controller
{
    public function create(Supplier $supplier, CltLayup $layup)
    {
        return view('layers.create', compact('supplier', 'layup'));
    }

    public function store(Request $request, Supplier $supplier, CltLayup $layup)
    {
        $request->validate([
            'layer_order' => 'required|integer|min:1',
            'thickness'   => 'required|numeric',
            'width'       => 'required|numeric',
            'angle'       => 'required|numeric',
        ]);
        $layup->layers()->create($request->only('layer_order', 'thickness', 'width', 'angle'));
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layer created.');
    }

    public function edit(Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        return view('layers.edit', compact('supplier', 'layup', 'layer'));
    }

    public function update(Request $request, Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        $request->validate([
            'layer_order' => 'required|integer|min:1',
            'thickness'   => 'required|numeric',
            'width'       => 'required|numeric',
            'angle'       => 'required|numeric',
        ]);
        $layer->update($request->only('layer_order', 'thickness', 'width', 'angle'));
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layer updated.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup, CltLayer $layer)
    {
        $layer->delete();
        return redirect()->route('suppliers.show', $supplier)->with('success', 'Layer deleted.');
    }
    
}