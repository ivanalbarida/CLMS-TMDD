<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lab;
use App\Models\PmTask;
use App\Models\PmTaskCompletion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreventiveChecklistController extends Controller
{
    public function index(Request $request)
    {
        // Get labs, filtered by user role
        $labsQuery = \App\Models\Lab::query();
        if (Auth::user()->role !== 'Admin') {
            $labsQuery->whereHas('users', fn($q) => $q->where('user_id', Auth::id()));
        }
        $labs = $labsQuery->orderBy('lab_name')->get();
        
        $today = Carbon::today();
        $selectedLabId = $request->input('lab_id');
        
        $tasksToShow = collect();
        $completedTaskIds = [];

        if ($selectedLabId) {
            // Get all active master tasks
            $allMasterTasks = PmTask::where('is_active', true)->get();

            // Get all completions for this lab for the current year (for efficiency)
            $completionsThisYear = PmTaskCompletion::where('lab_id', $selectedLabId)
                ->whereYear('completed_at', $today->year)
                ->get();

            // The main list of tasks to show includes ALL frequencies by default
            $tasksToShow = $allMasterTasks;

            // Now, get a list of which tasks are already completed for their respective periods
            $completedTaskIds = $tasksToShow->filter(function ($task) use ($completionsThisYear, $today) {
                $startDate = null;
                switch ($task->frequency) {
                    case 'Daily':     $startDate = $today->copy()->startOfDay(); break;
                    case 'Weekly':    $startDate = $today->copy()->startOfWeek(); break;
                    case 'Monthly':   $startDate = $today->copy()->startOfMonth(); break;
                    case 'End of Term': $startDate = $today->copy()->startOfQuarter(); break;
                    case 'Annually':  $startDate = $today->copy()->startOfYear(); break;
                }
                
                if ($startDate) {
                    // Check if a completion exists for this task WITHIN its current period
                    return $completionsThisYear->contains(function ($c) use ($task, $startDate) {
                        return $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->gte($startDate);
                    });
                }
                return false;
            })->pluck('id')->toArray();
        }

        // --- Sorting Logic ---
        $frequencyOrder = ['Daily', 'Weekly', 'Monthly', 'End of Term', 'Annually'];
        $sortedTasks = $tasksToShow->groupBy('frequency')
            ->sortBy(function ($tasks, $frequency) use ($frequencyOrder) {
                return array_search($frequency, $frequencyOrder);
            });

        return view('pm-checklist.index', [
            'labs' => $labs,
            'tasksDueToday' => $sortedTasks,
            'today' => $today,
            'completedTaskIds' => $completedTaskIds,
            'selectedLabId' => $selectedLabId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lab_id' => 'required|exists:labs,id',
            'completion_date' => 'required|date',
            'task_ids' => 'nullable|array', // The array of checked task IDs
            'task_ids.*' => 'exists:pm_tasks,id',
        ]);

        DB::transaction(function () use ($validated) {
            // First, delete any existing completions for this lab on this day
            // to prevent duplicates if the user re-submits.
            PmTaskCompletion::where('lab_id', $validated['lab_id'])
                ->whereDate('completed_at', $validated['completion_date'])
                ->delete();

            // If the user checked any boxes, loop through and create the completion records
            if (!empty($validated['task_ids'])) {
                foreach ($validated['task_ids'] as $taskId) {
                    PmTaskCompletion::create([
                        'pm_task_id' => $taskId,
                        'lab_id' => $validated['lab_id'],
                        'user_id' => Auth::id(),
                        'completed_at' => $validated['completion_date'],
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Checklist submitted successfully!');
    }
    
    public function toggleCompletion(Request $request)
    {
        //
    }
}