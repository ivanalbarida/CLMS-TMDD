<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Lab;
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
        // Add 'lab_name' to the list of sortable columns
        $sortableColumns = ['type', 'category', 'status', 'date_reported', 'scheduled_for', 'lab_name'];
        
        $sortBy = in_array($request->query('sort_by'), $sortableColumns) ? $request->query('sort_by') : 'date_reported';
        $sortDirection = in_array($request->query('sort_direction'), ['asc', 'desc']) ? $request->query('sort_direction') : 'desc';

        // --- Base Queries ---
        $correctiveQuery = MaintenanceRecord::where('type', 'Corrective');
        $preventiveQuery = MaintenanceRecord::where('type', 'Preventive');

        // --- Role-Based Filtering ---
        if (Auth::user()->role !== 'Admin') {
            $correctiveQuery->where('user_id', Auth::id());
            $preventiveQuery->where('user_id', Auth::id());
        }

        // --- Apply Sorting ---
        $queries = ['corrective' => $correctiveQuery, 'preventive' => $preventiveQuery];
        foreach ($queries as $type => $query) {
            if ($sortBy === 'lab_name') {
                $query->select('maintenance_records.*')
                    ->join('equipment_maintenance', 'maintenance_records.id', '=', 'equipment_maintenance.maintenance_record_id')
                    ->join('equipment', 'equipment_maintenance.equipment_id', '=', 'equipment.id')
                    ->join('labs', 'equipment.lab_id', '=', 'labs.id')
                    ->orderBy('labs.lab_name', $sortDirection)
                    ->groupBy('maintenance_records.id');
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        }
        
        // --- Fetch Data ---
        $correctiveRecords = $correctiveQuery->with('equipment.lab', 'user')->paginate(10, ['*'], 'corrective_page')->withQueryString();
        $preventiveRecords = $preventiveQuery->with('equipment.lab', 'user')->paginate(10, ['*'], 'preventive_page')->withQueryString();
        
        return view('maintenance.index', compact('correctiveRecords', 'preventiveRecords'));
    }

    public function schedule()
    {
        // This method fetches the exact same data as the create() method
        $labsQuery = \App\Models\Lab::query();
        if (Auth::user()->role !== 'Admin') {
            $labsQuery->whereHas('users', fn($q) => $q->where('user_id', Auth::id()));
        }
        $labs = $labsQuery->with('equipment')->get();
        
        $technicians = \App\Models\User::whereIn('role', ['Admin', 'Custodian/Technician'])->orderBy('name')->get();
        $categories = ['Hardware Issue', 'Software Issue', 'Network Issue', 'Facilities Issue', 'Preventive Maintenance', 'Other'];
        
        // Return the NEW schedule view
        return view('maintenance.schedule', compact('labs', 'technicians', 'categories'));
    }
    
    public function create(Request $request)
    {
        $type = $request->query('type', 'Corrective'); // Default to Corrective

        $labsQuery = Lab::query();
        if (Auth::user()->role !== 'Admin') {
            $labsQuery->whereHas('users', fn($q) => $q->where('user_id', Auth::id()));
        }
        $labs = $labsQuery->with('equipment')->get();
        
        $technicians = User::whereIn('role', ['Admin', 'Custodian/Technician'])->orderBy('name')->get();
        $categories = ['Hardware Issue', 'Software Issue', 'Network Issue', 'Facilities Issue', 'Preventive Maintenance', 'Other'];
        $statuses = ['Pending', 'In Progress', 'Completed'];

        return view('maintenance.create', compact('labs', 'technicians', 'categories', 'statuses', 'type'));
    }

    public function store(Request $request)
    {
        // Use a variable for the user_id validation rule
        $userIdValidation = Auth::user()->role === 'Admin' ? 'required' : 'nullable';

        $request->validate([
            'equipment_ids' => 'required|array|min:1', 
            'equipment_ids.*' => 'exists:equipment,id',
            'type' => 'required|in:Corrective,Preventive',
            'user_id' => [$userIdValidation, 'exists:users,id'], // Apply the dynamic rule
            'date_reported' => 'required|date',
            'issue_description' => 'required|string',
            'status' => 'required|string',
            'scheduled_for' => 'nullable|date',
            'action_taken' => 'required_if:status,Completed|nullable|string',
            'date_completed' => 'required_if:status,Completed|nullable|date',
            'category' => 'required|string',
        ]);

        $dataToCreate = $request->except(['_token', 'equipment_ids']);

        // If the logged-in user is not an Admin, force them to be the assigned user.
        if (Auth::user()->role !== 'Admin') {
            $dataToCreate['user_id'] = Auth::id();
        }

        DB::transaction(function () use ($dataToCreate, $request) {
            $maintenanceRecord = MaintenanceRecord::create($dataToCreate);
            $maintenanceRecord->equipment()->attach($request->equipment_ids);

            // Log the activity
            foreach ($maintenanceRecord->equipment as $pc) {
                log_activity(
                    'MAINTENANCE_LOGGED',
                    $pc,
                    "{$dataToCreate['type']} maintenance logged by " . Auth::user()->name . ". Issue: {$dataToCreate['issue_description']}"
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
        $labs = Lab::with('equipment')->get();
        
        // CORRECTED TECHNICIAN QUERY
        $technicians = User::whereIn('role', ['Admin', 'Custodian/Technician'])->orderBy('name')->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];
        $categories = ['Hardware Issue', 'Software Issue', 'Network Issue', 'Facilities Issue', 'Preventive Maintenance', 'Other'];

        return view('maintenance.edit', compact('maintenance', 'assignedEquipmentIds', 'labs', 'technicians', 'statuses', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceRecord $maintenance)
    {
        // 1. Validation that MATCHES the edit form's fields
        $validated = $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|string',
            'category' => 'required|string',
            'date_started' => 'nullable|date',
            'scheduled_for' => 'nullable|date',
            'issue_description' => 'required|string',
            'action_taken' => 'nullable|string',
        ]);

        // 2. Prepare the data for the update manually
        $dataToUpdate = [
            'user_id'           => $validated['user_id'],
            'status'            => $validated['status'],
            'category'          => $validated['category'],
            'date_started'      => $validated['date_started'],
            'issue_description' => $validated['issue_description'],
            'action_taken'      => $validated['action_taken'],
        ];

        // 3. Only add 'scheduled_for' to the update array if it was submitted
        if ($request->has('scheduled_for')) {
            $dataToUpdate['scheduled_for'] = $validated['scheduled_for'];
        }

        // 4. Log the activity BEFORE the transaction
        if ($request->filled('date_started') && is_null($maintenance->date_started)) {
            foreach ($maintenance->equipment as $pc) {
                log_activity('MAINTENANCE_STARTED', $pc, "Work started on '{$maintenance->issue_description}' by " . Auth::user()->name);
            }
        }
        
        // 5. Perform the update
        DB::transaction(function () use ($request, $maintenance, $dataToUpdate) {
            $maintenance->update($dataToUpdate);
            $maintenance->equipment()->sync($request->equipment_ids);
        });

        log_activity('UPDATED', $maintenance, "Updated details for maintenance log.");

        return redirect()->route('maintenance.index')->with('success', 'Maintenance log updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaintenanceRecord $maintenance)
    {
        $maintenance->delete();
        log_activity('DELETED', $maintenance, "Deleted maintenance log.");
        return redirect()->route('maintenance.index')->with('success', 'Maintenance log deleted successfully.');
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
