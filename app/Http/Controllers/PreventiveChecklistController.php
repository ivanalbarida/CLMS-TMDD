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
        $tasksToShow = collect();
        $completions = []; // This will now only hold IDs of completed tasks for the view
        $selectedLabId = $request->input('lab_id');

        if ($selectedLabId) {
            // Step 1: Determine which frequencies are active today
            $activeFrequencies = ['Daily'];
            if ($today->isMonday()) {
                $activeFrequencies[] = 'Weekly';
            }
            if ($today->day == 1) {
                $activeFrequencies[] = 'Monthly';
            }
            if ($today->day == 1 && in_array($today->month, [1, 4, 7, 10])) {
                $activeFrequencies[] = 'Quarterly';
            }
            if ($today->day == 1 && $today->month == 1) {
                $activeFrequencies[] = 'Annually';
            }

            // Step 2: Get all master tasks that are due today or are overdue
            $dueMasterTasks = PmTask::where('is_active', true)
                                ->whereIn('frequency', $activeFrequencies)
                                ->get();

            // Step 3: Get all completions for the selected lab within the current periods
            $completionsThisWeek = PmTaskCompletion::where('lab_id', $selectedLabId)
                                    ->where('pm_task_id', 'like', '%Weekly%') // Simplified check
                                    ->whereBetween('completed_at', [$today->startOfWeek(), $today->endOfWeek()])
                                    ->pluck('pm_task_id')->toArray();
            
            $completionsThisMonth = PmTaskCompletion::where('lab_id', $selectedLabId)
                                    ->whereIn('pm_task_id', $dueMasterTasks->where('frequency','Monthly')->pluck('id'))
                                    ->whereBetween('completed_at', [$today->startOfMonth(), $today->endOfMonth()])
                                    ->pluck('pm_task_id')->toArray();

            $completionsThisQuarter = PmTaskCompletion::where('lab_id', $selectedLabId)
                                    ->whereIn('pm_task_id', $dueMasterTasks->where('frequency','Quarterly')->pluck('id'))
                                    ->whereBetween('completed_at', [$today->startOfQuarter(), $today->endOfQuarter()])
                                    ->pluck('pm_task_id')->toArray();

            // Daily completions are for today only
            $completionsToday = PmTaskCompletion::where('lab_id', $selectedLabId)
                                    ->whereDate('completed_at', $today)
                                    ->pluck('pm_task_id')->toArray();
                                    
            $allCompletions = array_unique(array_merge($completionsThisWeek, $completionsThisMonth, $completionsThisQuarter, $completionsToday));


            // Step 4: The final list is the due tasks minus the completed tasks
            $tasksToShow = $dueMasterTasks->whereNotIn('id', $allCompletions);
            $completions = $allCompletions; // For the view to correctly pre-check items
        }

        return view('pm-checklist.index', [
            'labs' => $labs,
            'tasksDueToday' => $tasksToShow->groupBy('frequency'),
            'today' => $today,
            'completions' => $completions,
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

        if ($validated['is_complete']) {
            // If the checkbox is checked, create or find the completion record.
            PmTaskCompletion::firstOrCreate(
                [
                    'pm_task_id' => $validated['pm_task_id'],
                    'lab_id' => $validated['lab_id'],
                    'completed_at' => $validated['date'],
                ],
                [   
                    'user_id' => Auth::id(), // Only set the user_id on creation
                    'remarks' => $request->remarks,
                ]
            );
        } else {
            // If the checkbox is unchecked, delete the record.
            PmTaskCompletion::where('pm_task_id', $validated['pm_task_id'])
                ->where('lab_id', $validated['lab_id'])
                ->where('completed_at', $validated['date'])
                ->delete();
        }

        return response()->json(['success' => true]);
    }
}