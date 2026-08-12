<?php
// app/Http/Controllers/PackageSizeController.php

namespace App\Http\Controllers;

use App\Models\Package\PackageSize;
use Illuminate\Http\Request;

class PackageSizeController extends Controller
{
    public function index()
    {
        $packageSizes = PackageSize::ordered()->get();
        
        return view('package-size.index', compact('packageSizes'));
    }

    public function create()
    {
        return view('package-size.create');
    }
    public function show(PackageSize $packageSize)
    {
        return view('package-size.show', compact('packageSize'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:package_sizes,code',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight_limit' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        PackageSize::create($request->all());

        return redirect()->route('package-size.index')
            ->with('success', 'Csomagméret sikeresen létrehozva!');
    }

    public function edit(PackageSize $packageSize)
    {
        return view('package-size.edit', compact('packageSize'));
    }

    public function update(Request $request, PackageSize $packageSize)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:package_sizes,code,' . $packageSize->id,
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight_limit' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $packageSize->update($request->all());

        return redirect()->route('package-size.index')
            ->with('success', 'Csomagméret sikeresen frissítve!');
    }

    public function destroy(PackageSize $packageSize)
    {
        // Check if package size is used
        if ($packageSize->packages()->exists()) {
            return redirect()->route('package-size.index')
                ->with('error', 'Nem törölhető, mert használatban van csomagokban!');
        }

        $packageSize->delete();

        return redirect()->route('package-size.index')
            ->with('success', 'Csomagméret sikeresen törölve!');
    }

    public function toggleStatus(PackageSize $packageSize)
    {
        $packageSize->update(['status' => !$packageSize->status]);

        $status = $packageSize->status ? 'aktiválva' : 'deaktiválva';
        return redirect()->route('package-size.index')
            ->with('success', "Csomagméret sikeresen {$status}!");
    }
}