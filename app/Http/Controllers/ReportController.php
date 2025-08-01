<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceRecord; 
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaintenanceReportExport;
use App\Models\Lab;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Models\PmTask;
use App\Models\PmTaskCompletion;
use App\Models\ServiceRequest;

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

        $user = Auth::user();
        if ($user->role !== 'Admin') {
            // If the user is NOT an Admin, only show records they are assigned to.
            $query->where('user_id', $user->id);
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
        $lab->load(['softwareProfile.softwareItems', 'equipment.components', 'equipment.openSoftwareIssues']);

        // Fetch the maintenance history for the selected date range
        $equipmentIdsInLab = $lab->equipment->pluck('id');
        $history = \App\Models\MaintenanceRecord::whereHas('equipment', function ($query) use ($equipmentIdsInLab) {
            $query->whereIn('equipment.id', $equipmentIdsInLab);
        })
        ->whereBetween('date_reported', [$startDate, $endDate])
        ->with('user', 'equipment')
        ->latest('date_reported')
        ->get();
    
        return view('reports.lab-report', compact('lab', 'history', 'startDate', 'endDate'));
    }

    /**
     * Show the form for generating a PM compliance report.
     */
    public function showPmReportForm()
    {
        $labsQuery = \App\Models\Lab::query();

        if (Auth::user()->role !== 'Admin') {
            $labsQuery->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            });
        }

        $labs = $labsQuery->orderBy('lab_name')->get();
        
        return view('reports.pm-report-form', compact('labs'));
    }

    /**
     * Generate the PM compliance report showing missed tasks.
     */
    public function generatePmReport(Request $request)
    {
        $request->validate([
            'lab_id' => 'required|exists:labs,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $lab = Lab::findOrFail($request->lab_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $missedTasks = [];
        $masterTasks = PmTask::where('is_active', true)->get();

        // Get all completions for this lab within the date range for efficiency
        $completions = PmTaskCompletion::where('lab_id', $lab->id)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->get();

        // 1. Check for missed Daily tasks
        $dailyMasterTasks = $masterTasks->where('frequency', 'Daily');
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            foreach ($dailyMasterTasks as $task) {
                $isCompleted = $completions->contains(function ($c) use ($task, $date) {
                    return $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->isSameDay($date);
                });
                if (!$isCompleted) {
                    $missedTasks[] = ['date' => $date->copy(), 'task' => $task];
                }
            }
        }
        
        // 2. Check for missed Weekly tasks
        $weeklyMasterTasks = $masterTasks->where('frequency', 'Weekly');
        foreach($weeklyMasterTasks as $task) {
            $isCompleted = $completions->contains(function($c) use ($task, $startDate, $endDate) {
                // Check if it was completed ANYTIME between the report start and end date
                return $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->between($startDate, $endDate);
            });
            if (!$isCompleted) {
                // If not, add it to the list with a generic "Period" date
                $missedTasks[] = ['date' => 'For Period', 'task' => $task];
            }
        }

        // 3. Check for missed Monthly and End of Term tasks (same logic)
        $longerTermTasks = $masterTasks->whereIn('frequency', ['Monthly', 'End of Term']);
        foreach($longerTermTasks as $task) {
            $isCompleted = $completions->contains(fn($c) => $c->pm_task_id == $task->id);
            if (!$isCompleted) {
                $missedTasks[] = ['date' => 'For Period', 'task' => $task];
            }
        }

        return view('reports.pm-report', compact('lab', 'startDate', 'endDate', 'missedTasks'));
    }

    public function showServiceRequestReportForm()
    {
        return view('reports.service-request-form');
    }

    /**
 * Generate and display the service request report.
 */
public function generateServiceRequestReport(Request $request)
{
    // Get the filtered records using a reusable private method
    $records = $this->getFilteredServiceRequests($request);
    $filters = $request->only(['start_date', 'end_date', 'type', 'status']);

    return view('reports.service-request-show', [
        'records' => $records,
        'startDate' => Carbon::parse($request->start_date),
        'endDate' => Carbon::parse($request->end_date),
        'filters' => $filters,
    ]);
}

/**
 * Export the service request report as a CSV.
 */
public function exportServiceRequestReport(Request $request)
{
    // Get the filtered records
    $records = $this->getFilteredServiceRequests($request);
    
    // (We will create this Export class in the next step)
    // return Excel::download(new ServiceRequestReportExport($records), 'service-request-report.csv');
    // For now, let's use the manual method as it's more reliable for you
    
    $fileName = 'service-request-report.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        // ... other headers
    ];

    $columns = [/* ... Define CSV columns ... */];

    $callback = function() use($records, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        // ... loop through $records and fputcsv() each row ...
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
    }

    /**
     * A private helper method to get filtered service requests based on role.
     */
    private function getFilteredServiceRequests(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        
        $query = ServiceRequest::query();
        
        // Filter by date range (based on when it was created/submitted)
        $query->whereBetween('created_at', [$startDate, $endDate]);
        
        // Apply optional filters
        if ($request->filled('type')) { $query->where('request_type', $request->type); }
        if ($request->filled('status')) { $query->where('status', $request->status); }
        
        // Apply role-based filter
        if (Auth::user()->role !== 'Admin') {
            $user = Auth::user();
            $query->where(fn($q) => $q->where('requester_id', $user->id)->orWhere('technician_id', $user->id));
        }
        
        return $query->with('requester', 'technician')->latest()->get();
    }
}