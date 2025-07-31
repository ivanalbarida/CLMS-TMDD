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
        $labsQuery = Lab::query();
        if (Auth::user()->role !== 'Admin') {
            $labsQuery->whereHas('users', fn($q) => $q->where('user_id', Auth::id()));
        }
        $labs = $labsQuery->orderBy('lab_name')->get();
        $today = Carbon::today();
        $selectedLabId = $request->input('lab_id');
        
        $tasksByFrequency = [
            'Daily' => collect(),
            'Weekly' => collect(),
            'Monthly' => collect(),
            'Quarterly' => collect(),
            'Annually' => collect(),
        ];
        $completedTaskIds = [];

        if ($selectedLabId) {
            // Get all active master tasks
            $allMasterTasks = PmTask::where('is_active', true)->get();

            // Get all completions for this lab to check against
            $completionsThisYear = PmTaskCompletion::where('lab_id', $selectedLabId)
                ->whereYear('completed_at', $today->year)
                ->get();

            // Manually build the list for each frequency
            
            // --- DAILY ---
            $dailyTasks = $allMasterTasks->where('frequency', 'Daily');
            foreach ($dailyTasks as $task) {
                $tasksByFrequency['Daily']->push($task);
                $isComplete = $completionsThisYear->contains(function ($c) use ($task, $today) {
                    return $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->isSameDay($today);
                });
                if ($isComplete) { $completedTaskIds[] = $task->id; }
            }

            // --- WEEKLY ---
            $weeklyTasks = $allMasterTasks->where('frequency', 'Weekly');
            foreach ($weeklyTasks as $task) {
                $tasksByFrequency['Weekly']->push($task);
                $isComplete = $completionsThisYear->contains(function ($c) use ($task, $today) {
                    return $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->isSameWeek($today);
                });
                if ($isComplete) { $completedTaskIds[] = $task->id; }
            }

            // --- MONTHLY ---
            $monthlyTasks = $allMasterTasks->where('frequency', 'Monthly');
            foreach ($monthlyTasks as $task) {
                $tasksByFrequency['Monthly']->push($task);
                $isComplete = $completionsThisYear->contains(function ($c) use ($task, $today) {
                    return $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->isSameMonth($today);
                });
                if ($isComplete) { $completedTaskIds[] = $task->id; }
            }
            
            // --- QUARTERLY ---
            $quarterlyTasks = $allMasterTasks->where('frequency', 'Quarterly');
            foreach ($quarterlyTasks as $task) {
                $tasksByFrequency['Quarterly']->push($task);
                $isComplete = $completionsThisYear->contains(function ($c) use ($task, $today) {
                    return $c->pm_task_id == $task->id && Carbon::parse($c->completed_at)->isSameQuarter($today);
                });
                if ($isComplete) { $completedTaskIds[] = $task->id; }
            }
        }
        
        // Remove empty frequency groups before sending to the view
        $sortedTasks = collect($tasksByFrequency)->filter(fn($tasks) => $tasks->isNotEmpty());

        return view('pm-checklist.index', [
            'labs' => $labs,
            'tasksDueToday' => $sortedTasks,
            'today' => $today,
            'completedTaskIds' => array_unique($completedTaskIds),
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