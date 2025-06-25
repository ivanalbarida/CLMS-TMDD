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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('labs.edit', compact('lab'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'lab_name' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ]);

        $lab->update($request->all());

        return redirect()->route('labs.index')->with('success', 'Lab updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lab->delete();
        return redirect()->route('labs.index')->with('success', 'Lab deleted successfully.');
    }
}
