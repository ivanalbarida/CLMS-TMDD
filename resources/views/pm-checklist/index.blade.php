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
                                // Check if the current task's ID is in the completed list from the controller
                                $isComplete = in_array($task->id, $completedTaskIds);
                            @endphp
                            <label class="flex items-center p-3 rounded-md transition {{ $isComplete ? 'bg-green-100 text-gray-500' : 'bg-gray-50 hover:bg-gray-100' }}">
                                <input type="checkbox"
                                    @change="toggleCompletion($event, {{ $task->id }}, '{{ $today->format('Y-m-d') }}')"
                                    class="h-5 w-5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    {{ $isComplete ? 'checked disabled' : '' }}
                                >
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
    
    <script>
        function pmChecklist() {
            return {
                selectedLabId: '{{ $selectedLabId ?? '' }}',
                
                changeLab(labId) {
                    let url = `{{ route('pm-checklist.index') }}`;
                    if (labId) {
                        url += `?lab_id=${labId}`;
                    } else {
                        // If no lab is selected, go back to the base URL
                        window.location.href = url;
                    }
                    window.location.href = url;
                },

                toggleCompletion(event, taskId, date) {
                const checkbox = event.currentTarget;
                const isComplete = checkbox.checked;
                const label = checkbox.closest('label');

                // Give visual feedback that something is happening
                label.classList.add('opacity-50');
                // Temporarily disable to prevent double clicks
                checkbox.disabled = true;

                fetch('{{ route("pm-checklist.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        pm_task_id: taskId,
                        lab_id: this.selectedLabId,
                        date: date,
                        is_complete: isComplete
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Server error');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // SUCCESS: Re-enable the checkbox so it can be changed again
                        checkbox.disabled = false;
                    } else {
                        throw new Error('API returned success:false');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. The page will now reload to ensure data consistency.');
                    // On a critical error, reloading the page is the safest way
                    // to ensure the UI matches the database state.
                    window.location.reload();
                })
                .finally(() => {
                    // ALWAYS remove the loading state after the request is done
                    label.classList.remove('opacity-50');
                });
            }
            }
        }
    </script>
</x-app-layout>