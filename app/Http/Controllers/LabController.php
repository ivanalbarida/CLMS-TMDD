<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labs = Lab::all(); // Get all labs from the database
        return view('labs.index', compact('labs')); // Send the data to the view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('labs.create'); // Just show the form
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'lab_name' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ]);

        // Create the new lab
        Lab::create([
            'lab_name' => $request->lab_name,
            'building_name' => $request->building_name,
        ]);

        // Redirect back to the list page with a success message
        return redirect()->route('labs.index')->with('success', 'Lab created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified lab.
     */
    public function edit(Lab $lab) // <-- THE FIX
    {
        // Now, Laravel automatically provides the $lab object.
        return view('labs.edit', compact('lab'));
    }

    /**
     * Update the specified lab in storage.
     */
    public function update(Request $request, Lab $lab) // <-- PROACTIVE FIX
    {
        $request->validate([
            'lab_name' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ]);

        $lab->update($request->all());

        return redirect()->route('labs.index')->with('success', 'Lab updated successfully.');
    }

    /**
     * Remove the specified lab from storage.
     */
    public function destroy(Lab $lab) // <-- THE FIX
    {
        // Now Laravel provides the full $lab object to delete.
        $lab->delete();
        
        return redirect()->route('labs.index')->with('success', 'Lab deleted successfully.');
    }
}
