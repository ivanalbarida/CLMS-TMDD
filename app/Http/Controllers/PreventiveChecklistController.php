<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lab;
use App\Models\PmTask;
use App\Models\PmTaskCompletion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PreventiveChecklistController extends Controller
{
    public function index(Request $request)
    {
        $labs = Lab::orderBy('lab_name')->get();
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
    
    public function toggleCompletion(Request $request)
    {
        $validated = $request->validate([
            'pm_task_id' => 'required|exists:pm_tasks,id',
            'lab_id' => 'required|exists:labs,id',
            'date' => 'required|date_format:Y-m-d',
            'is_complete' => 'required|boolean',
        ]);

        // Define the attributes to find the record
        $attributes = [
            'pm_task_id' => $validated['pm_task_id'],
            'lab_id' => $validated['lab_id'],
            'completed_at' => $validated['date'],
        ];

        if ($validated['is_complete']) {
            // If the checkbox is checked, create the record if it doesn't exist.
            // Add the user_id to the values that get created.
            PmTaskCompletion::firstOrCreate($attributes, ['user_id' => Auth::id()]);

            // Also log this specific activity
            $task = PmTask::find($validated['pm_task_id']);
            $lab = Lab::find($validated['lab_id']);
            log_activity('COMPLETED_PM_TASK', $task, "Completed PM task '{$task->task_description}' for lab '{$lab->lab_name}'");

        } else {
            // If the checkbox is unchecked, find the specific record and delete it.
            PmTaskCompletion::where($attributes)->delete();

            // Also log the un-checking action
            $task = PmTask::find($validated['pm_task_id']);
            $lab = Lab::find($validated['lab_id']);
            log_activity('UNCHECKED_PM_TASK', $task, "Un-checked PM task '{$task->task_description}' for lab '{$lab->lab_name}'");
        }

        return response()->json(['success' => true]);
    }
}