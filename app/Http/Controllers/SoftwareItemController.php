<?php

namespace App\Http\Controllers;

use App\Models\SoftwareItem;
use Illuminate\Http\Request;

class SoftwareItemController extends Controller
{
    public function index()
    {
        $softwareItems = SoftwareItem::orderBy('name')->paginate(15);
        return view('software-items.index', compact('softwareItems'));
    }

    public function create()
    {
        return view('software-items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'license_details' => 'nullable|string',
        ]);

        SoftwareItem::create($request->all());
        return redirect()->route('software-items.index')->with('success', 'Software added successfully.');
    }

    public function edit(SoftwareItem $softwareItem)
    {
        return view('software-items.edit', compact('softwareItem'));
    }

    public function update(Request $request, SoftwareItem $softwareItem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'license_details' => 'nullable|string',
        ]);

        $softwareItem->update($request->all());
        return redirect()->route('software-items.index')->with('success', 'Software updated successfully.');
    }

    public function destroy(SoftwareItem $softwareItem)
    {
        $softwareItem->delete();
        return redirect()->route('software-items.index')->with('success', 'Software deleted successfully.');
    }
}