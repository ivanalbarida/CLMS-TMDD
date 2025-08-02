<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Maintenance Log (ID: {{ $maintenance->id }})
        </h2>
    </x-slot>

    @php
        $isCompleted = ($maintenance->status == 'Completed');
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('maintenance.update', $maintenance->id) }}">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="p-4 mx-6 mt-6 bg-red-100 border-l-4 border-red-500 text-red-700">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="p-6 space-y-6" x-data="{ selectedLab: '{{ $maintenance->equipment->first()->lab_id ?? '' }}' }">
                        
                        <!-- Step 1: Lab Selector -->
                        <div>
                            <label for="lab_selector" class="block font-medium text-sm text-gray-700">1. Select a Lab</label>
                            <select id="lab_selector" x-model="selectedLab" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" {{ $isCompleted ? 'disabled' : '' }}>
                                <option value="">-- Select Lab --</option>
                                @foreach($labs as $lab)
                                    <option value="{{ $lab->id }}" @if($maintenance->equipment->first()->lab_id == $lab->id) selected @endif>{{ $lab->lab_name }} - {{ $lab->building_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Equipment Selector -->
                        <div x-show="selectedLab" x-cloak>
                            <label class="block font-medium text-sm text-gray-700">2. Select Equipment</label>
                            <div class="mt-2 border rounded-md max-h-60 overflow-y-auto p-4">
                                @foreach($labs as $lab)
                                    <div x-show="selectedLab == {{ $lab->id }}" class="space-y-2">
                                        @foreach($lab->equipment as $pc)
                                            <label class="flex items-center ml-6">
                                                <input type="checkbox" name="equipment_ids[]" value="{{ $pc->id }}" class="rounded" @if(in_array($pc->id, $assignedEquipmentIds)) checked @endif {{ $isCompleted ? 'disabled' : '' }}>
                                                <span class="ml-2">{{ $pc->tag_number }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 3: Log Details -->
                        <div class="border-t pt-6 space-y-6">
                            <h3 class="block font-medium text-lg text-gray-800">3. Provide Log Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label for="category" class="block font-medium text-sm text-gray-700">Category*</label>
                                    <select id="category" name="category" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" {{ $isCompleted ? 'disabled' : '' }}>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" @selected(old('category', $maintenance->category) == $category)>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="user_id" class="block font-medium text-sm text-gray-700">Assigned Technician</label>
                                    
                                    @if(Auth::user()->role === 'Admin' && !$isCompleted)
                                        <select id="user_id" name="user_id" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                            @foreach($technicians as $tech)
                                                <option value="{{ $tech->id }}" @selected(old('user_id', $maintenance->user_id) == $tech->id)>{{ $tech->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="user_id" value="{{ $maintenance->user_id }}">
                                        <input type="text" value="{{ $maintenance->user->name ?? 'N/A' }}" disabled 
                                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-100">
                                    @endif
                                </div>
                                <div>
                                    <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                                    <select id="status" name="status" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" {{ $isCompleted ? 'disabled' : '' }}>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" @selected(old('status', $maintenance->status) == $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="date_started" class="block font-medium text-sm text-gray-700">Date Started</label>
                                    <input type="date" id="date_started" name="date_started" value="{{ old('date_started', $maintenance->date_started ? $maintenance->date_started->format('Y-m-d') : '') }}" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" {{ $isCompleted ? 'disabled' : '' }}>
                                </div>
                            </div>

                            @if($maintenance->type == 'Preventive')
                                <div class="mt-4">
                                    <label for="scheduled_for" class="block font-medium text-sm text-gray-700">Scheduled For Date</label>
                                    <input type="date" id="scheduled_for" name="scheduled_for" 
                                        value="{{ old('scheduled_for', $maintenance->scheduled_for ? $maintenance->scheduled_for->format('Y-m-d') : '') }}" 
                                        class="block mt-1 w-full md:w-1/2 border-gray-300 rounded-md shadow-sm" {{ $isCompleted ? 'disabled' : '' }}>
                                </div>
                            @endif

                            <div>
                                <label for="issue_description" class="block font-medium text-sm text-gray-700">Issue Description / PM Tasks</label>
                                <textarea id="issue_description" name="issue_description" rows="4" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" {{ $isCompleted ? 'disabled' : '' }}>{{ old('issue_description', $maintenance->issue_description) }}</textarea>
                            </div>
                            <div>
                                <label for="action_taken" class="block font-medium text-sm text-gray-700">Action Taken</label>
                                <textarea id="action_taken" name="action_taken" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" {{ $isCompleted ? 'disabled' : '' }}>{{ old('action_taken', $maintenance->action_taken) }}</textarea>
                            </div>
                        </div>
                    </div>

                   <div class="p-6 bg-gray-50 flex items-center justify-end space-x-4">
                        <a href="{{ route('maintenance.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>

                        @if ($isCompleted)
                            <p class="text-sm font-semibold text-green-600">This task was completed on {{ \Carbon\Carbon::parse($maintenance->date_completed)->format('M d, Y') }}.</p>
                        @else
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Update Details
                            </button>
                            <a href="{{ route('maintenance.complete', $maintenance->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500"
                               onclick="return confirm('Please ensure all notes in \'Action Taken\' are saved by clicking \'Update Details\' first. Proceed to mark as complete?')">
                                Mark as Complete
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>