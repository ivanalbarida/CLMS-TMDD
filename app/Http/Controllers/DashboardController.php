<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
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
        // Get the 5 most recently reported maintenance logs
        $recentActivities = MaintenanceRecord::with('equipment', 'user')
                            ->latest('date_reported')
                            ->limit(5)
                            ->get();
        
        // Pass the data to the view
        return view('dashboard', compact('stats', 'recentActivities'));
    }
}