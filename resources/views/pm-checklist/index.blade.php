<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preventive Maintenance Checklist') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="pmChecklist()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="md:flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-700">Pending Tasks for: {{ $today->format('F d, Y') }}</h3>
                <div>
                    <label for="lab_id" class="text-sm font-medium">Viewing For:</label>
                    <select name="lab_id" id="lab_id" @change="changeLab($event.target.value)" class="ml-2 rounded-md ...">
                        <option value="">-- Choose a Lab --</option>
                        @foreach ($labs as $lab)
                            <option value="{{ $lab->id }}" @selected($selectedLabId == $lab->id)>
                                {{ $lab->lab_name }} ({{ $lab->building_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- The Checklist -->
            <div x-show="selectedLabId" x-cloak class="bg-white p-6 rounded-lg shadow space-y-6">
                @forelse ($tasksDueToday as $frequency => $tasks)
                    <div>
                        <h4 class="font-bold text-lg border-b pb-2 mb-3">{{ $frequency }} Tasks</h4>
                        <div class="space-y-3">
                            @foreach($tasks as $task)
                                @php
                                    // The check for completion is now just for visual state, logic is in controller
                                    $isComplete = in_array($task->id, $completions);
                                @endphp
                                <label class="flex items-center p-3 rounded-md transition bg-gray-50 hover:bg-gray-100">
                                    <input type="checkbox" @change="toggleCompletion({{ $task->id }}, '{{ $today->format('Y-m-d') }}', $event.target.checked)"
                                           class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-3 text-sm">{{ $task->task_description }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-green-600 font-semibold">All tasks for today are complete!</p>
                        <p class="text-gray-500 text-sm">Or no tasks were scheduled for today.</p>
                    </div>
                @endforelse
            </div>

            <!-- Message to select a lab -->
            <div x-show="!selectedLabId" class="text-center py-10 bg-white rounded-lg shadow">
                <p class="text-gray-600">Please select a lab to view today's pending checklist.</p>
            </div>
        </div>
    </div>
    
    <!-- The JavaScript part remains the same -->
    <script>
        function pmChecklist() { ... }
    </script>
</x-app-layout>