<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // We'll use this for counting

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

        // --- Pass ALL data to the view in ONE return statement ---
        return view('dashboard', compact('stats', 'recentActivities', 'announcements'));
    }
}