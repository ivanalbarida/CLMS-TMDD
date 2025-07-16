<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Models\Announcement;
use App\Models\PmTask;
use App\Models\PmTaskCompletion;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // --- KEY METRIC CARDS ---
        $stats = [
            'for_repair' => Equipment::where('status', 'For Repair')->count(),
            'pending_corrective' => MaintenanceRecord::where('type', 'Corrective')->where('status', '!=', 'Completed')->count(),
        ];

        // --- PENDING PM TASKS LOGIC ---
        $today = Carbon::today();
        $allMasterTasks = PmTask::where('is_active', true)->get();

        // Determine which tasks are theoretically due today
        $dueTasks = $allMasterTasks->filter(function ($task) use ($today) {
            switch ($task->frequency) {
                case 'Daily': return true;
                case 'Weekly': return $today->isMonday();
                case 'Monthly': return $today->day == 1;
                case 'Quarterly': return $today->day == 1 && in_array($today->month, [1, 4, 7, 10]);
                case 'Annually': return $today->day == 1 && $today->month == 1;
                default: return false;
            }
        });

        // Get completions for ALL labs to calculate the total pending count
        // Note: This is a simplified count. A more complex query would be needed for a perfect "overdue" count.
        $completedTodayCount = PmTaskCompletion::whereIn('pm_task_id', $dueTasks->pluck('id'))
                               ->whereDate('completed_at', $today)
                               ->distinct('pm_task_id', 'lab_id') // Count each task+lab combo once
                               ->count();

        // Calculate total possible tasks for the day (due tasks * number of labs)
        $totalPossibleTasks = $dueTasks->count() * \App\Models\Lab::count();
        $stats['pending_pm'] = $totalPossibleTasks - $completedTodayCount;
        
        // --- OPEN CORRECTIVE MAINTENANCE WIDGET ---
        $openCorrective = MaintenanceRecord::where('type', 'Corrective')
                            ->where('status', '!=', 'Completed')
                            ->with('equipment.lab', 'user')
                            ->latest('date_reported')
                            ->limit(5)
                            ->get();

        // --- ANNOUNCEMENTS WIDGET ---
        $announcements = Announcement::latest()->limit(3)->get();

        return view('dashboard', compact('stats', 'openCorrective', 'announcements'));
    }
}