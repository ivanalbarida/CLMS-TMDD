<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Models\Announcement;
use App\Models\PmTask;
use App\Models\PmTaskCompletion;
use Carbon\Carbon;
use App\Models\ServiceRequest;
use App\Models\Lab;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $assignedLabIds = [];
        if ($user->role !== 'Admin') {
            $assignedLabIds = $user->labs()->pluck('labs.id')->toArray();
        }

        // --- Build Role-Aware Queries ---
        $equipmentQuery = ($user->role === 'Admin') ? Equipment::query() : Equipment::whereIn('lab_id', $assignedLabIds);
        $maintenanceQuery = ($user->role === 'Admin') ? MaintenanceRecord::query() : MaintenanceRecord::whereHas('equipment', fn($q) => $q->whereIn('lab_id', $assignedLabIds));
        $serviceRequestQuery = ($user->role === 'Admin') ? \App\Models\ServiceRequest::query() : \App\Models\ServiceRequest::where(fn($q) => $q->where('requester_id', $user->id)->orWhere('technician_id', $user->id));

        // --- Calculate Stats ---
        $stats = [
            'total_equipment' => (clone $equipmentQuery)->count(),
            'for_repair' => (clone $equipmentQuery)->where('status', 'For Repair')->count(),
            'open_service_requests' => (clone $serviceRequestQuery)->whereNotIn('status', ['Completed', 'Rejected'])->count(),
        ];

        // --- Accurate PM Count ---
        $today = Carbon::today();
        $labsForPM = ($user->role === 'Admin') ? Lab::all() : Lab::whereIn('id', $assignedLabIds)->get();
        $masterTasks = PmTask::where('is_active', true)->get();
        $pendingPmCount = 0;

        foreach ($labsForPM as $lab) {
            $completions = PmTaskCompletion::where('lab_id', $lab->id)->whereYear('completed_at', $today->year)->get();
            foreach ($masterTasks as $task) {
                $isCompleted = false;
                $startDate = null;

                switch ($task->frequency) {
                    case 'Daily': $startDate = $today->copy(); break;
                    case 'Weekly': $startDate = $today->copy()->startOfWeek(); break;
                    case 'Monthly': $startDate = $today->copy()->startOfMonth(); break;
                    case 'Quarterly': $startDate = $today->copy()->startOfQuarter(); break;
                }

                if ($startDate) {
                    $isCompleted = $completions->contains(fn($c) => $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->gte($startDate));
                }
                if ($startDate && !$isCompleted) {
                    $pendingPmCount++;
                }
            }
        }
        $stats['pending_pm'] = $pendingPmCount;

        // --- WIDGETS DATA ---
        $openCorrective = (clone $maintenanceQuery)->where('type', 'Corrective')->where('status', '!=', 'Completed')->with('equipment.lab', 'user')->latest('date_reported')->limit(5)->get();
        $openPreventive = (clone $maintenanceQuery)->where('type', 'Preventive')->where('status', '!=', 'Completed')->with('equipment.lab', 'user')->latest('scheduled_for')->limit(5)->get();
        $announcements = Announcement::latest()->limit(5)->get();

        return view('dashboard', compact('stats', 'openCorrective', 'openPreventive', 'announcements'));
    }
}