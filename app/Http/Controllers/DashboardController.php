<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Stats Overview ---
        $stats = [
            'total_equipment' => Equipment::count(),
            'working_equipment' => Equipment::where('status', 'Working')->count(),
            'for_repair' => Equipment::where('status', 'For Repair')->count(),
            
            'pending_maintenance' => MaintenanceRecord::where('status', '!=', 'Completed')->count(),
        ];

        // --- Recent Activities ---
        $recentActivities = MaintenanceRecord::where('type', 'Corrective')
                            ->whereIn('status', ['Pending', 'In Progress'])
                            ->with('equipment', 'user')
                            ->latest('date_reported')
                            ->limit(5)
                            ->get();
        
        // --- Announcements ---
        $announcements = \App\Models\Announcement::latest()->limit(3)->get();

        // --- Upcoming Preventive Maintenance ---
        $upcomingPM = MaintenanceRecord::where('type', 'Preventive')
                                ->where('status', 'Pending')
                                ->whereNotNull('scheduled_for')
                                ->where('scheduled_for', '>=', now()->toDateString())
                                ->orderBy('scheduled_for', 'asc')
                                ->with(['equipment.lab', 'user'])
                                ->limit(5)
                                ->get();
        
        // Pass all the correct data to the view
        return view('dashboard', compact('stats', 'recentActivities', 'announcements', 'upcomingPM'));
    }
}