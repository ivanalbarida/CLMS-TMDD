<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Equipment;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) 
    {
        // Start the base query
        $query = MaintenanceRecord::query();

        // Add conditional filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // By default, only show open tasks on this page
            $query->where('status', '!=', 'Completed');
        }

        // Eager load relationships and get the results
        $records = $query->with('equipment', 'user')
                        ->latest('date_reported')
                        ->get();

        return view('maintenance.index', compact('records'));
    }

    public function create()
    {
        $labsQuery = \App\Models\Lab::query();

        if (Auth::user()->role !== 'Admin') {
            // If not an admin, only get labs assigned to this user
            $labsQuery->whereHas('users', fn($q) => $q->where('user_id', Auth::id()));
        }

        $labs = $labsQuery->with('equipment')->get();
        $technicians = User::whereIn('role', ['Admin', 'Custodian/Technician'])->orderBy('name')->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];
        $categories = ['Hardware Issue', 'Software Issue', 'Network Issue', 'Facilities Issue', 'Other'];

        return view('maintenance.create', compact('labs', 'technicians', 'statuses', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_ids' => 'required|array|min:1', 
            'equipment_ids.*' => 'exists:equipment,id',
            'type' => 'required|in:Corrective,Preventive',
            'user_id' => 'required|exists:users,id',
            'date_reported' => 'required|date',
            'issue_description' => 'required|string',
            'status' => 'required|string',
            'scheduled_for' => 'nullable|date',
            'category' => 'required|string',
            
            // Add conditional validation
            'action_taken' => 'required_if:status,Completed|nullable|string',
            'date_completed' => 'required_if:status,Completed|nullable|date',
        ]);

        if (Auth::user()->role !== 'Admin') {
            $dataToCreate['user_id'] = Auth::id();
        }

        DB::transaction(function () use ($request) {
            $dataToCreate = $request->except(['_token', 'equipment_ids']);
            
            $maintenanceRecord = MaintenanceRecord::create($dataToCreate);

            $maintenanceRecord->equipment()->attach($request->equipment_ids);

            // Log the activity
            foreach ($maintenanceRecord->equipment as $pc) {
                log_activity(
                    'MAINTENANCE_LOGGED',
                    $pc,
                    "{$request->type} maintenance logged by " . Auth::user()->name . ". Issue: {$request->issue_description}"
                );
            }
        });

        return redirect()->route('maintenance.index')->with('success', 'Maintenance log created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('maintenance.index');
    }

        /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaintenanceRecord $maintenance)
    {
        $maintenance->load('equipment');
        $assignedEquipmentIds = $maintenance->equipment->pluck('id')->toArray();
        
        $labs = \App\Models\Lab::with('equipment')->get();
        
        $technicians = \App\Models\User::whereIn('role', ['Admin', 'Technician'])->orderBy('name')->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];
        $categories = ['Hardware Issue', 'Software Issue', 'Network Issue', 'Facilities Issue', 'Other'];

        return view('maintenance.edit', compact('maintenance', 'assignedEquipmentIds', 'labs', 'technicians', 'statuses', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceRecord $maintenance)
    {
        // Add date_started to the validation rules
        $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
            'issue_description' => 'required|string',
            'action_taken' => 'nullable|string',
            'date_started' => 'nullable|date',
            'category' => 'required|string',
        ]);

        // --- Activity Logging Logic ---
        // Check if the 'date_started' is being set for the first time
        if ($request->filled('date_started') && is_null($maintenance->date_started)) {
            // Log this action for each piece of equipment involved
            foreach ($maintenance->equipment as $pc) {
                log_activity(
                    'MAINTENANCE_STARTED',
                    $pc,
                    "Work started on '{$maintenance->issue_description}' by " . Auth::user()->name
                );
            }
        }
        // --- End of Logging Logic ---

        DB::transaction(function () use ($request, $maintenance) {
            // The update() call will now automatically handle the new 'date_started' field
            $maintenance->update($request->except(['_token', '_method', 'equipment_ids']));
            $maintenance->equipment()->sync($request->equipment_ids);
        });

        return redirect()->route('maintenance.index')->with('success', 'Maintenance log updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceRecord $maintenance) // <-- Use Route Model Binding
    {
        $maintenance->delete();
        
        return redirect()->route('maintenance.index')->with('success', 'Maintenance log deleted successfully.');
    }

    public function schedule()
    {
        $labsQuery = \App\Models\Lab::query();

        if (Auth::user()->role !== 'Admin') {
            // If not an admin, only get labs assigned to this user
            $labsQuery->whereHas('users', fn($q) => $q->where('user_id', Auth::id()));
        }

        $labs = $labsQuery->with('equipment')->get();
        $technicians = User::whereIn('role', ['Admin', 'Custodian/Technician'])->orderBy('name')->get();
        
        return view('maintenance.schedule', compact('labs', 'technicians'));
    }

    public function complete(MaintenanceRecord $maintenance)
    {
        $maintenance->update([
            'status' => 'Completed',
            'date_completed' => now(),
        ]);

        foreach ($maintenance->equipment as $pc) {
            log_activity('MAINTENANCE_COMPLETED', $pc, "Maintenance marked as complete by " . Auth::user()->name);
        }

        return redirect()->route('maintenance.index')->with('success', 'Maintenance log marked as complete.');
    }
}
