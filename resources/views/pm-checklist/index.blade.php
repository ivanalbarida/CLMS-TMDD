<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preventive Maintenance Checklist') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Header: Lab Selector and Date -->
            <div class="md:flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-700">Scheduled Tasks for: {{ $today->format('F d, Y') }}</h3>
                
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <form id="lab-select-form" method="GET" action="{{ route('pm-checklist.index') }}" class="flex items-center">
                        <label for="lab_id" class="text-sm font-medium mr-2">Viewing For:</label>
                        <select name="lab_id" id="lab_id" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Choose a Lab --</option>
                            @foreach ($labs as $lab)
                                <option value="{{ $lab->id }}" @selected($selectedLabId == $lab->id)>
                                    {{ $lab->lab_name }} ({{ $lab->building_name }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('reports.pm.form') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Generate Report
                    </a>
                </div>
            </div>

            @if($selectedLabId)
            <!-- The Checklist Form -->
            <form method="POST" action="{{ route('pm-checklist.store') }}">
                @csrf
                <input type="hidden" name="lab_id" value="{{ $selectedLabId }}">
                <input type="hidden" name="completion_date" value="{{ $today->format('Y-m-d') }}">

                <div class="bg-white p-6 rounded-lg shadow space-y-6">
                    @forelse ($tasksDueToday as $frequency => $tasks)
                        <div>
                            <div class="flex justify-between items-center border-b pb-2 mb-3">
                                <h4 class="font-bold text-lg">{{ $frequency }} Tasks</h4>
                                <!-- CORRECTED: Use the full class path for Str::slug() -->
                                <button type="button" onclick="checkAll('{{ \Illuminate\Support\Str::slug($frequency) }}')" class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">Check All</button>
                            </div>
                            <div class="space-y-3">
                                @foreach($tasks as $task)
                                    @php
                                        $isComplete = in_array($task->id, $completedTaskIds);
                                    @endphp
                                    <label class="flex items-center p-3 rounded-md transition {{ $isComplete ? 'bg-green-100 text-gray-500' : 'bg-gray-50 hover:bg-gray-100' }}">
                                        <!-- CORRECTED: Use the full class path for Str::slug() -->
                                        <input type="checkbox" name="task_ids[]" value="{{ $task->id }}"
                                               class="h-5 w-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 checklist-{{ \Illuminate\Support\Str::slug($frequency) }}"
                                               {{ $isComplete ? 'checked' : '' }}>
                                        <span class="ml-3 text-sm">{{ $task->task_description }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8"><p class="text-gray-500">No tasks scheduled for today.</p></div>
                    @endforelse

                    <!-- Submit Button -->
                    <div class="border-t pt-6 text-right">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Submit Today's Checklist
                        </button>
                    </div>
                </div>
            </form>
            @else
            <!-- Message to select a lab -->
            <div class="text-center py-10 bg-white rounded-lg shadow">
                <p class="text-gray-600">Please select a lab to view today's checklist.</p>
            </div>
            @endif
        </div>
    </div>

    <script>
        function checkAll(frequency) {
            const checkboxes = document.querySelectorAll('.checklist-' + frequency);
            const shouldCheck = checkboxes.length > 0 ? !checkboxes[0].checked : false;
            checkboxes.forEach(checkbox => {
                checkbox.checked = shouldCheck;
            });
        }
    </script>
</x-app-layout>