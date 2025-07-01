<?php

use App\Http\Controllers\LabController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index']) 
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Announcement routes accessible by everyone
Route::get('/announcements', [AnnouncementController::class, 'index'])
    ->name('announcements.index');

Route::resource('labs', LabController::class)
    ->middleware(['auth', 'verified']);
require __DIR__.'/auth.php';

Route::resource('equipment', EquipmentController::class)
    ->middleware(['auth', 'verified']);

Route::get('/labs/{lab}/equipment', [EquipmentController::class, 'showByLab'])->name('equipment.showByLab')
    ->middleware(['auth', 'verified']);

Route::resource('maintenance', MaintenanceController::class)
    ->middleware(['auth', 'verified']);

// CSV Import Routes (place them inside the authenticated group)
Route::get('/import', [CsvImportController::class, 'show'])->name('import.show')->middleware(['auth', 'verified']);
Route::post('/import', [CsvImportController::class, 'store'])->name('import.store')->middleware(['auth', 'verified']);

    // Announcement routes accessible ONLY by admins
Route::middleware('is.admin')->group(function () {
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::resource('users', UserController::class);
});