<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Component;
use App\Models\Lab;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // The index will now list labs with an equipment count for each.
        $labs = Lab::withCount('equipment')->get();
        return view('equipment.index', compact('labs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $labs = Lab::all(); // Get all labs for the dropdown
        $statuses = ['Working', 'For Repair', 'In Use', 'Retired']; // For status dropdown
        // Fixed list of component types based on CSV and requirements
        $componentTypes = ['Monitor', 'OS', 'Processor', 'CPU Serial Num', 'Motherboard', 'Memory', 'Storage', 'Video Card', 'PSU', 'Router', 'Switch', 'Other'];

        return view('equipment.create', compact('labs', 'statuses', 'componentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // --- Validation ---
        $request->validate([
            'tag_number' => 'required|string|unique:equipment,tag_number',
            'lab_id' => 'required|exists:labs,id',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'components' => 'required|array',
            'components.*.type' => 'required|string',
            'components.*.description' => 'required|string',
            'components.*.serial_number' => 'nullable|string',
        ]);

        // --- Use a Database Transaction ---
        // This ensures that if any part fails, the whole operation is rolled back.
        DB::transaction(function () use ($request) {
            // 1. Create the main Equipment record
            $equipment = Equipment::create([
                'tag_number' => $request->tag_number,
                'lab_id' => $request->lab_id,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // 2. Loop through and create the components
            foreach ($request->components as $componentData) {
                // Skip any rows that might be empty
                if (!empty($componentData['type']) && !empty($componentData['description'])) {
                    $equipment->components()->create([
                        'type' => $componentData['type'],
                        'description' => $componentData['description'],
                        'serial_number' => $componentData['serial_number'],
                    ]);
                }
            }
        });

        return redirect()->route('equipment.index')->with('success', 'Equipment added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
        {
            // Laravel has already found the $equipment object for us from the ID in the URL.
            // We just need to load its relationships before sending it to the view.
            $equipment->load('lab', 'components');
            
            return view('equipment.show', compact('equipment'));
        }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        $equipment->load('components'); // Load components to edit them
        $labs = Lab::all();
        $statuses = ['Working', 'For Repair', 'In Use', 'Retired'];
        $componentTypes = ['Monitor', 'OS', 'Processor', 'CPU Serial Num', 'Motherboard', 'Memory', 'Storage', 'Video Card', 'PSU', 'Router', 'Switch', 'Other'];

        return view('equipment.edit', compact('equipment', 'labs', 'statuses', 'componentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'tag_number' => 'required|string|unique:equipment,tag_number,' . $equipment->id,
            'lab_id' => 'required|exists:labs,id',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'components' => 'nullable|array', // Components can be empty now
        ]);

        DB::transaction(function () use ($request, $equipment) {
            // 1. Update the main equipment details
            $equipment->update($request->only('tag_number', 'lab_id', 'status', 'notes'));

            // 2. Sync Components: Delete old ones, update existing, add new ones
            // A simple way is to delete all old components and re-create them from the form.
            $equipment->components()->delete();

            if ($request->has('components')) {
                foreach ($request->components as $componentData) {
                    if (!empty($componentData['type']) && !empty($componentData['description'])) {
                        $equipment->components()->create([
                            'type' => $componentData['type'],
                            'description' => $componentData['description'],
                            'serial_number' => $componentData['serial_number'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('equipment.showByLab', $equipment->lab_id)->with('success', 'Equipment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        // The database is set up with cascading deletes,
        // so deleting the equipment will also delete its components and maintenance records.
        $equipment->delete();

        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully.');
    }

    public function showByLab(Lab $lab)
    {
        // Eager load the equipment and its components for the given lab
        $lab->load('equipment.components');
        return view('equipment.list-by-lab', compact('lab'));
    }
}
