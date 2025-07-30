<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRecord; 
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaintenanceReportExport;
use App\Models\Lab;
use App\Models\ActivityLog;

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

    public function export(Request $request)
    {
        // 1. Validate filters (same as before)
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|in:Corrective,Preventive',
            'status' => 'nullable|in:Pending,In Progress,Completed',
        ]);

        // 2. Run the query (same as before)
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $query = MaintenanceRecord::whereBetween('date_reported', [$startDate, $endDate]);
        if ($request->filled('type')) { $query->where('type', $request->type); }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        $records = $query->with(['user', 'equipment.lab'])->orderBy('date_reported', 'desc')->get();

        // 3. Manually build the CSV and trigger a download
        $fileName = 'maintenance-report.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Reported Date', 'Completed Date', 'Type', 'Status', 'Equipment Serviced', 'Lab', 'Technician', 'Action Taken'];

        $callback = function() use($records, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($records as $record) {
                $row['Reported Date']    = Carbon::parse($record->date_reported)->format('Y-m-d');
                $row['Completed Date']   = $record->date_completed ? Carbon::parse($record->date_completed)->format('Y-m-d') : 'N/A';
                $row['Type']             = $record->type;
                $row['Status']           = $record->status;
                $row['Equipment']        = $record->equipment->pluck('tag_number')->implode(', ');
                $row['Lab']              = $record->equipment->first()->lab->lab_name ?? 'N/A';
                $row['Technician']       = $record->user->name ?? 'N/A';
                $row['Action Taken']     = $record->type == 'Preventive' ? $record->issue_description : $record->action_taken;

                fputcsv($file, [
                    $row['Reported Date'], 
                    $row['Completed Date'], 
                    $row['Type'], 
                    $row['Status'], 
                    $row['Equipment'], 
                    $row['Lab'], 
                    $row['Technician'], 
                    $row['Action Taken']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function showLabReportForm(Lab $lab)
        {
            return view('reports.lab-report-form', compact('lab'));
        }

        public function generateLabReport(Request $request, Lab $lab)
    {
        // Validate the incoming dates
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Load all the relationships we need for the report
        $lab->load(['softwareProfile.softwareItems', 'equipment.components']);

        // Fetch the maintenance history for the selected date range
        $equipmentIdsInLab = $lab->equipment->pluck('id');
        $history = ActivityLog::where('subject_type', 'App\Models\Equipment')
                            ->whereIn('subject_id', $equipmentIdsInLab)
                            ->whereBetween('created_at', [$startDate, $endDate]) // Use the dynamic dates
                            ->with('user', 'subject')
                            ->latest()
                            ->get();
        
        return view('reports.lab-report', compact('lab', 'history', 'startDate', 'endDate'));
    }
}