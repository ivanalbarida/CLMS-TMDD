<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PmTaskController;
use App\Http\Controllers\PreventiveChecklistController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SoftwareItemController;
use App\Http\Controllers\SoftwareListController;
use App\Http\Controllers\SoftwareProfileController;

// 1. PUBLIC ROUTES
Route::get('/', fn() => redirect()->route('login'));
require __DIR__.'/auth.php';


// 2. AUTHENTICATED ROUTES (FOR ALL ROLES)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Core Pages
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Labs & Equipment (Full CRUD for all roles, permissions handled in controller/view)
    Route::resource('labs', LabController::class);
    Route::resource('equipment', EquipmentController::class);
    Route::get('/labs/{lab}/equipment', [EquipmentController::class, 'showByLab'])->name('equipment.showByLab');
    
    // Maintenance (Full CRUD for all roles)
    Route::get('/maintenance/schedule', [MaintenanceController::class, 'schedule'])->name('maintenance.schedule');
    Route::get('/maintenance/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
    Route::resource('maintenance', MaintenanceController::class);
    
    // Service Requests (Full CRUD for all roles)
    Route::post('/service-requests/{serviceRequest}/verify', [ServiceRequestController::class, 'verify'])->name('service-requests.verify');
    Route::resource('service-requests', ServiceRequestController::class);
    
    // Checklists & Software Lists
    Route::get('/preventive-checklist', [PreventiveChecklistController::class, 'index'])->name('pm-checklist.index');
    Route::post('/pm-checklist/toggle', [PreventiveChecklistController::class, 'toggleCompletion'])->name('pm-checklist.toggle');
    Route::get('/software-list', [SoftwareListController::class, 'index'])->name('software-list.index');
    
    // Reporting (Accessible to all roles, filtered in controller)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/lab/{lab}/form', [ReportController::class, 'showLabReportForm'])->name('reports.lab.form');
    Route::post('/reports/lab/{lab}', [ReportController::class, 'generateLabReport'])->name('reports.lab');
    

    // Bulk Import
    Route::get('/import', [CsvImportController::class, 'show'])->name('import.show');
    Route::post('/import', [CsvImportController::class, 'store'])->name('import.store');


    // 3. ADMIN-ONLY ROUTES
    Route::middleware('is.admin')->group(function () {
        
        // Content Management
        Route::resource('announcements', AnnouncementController::class);
        
        // System Configuration
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
        Route::resource('pm-tasks', PmTaskController::class);
        Route::resource('software-items', SoftwareItemController::class);
        Route::resource('software-profiles', SoftwareProfileController::class);
    });
});