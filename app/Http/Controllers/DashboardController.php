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
            'pending_maintenance' => MaintenanceRecord::where('status', 'Pending')->count(),
        ];

        // --- Recent Activities ---
        $recentActivities = MaintenanceRecord::with('equipment', 'user')
                            ->latest('date_reported')
                            ->limit(5)
                            ->get();
        
        // --- Announcements ---
        $announcements = Announcement::latest()->limit(3)->get();

        // --- Preventive Maintenance ---
        $upcomingPM = MaintenanceRecord::where('type', 'Preventive')
                                ->where('status', 'Pending')
                                ->where('scheduled_for', '>=', now()->toDateString())
                                ->orderBy('scheduled_for', 'asc')
                                ->with('equipment') // Eager load equipment to prevent extra queries
                                ->limit(5)
                                ->get();
        
        // Pass ALL the data to the view, including the new variable
        return view('dashboard', compact('stats', 'recentActivities', 'announcements', 'upcomingPM'));
    }
}