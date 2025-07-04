<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Equipment;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $equipment = Equipment::orderBy('tag_number')->get();
        // Get only users with Admin or Technician roles
        $technicians = User::whereIn('role', ['Admin', 'Technician'])->orderBy('name')->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];

        return view('maintenance.create', compact('equipment', 'technicians', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
             'type' => 'required|in:Corrective,Preventive',
            'equipment_id' => 'required|exists:equipment,id',
            'user_id' => 'required|exists:users,id',
            'date_reported' => 'required|date',
            'issue_description' => 'required|string',
            'status' => 'required|string',
            'scheduled_for' => 'nullable|date',
        ]);

        MaintenanceRecord::create($request->all());

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
    public function edit(MaintenanceRecord $maintenance) // <-- Use Route Model Binding
    {
        $equipment = Equipment::orderBy('tag_number')->get();
        $technicians = User::whereIn('role', ['Admin', 'Technician'])->orderBy('name')->get();
        $statuses = ['Pending', 'In Progress', 'Completed'];

        return view('maintenance.edit', compact('maintenance', 'equipment', 'technicians', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaintenanceRecord $maintenance) // <-- Use Route Model Binding
    {
        $request->validate([
             'type' => 'required|in:Corrective,Preventive',
            'equipment_id' => 'required|exists:equipment,id',
            'user_id' => 'required|exists:users,id',
            'date_reported' => 'required|date',
            'issue_description' => 'required|string',
            'action_taken' => 'nullable|string',
            'status' => 'required|string',
            'scheduled_for' => 'nullable|date',
            'date_completed' => 'nullable|date',
        ]);

        $maintenance->update($request->all());

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
        $equipment = \App\Models\Equipment::orderBy('tag_number')->get();
        $technicians = \App\Models\User::whereIn('role', ['Admin', 'Technician'])->orderBy('name')->get();
        return view('maintenance.schedule', compact('equipment', 'technicians'));
    }
}
