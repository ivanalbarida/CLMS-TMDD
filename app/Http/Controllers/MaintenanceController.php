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
    public function index()
    {
        // Eager load the equipment and user to prevent N+1 query problem
        $records = MaintenanceRecord::with('equipment', 'user')->latest('date_reported')->get();
        return view('maintenance.index', compact('records'));
    }

    public function create()
    {
        $labs = \App\Models\Lab::with('equipment')->get();
        $technicians = \App\Models\User::whereIn('role', ['Admin', 'Technician'])->orderBy('name')->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];

        return view('maintenance.create', compact('labs', 'technicians', 'statuses'));
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
        ]);

        DB::transaction(function () use ($request) {
            $maintenanceRecord = MaintenanceRecord::create([
                'type' => $request->type,
                'user_id' => $request->user_id,
                'date_reported' => $request->date_reported,
                'issue_description' => $request->issue_description,
                'status' => $request->status,
                'scheduled_for' => $request->scheduled_for,
            ]);

            $maintenanceRecord->equipment()->attach($request->equipment_ids);

            foreach ($maintenanceRecord->equipment as $pc) {
                log_activity('MAINTENANCE_LOGGED', $pc, "Corrective maintenance logged by {$maintenanceRecord->user->name}. Issue: {$maintenanceRecord->issue_description}");
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

        return view('maintenance.edit', compact('maintenance', 'assignedEquipmentIds', 'labs', 'technicians', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceRecord $maintenance)
    {
        $request->validate([
            'equipment_ids' => 'required|array|min:1',
            'equipment_ids.*' => 'exists:equipment,id',
        ]);

        DB::transaction(function () use ($request, $maintenance) {
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
        $labs = \App\Models\Lab::with('equipment')->get();
        $technicians = \App\Models\User::whereIn('role', ['Admin', 'Technician'])->orderBy('name')->get();
        
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
