<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SoftwareChecklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SoftwareChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Group the checklist items for a clean, organized view
        $checklistItems = SoftwareChecklist::with('user')
                            ->orderBy('program_name')
                            ->orderBy('year_and_sem')
                            ->get()
                            ->groupBy(['program_name', 'year_and_sem']);

        return view('software-checklist.index', compact('checklistItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('software-checklist.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // New validation rules to handle arrays
        $request->validate([
            'program_name' => 'required|string|max:255',
            'year_and_sem' => 'required|string|max:255',
            'software_items' => 'required|array|min:1', // Expect an array of software
            'software_items.*.software_name' => 'required|string|max:255', // Validate each item in the array
            'software_items.*.version' => 'nullable|string|max:50',
            'software_items.*.notes' => 'nullable|string',
        ]);

        // Use a transaction to ensure all or nothing is saved
        DB::transaction(function () use ($request) {
            foreach ($request->software_items as $item) {
                // Skip any empty rows that might have been added
                if (empty($item['software_name'])) {
                    continue;
                }

                SoftwareChecklist::create([
                    'program_name' => $request->program_name,
                    'year_and_sem' => $request->year_and_sem,
                    'software_name' => $item['software_name'],
                    'version' => $item['version'],
                    'notes' => $item['notes'],
                    'user_id' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('software-checklist.index')->with('success', 'Checklist items added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(SoftwareChecklist $softwareChecklist)
    {
        return view('software-checklist.edit', compact('softwareChecklist'));
    }

    public function update(Request $request, SoftwareChecklist $softwareChecklist)
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
            'year_and_sem' => 'required|string|max:255',
            'software_name' => 'required|string|max:255',
            'version' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $request->merge(['user_id' => Auth::id()]);
        $softwareChecklist->update($request->all());

        return redirect()->route('software-checklist.index')->with('success', 'Checklist item updated successfully.');
    }

    public function destroy(SoftwareChecklist $softwareChecklist)
    {
        $softwareChecklist->delete();
        
        return redirect()->route('software-checklist.index')->with('success', 'Checklist item deleted successfully.');
    }
}
