<?php

namespace App\Http\Controllers;

use App\Models\PmTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PmTaskController extends Controller
{
    public function index()
    {
        $tasks = PmTask::orderBy('category')->orderBy('frequency')->get();
        return view('pm-tasks.index', compact('tasks'));
    }

    public function create()
    {
        $categories = ['Hardware Maintenance', 'Software Maintenance', 'Network Maintenance', 'Security & Safety Maintenance', 'Environmental Maintenance', 'Documentation & Monitoring'];
        $frequencies = ['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annually'];
        return view('pm-tasks.create', compact('categories', 'frequencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array|min:1',
            'tasks.*.category' => 'required|string|max:255',
            'tasks.*.task_description' => 'required|string',
            'tasks.*.frequency' => 'required|in:Daily,Weekly,Monthly,Quarterly,Annually',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->tasks as $taskData) {

                if (empty($taskData['task_description'])) {
                    continue;
                }
                PmTask::create($taskData);
            }
        });

        return redirect()->route('pm-tasks.index')->with('success', 'PM Tasks added successfully.');
    }

    public function edit(PmTask $pmTask)
    {
        $categories = ['Hardware Maintenance', 'Software Maintenance', 'Network Maintenance', 'Security & Safety Maintenance', 'Environmental Maintenance', 'Documentation & Monitoring'];
        $frequencies = ['Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annually'];
        return view('pm-tasks.edit', compact('pmTask', 'categories', 'frequencies'));
    }

    public function update(Request $request, PmTask $pmTask)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'task_description' => 'required|string',
            'frequency' => 'required|in:Daily,Weekly,Monthly,Quarterly,Annually',
            'is_active' => 'boolean',
        ]);

        $pmTask->update($request->all());

        return redirect()->route('pm-tasks.index')->with('success', 'PM Task updated successfully.');
    }

    public function destroy(PmTask $pmTask)
    {
        $pmTask->delete();
        return redirect()->route('pm-tasks.index')->with('success', 'PM Task deleted successfully.');
    }
}