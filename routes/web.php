<?php

use App\Http\Controllers\LabController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index']) 
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('labs', LabController::class)
    ->middleware(['auth', 'verified']);
require __DIR__.'/auth.php';

Route::resource('equipment', EquipmentController::class)
    ->middleware(['auth', 'verified']);

Route::resource('maintenance', MaintenanceController::class)
    ->middleware(['auth', 'verified']);