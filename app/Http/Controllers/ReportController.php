<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRecord; 
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|in:Corrective,Preventive',
            'status' => 'nullable|in:Pending,In Progress,Completed',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // 2. Start building the query
        $query = MaintenanceRecord::query();

        // Filter by the date the issue was REPORTED
        $query->whereBetween('date_reported', [$startDate, $endDate]);

        // Add conditional filters if they were provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->status . '%');
        }

        // 3. Eager load all necessary relationships and execute the query
        $records = $query->with(['user', 'equipment.lab'])
                         ->orderBy('date_reported', 'desc')
                         ->get();

        // 4. Return the view with all the necessary data
        return view('reports.show', [
            'records' => $records,
            'startDate' => Carbon::parse($request->start_date),
            'endDate' => Carbon::parse($request->end_date),
            'filters' => $request->only(['start_date', 'end_date', 'type', 'status']), // For the export button later
        ]);
    }
}