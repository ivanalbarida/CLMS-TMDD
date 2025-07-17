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
            'for_repair' => \App\Models\Equipment::where('status', 'For Repair')->count(),
            'total_equipment' => Equipment::count(),
            'pending_corrective' => \App\Models\MaintenanceRecord::where('type', 'Corrective')->where('status', '!=', 'Completed')->count(),
        ];

        // --- PENDING PM TASKS LOGIC ---
        $today = \Carbon\Carbon::today();
        $allLabs = \App\Models\Lab::all();
        $pendingPmCount = 0;
        
        $allMasterTasks = \App\Models\PmTask::where('is_active', true)->get();

        foreach ($allLabs as $lab) {
            $completionsThisYear = \App\Models\PmTaskCompletion::where('lab_id', $lab->id)
                ->whereYear('completed_at', $today->year)
                ->get();
            
            foreach ($allMasterTasks as $task) {
                $isCompletedInPeriod = false;
                $startDate = null;
                $isDue = false;

                switch ($task->frequency) {
                    case 'Daily': $isDue = true; $startDate = $today->copy(); break;
                    case 'Weekly': $isDue = true; $startDate = $today->copy()->startOfWeek(); break;
                    case 'Monthly': $isDue = true; $startDate = $today->copy()->startOfMonth(); break;
                    case 'Quarterly': $isDue = true; $startDate = $today->copy()->startOfQuarter(); break;
                }

                if ($isDue) {
                    $isCompletedInPeriod = $completionsThisYear->contains(function ($c) use ($task, $startDate) {
                        return $c->pm_task_id == $task->id && \Carbon\Carbon::parse($c->completed_at)->gte($startDate);
                    });
                }
                
                if ($isDue && !$isCompletedInPeriod) {
                    $pendingPmCount++;
                }
            }
        }
        
        $stats['pending_pm'] = $pendingPmCount;
        // --- END OF NEW LOGIC ---
        
        $openCorrective = MaintenanceRecord::where('type', 'Corrective')
                    ->where('status', '!=', 'Completed')
                    ->with('equipment.lab', 'user')->latest('date_reported')->limit(10)->get(); 

        $openPreventive = MaintenanceRecord::where('type', 'Preventive')
                            ->where('status', '!=', 'Completed')
                            ->with('equipment.lab', 'user')->latest('scheduled_for')->limit(10)->get();
        
        $announcements = \App\Models\Announcement::latest()->limit(7)->get();

        return view('dashboard', compact('stats', 'openCorrective', 'openPreventive', 'announcements'));
    }
}