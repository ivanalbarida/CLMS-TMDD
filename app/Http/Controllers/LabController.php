<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // This line is crucial for the update method

class LabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labs = Lab::all();
        return view('labs.index', compact('labs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('labs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data with unique constraint and custom message
        $request->validate([
            'lab_name' => 'required|string|max:255|unique:labs,lab_name', // Unique rule
            'building_name' => 'required|string|max:255',
        ], [
            // Custom validation message for uniqueness
            'lab_name.unique' => 'The Lab Name / Room # already exists. Please choose a different one.',
        ]);

        Lab::create([
            'lab_name' => $request->lab_name,
            'building_name' => $request->building_name,
        ]);

        return redirect()->route('labs.index')->with('success', 'Lab created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // This method is currently empty, keep it as is unless you need to display a single lab
    }

    /**
     * Show the form for editing the specified lab.
     */
    public function edit(Lab $lab)
    {
        return view('labs.edit', compact('lab'));
    }

    /**
     * Update the specified lab in storage.
     */
    public function update(Request $request, Lab $lab)
    {
        // Validate the incoming data with unique constraint (ignoring the current lab's ID) and custom message
        $request->validate([
            'lab_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('labs', 'lab_name')->ignore($lab->id), // Unique rule ignoring current ID
            ],
            'building_name' => 'required|string|max:255',
        ], [
            // Custom validation message for uniqueness
            'lab_name.unique' => 'The Lab Name / Room # already exists. Please choose a different one.',
        ]);

        $lab->update([
            'lab_name' => $request->lab_name,
            'building_name' => $request->building_name,
        ]);

        return redirect()->route('labs.index')->with('success', 'Lab updated successfully.');
    }

    /**
     * Remove the specified lab from storage.
     */
    public function destroy(Lab $lab)
    {
        $lab->delete();
        return redirect()->route('labs.index')->with('success', 'Lab deleted successfully.');
    }
}