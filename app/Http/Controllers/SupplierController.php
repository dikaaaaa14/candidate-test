<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('layups');

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $suppliers = $query->get();

        // Export CSV (all suppliers list)
        if ($request->has('export')) {
            return $this->exportSuppliersCSV($suppliers);
        }

        return view('suppliers.index', compact('suppliers'));
    }

    private function exportSuppliersCSV($suppliers)
    {
        $filename = 'suppliers-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($suppliers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Total Layups', 'Created At']);
            foreach ($suppliers as $s) {
                fputcsv($file, [
                    'SUP-' . str_pad($s->id, 7, '0', STR_PAD_LEFT),
                    $s->name,
                    $s->layups_count,
                    $s->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Supplier::create($request->only('name'));
        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('layups.layers');
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function export(Supplier $supplier)
    {
        $supplier->load('layups.layers');

        $filename = 'supplier-' . $supplier->id . '-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($supplier) {
            $file = fopen('php://output', 'w');

            // Supplier info header
            fputcsv($file, ['Supplier ID', 'Supplier Name']);
            fputcsv($file, ['SUP-' . str_pad($supplier->id, 7, '0', STR_PAD_LEFT), $supplier->name]);
            fputcsv($file, []); // empty row

            // Layups & layers
            fputcsv($file, ['Layup ID', 'Layup Name', 'Layer Order', 'Thickness', 'Width', 'Angle']);

            foreach ($supplier->layups as $layup) {
                if ($layup->layers->isEmpty()) {
                    fputcsv($file, ['L-' . str_pad($layup->id, 3, '0', STR_PAD_LEFT), $layup->name, '-', '-', '-', '-']);
                } else {
                    foreach ($layup->layers as $layer) {
                        fputcsv($file, [
                            'L-' . str_pad($layup->id, 3, '0', STR_PAD_LEFT),
                            $layup->name,
                            $layer->layer_order,
                            $layer->thickness,
                            $layer->width,
                            $layer->angle,
                        ]);
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importForm(Supplier $supplier)
    {
        return view('suppliers.import', compact('supplier'));
    }

    public function import(Request $request, Supplier $supplier)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');

        // Skip header rows (Supplier ID, Supplier Name, empty, column headers)
        $lineCount = 0;
        $rows = [];
        while (($row = fgetcsv($file)) !== false) {
            $lineCount++;
            if ($lineCount <= 4) continue; // skip header block
            $rows[] = $row;
        }
        fclose($file);

        $strategy = $request->input('strategy', 'skip');
        $conflicts = 0;
        $layupData = [];

        // Group rows by layup
        foreach ($rows as $row) {
            if (count($row) < 6) continue;
            [$layupId, $layupName, $layerOrder, $thickness, $width, $angle] = $row;
            if (!isset($layupData[$layupName])) {
                $layupData[$layupName] = [];
            }
            if ($layerOrder !== '-') {
                $layupData[$layupName][] = [
                    'layer_order' => (int) $layerOrder,
                    'thickness'   => (float) $thickness,
                    'width'       => (float) $width,
                    'angle'       => (float) $angle,
                ];
            }
        }

        foreach ($layupData as $layupName => $layers) {
            $existingLayup = $supplier->layups()->where('name', $layupName)->first();

            if ($existingLayup) {
                if ($strategy === 'skip') {
                    $conflicts++;
                    continue;
                } elseif ($strategy === 'overwrite') {
                    foreach ($layers as $layerData) {
                        $existingLayup->layers()->updateOrCreate(
                            ['layer_order' => $layerData['layer_order']],
                            $layerData
                        );
                    }
                } elseif ($strategy === 'duplicate') {
                    $newLayup = $supplier->layups()->create(['name' => $layupName . ' (imported)']);
                    foreach ($layers as $layerData) {
                        $newLayup->layers()->create($layerData);
                    }
                }
            } else {
                $newLayup = $supplier->layups()->create(['name' => $layupName]);
                foreach ($layers as $layerData) {
                    $newLayup->layers()->create($layerData);
                }
            }
        }

        $message = 'Import successful.';
        if ($conflicts > 0 && $strategy === 'skip') {
            $message = "Import done. $conflicts layup(s) skipped due to conflicts.";
        }

        return redirect()->route('suppliers.show', $supplier)->with('success', $message);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $supplier->update($request->only('name'));
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}