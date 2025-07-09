<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SoftwareChecklistController;
use App\Http\Controllers\PmTaskController;
use App\Http\Controllers\PreventiveChecklistController;

// 1. Publicly Accessible Routes
Route::get('/', function () {
    return view('welcome');
});

require __DIR__.'/auth.php';

// 2. ALL Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Software Checklist (All users)
    Route::resource('software-checklist', SoftwareChecklistController::class);

    // Maintenance Routes
    // Specific route is defined BEFORE the resource route
    Route::get('/maintenance/schedule', [MaintenanceController::class, 'schedule'])->name('maintenance.schedule');
    Route::resource('maintenance', MaintenanceController::class);

    // Labs and Equipment Routes
    Route::resource('labs', LabController::class);
    Route::get('/labs/{lab}/equipment', [EquipmentController::class, 'showByLab'])->name('equipment.showByLab');
    Route::resource('equipment', EquipmentController::class);

    Route::get('/maintenance/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');

    Route::get('/preventive-checklist', [PreventiveChecklistController::class, 'index'])->name('pm-checklist.index');

    Route::middleware('auth:sanctum')->post('/pm-checklist/toggle', [PreventiveChecklistController::class, 'toggleCompletion']);

    Route::post('/pm-checklist/toggle', [PreventiveChecklistController::class, 'toggleCompletion'])->name('pm-checklist.toggle');

    Route::resource('users', UserController::class);
    Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');

    // 3. Admin-Only Routes
    Route::middleware('is.admin')->group(function () {
        
        // CSV Import
        Route::get('/import', [CsvImportController::class, 'show'])->name('import.show');
        Route::post('/import', [CsvImportController::class, 'store'])->name('import.store');

        // Announcements Management
        Route::resource('announcements', AnnouncementController::class);

        // User Management
        Route::resource('users', UserController::class);

        // For the more complex academic software feature (if we re-add it)
        // Route::resource('programs', ProgramController::class);
        // Route::resource('software', SoftwareController::class);
        // Route::resource('software-sets', SoftwareSetController::class);
        Route::resource('pm-tasks', PmTaskController::class);
    });
});