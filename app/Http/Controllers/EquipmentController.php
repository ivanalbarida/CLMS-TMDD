<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\Component;
use App\Models\Lab;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labsQuery = Lab::query();

        $user = Auth::user();
        if ($user->role === 'Custodian/Technician') {

            $labsQuery->whereHas('users', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }
        
        $labs = $labsQuery->withCount('equipment')->get();

        return view('equipment.index', compact('labs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $labs = Lab::all(); // Get all labs for the dropdown
        // MODIFIED: 'In Use' removed from the statuses array for create method
        $statuses = ['Working', 'For Repair', 'Retired']; // For status dropdown
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
            'tag_number' => [
            'required',
            'string',
            Rule::unique('equipment')->where(function ($query) use ($request) {
                return $query->where('lab_id', $request->lab_id);
                }),
            ],
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
            
            log_activity('CREATED', $equipment, "Created new equipment: {$equipment->tag_number}");

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
    public function show(Request $request, Equipment $equipment) // <-- Add Request $request
    {
        // Load the standard relationships
        $equipment->load('lab', 'components');

        // --- FILTERING LOGIC ---
        // Start building the query for the activity history
        $historyQuery = \App\Models\ActivityLog::where('subject_type', 'App\Models\Equipment')
                                            ->where('subject_id', $equipment->id)
                                            ->with('user'); // Eager load the user

        // Apply a "start date" filter if provided
        if ($request->filled('start_date')) {
            $historyQuery->where('created_at', '>=', $request->start_date);
        }

        // Apply an "end date" filter if provided
        if ($request->filled('end_date')) {
            // Add a day to the end date to include all events on that day
            $endDate = \Carbon\Carbon::parse($request->end_date)->addDay();
            $historyQuery->where('created_at', '<', $endDate);
        }

        // Apply an "action type" filter if provided
        if ($request->filled('action_type')) {
            $historyQuery->where('action_type', $request->action_type);
        }

        // Execute the final query with ordering and pagination
        $history = $historyQuery->latest()->paginate(15)->withQueryString(); // withQueryString appends filters to pagination links

        // Get a list of all possible action types for the dropdown
        $actionTypes = ['CREATED', 'UPDATED', 'DELETED', 'MAINTENANCE_LOGGED', 'MAINTENANCE_COMPLETED', 'COMPLETED_PM_TASK', 'UNCHECKED_PM'];

        return view('equipment.show', compact('equipment', 'history', 'actionTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        $equipment->load('components'); // Load components to edit them
        $labs = Lab::all();
        // MODIFIED: 'In Use' removed from the statuses array for edit method
        $statuses = ['Working', 'For Repair', 'Retired'];
        $componentTypes = ['Monitor', 'OS', 'Processor', 'CPU Serial Num', 'Motherboard', 'Memory', 'Storage', 'Video Card', 'PSU', 'Router', 'Switch', 'Other'];

        return view('equipment.edit', compact('equipment', 'labs', 'statuses', 'componentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'tag_number' => [
            'required',
            'string',
            Rule::unique('equipment')->where(function ($query) use ($request) {
                return $query->where('lab_id', $request->lab_id);
            })->ignore($equipment->id), // Important: ignore the current record itself
        ],
            'lab_id' => 'required|exists:labs,id',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'components' => 'nullable|array', // Components can be empty now
        ]);

        DB::transaction(function () use ($request, $equipment) {
            // 1. Update the main equipment details
            $equipment->update($request->only('tag_number', 'lab_id', 'status', 'notes'));

            log_activity('UPDATED', $equipment, "Updated details for equipment: {$equipment->tag_number}");

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

        log_activity('DELETED', $equipment, "Deleted equipment: {$equipment->tag_number}");

        $equipment->delete();

        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully.');
    }

    public function showByLab(Lab $lab)
    {
        // Eager load the equipment, and for each piece of equipment,
        // also load its 'components' relationship.
        $lab->load(['equipment.components', 'equipment.openSoftwareIssues', 'softwareProfile.softwareItems']);

        $lab->load(['equipment' => function ($query) {
            $query->with(['components', 'maintenanceRecords' => function ($subQuery) {
                // Get only OPEN maintenance records categorized as software issues
                $subQuery->where('status', '!=', 'Completed')->where('category', 'Software Issue');
            }]);
        }, 'softwareProfile.softwareItems']);
        
        // The lab activity history part is already correct
        $equipmentIdsInLab = $lab->equipment->pluck('id');
        $labHistory = ActivityLog::where('subject_type', 'App\Models\Equipment')
                                ->whereIn('subject_id', $equipmentIdsInLab)
                                ->with('user', 'subject')
                                ->latest()
                                ->paginate(10, ['*'], 'history_page');

        return view('equipment.list-by-lab', compact('lab', 'labHistory'));
    }
}